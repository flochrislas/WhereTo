<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use App\Support\ExportGuard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * The guards of "php artisan app:export-static" (doc/16-standalone-apk-plan.md, Phase 3): it rebuilds a database, so
 * it must refuse to run anywhere but on whereto_export. The full run (rebuild, seeders, export) is exercised by
 * tools/android-app, never here: these tests only see the command refuse.
 */
class ExportStaticCommandTest extends TestCase
{
    use RefreshDatabase;

    private string $tmp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tmp = sys_get_temp_dir().'/whereto-export-command-'.bin2hex(random_bytes(4));
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->tmp);
        parent::tearDown();
    }

    public function test_a_directory_that_is_not_empty_is_refused_before_any_query(): void
    {
        File::ensureDirectoryExists($this->tmp);
        File::put($this->tmp.'/keep.txt', 'mine');
        $queries = 0;
        DB::listen(function () use (&$queries) {
            $queries++;
        });

        $this->artisan('app:export-static', ['dir' => $this->tmp])
            ->expectsOutputToContain('not an empty directory')
            ->assertFailed();

        $this->assertSame(0, $queries);
        $this->assertSame('mine', File::get($this->tmp.'/keep.txt'));
        // The run was not even prepared: the stand-alone mode is still off
        $this->assertNull(config('whereto.standalone'));
    }

    public function test_a_cached_configuration_is_refused(): void
    {
        // What Application::configurationIsCached() remembers once it looked for bootstrap/cache/config.php
        $this->app->instance('config_loaded_from_cache', true);
        $queries = 0;
        DB::listen(function () use (&$queries) {
            $queries++;
        });

        $this->artisan('app:export-static', ['dir' => $this->tmp])
            ->expectsOutputToContain('configuration is cached')
            ->assertFailed();

        $this->assertSame(0, $queries);
        $this->assertDirectoryDoesNotExist($this->tmp);
    }

    public function test_the_export_connection_aimed_at_another_database_is_refused_and_leaves_it_intact(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            $this->markTestSkipped('PostgreSQL only: the guard asks the server for current_database().');
        }
        $place = Restaurant::create(['name' => 'Still here', 'score_lunch' => 1]);
        // The export connection, with the name of the test database instead of whereto_export
        $testDatabase = DB::connection()->getDatabaseName();
        config(['database.connections.'.ExportGuard::CONNECTION => array_merge(
            config('database.connections.'.ExportGuard::CONNECTION),
            ['host' => config('database.connections.pgsql.host'), 'port' => config('database.connections.pgsql.port'), 'database' => $testDatabase],
        )]);
        DB::purge(ExportGuard::CONNECTION);

        $this->artisan('app:export-static', ['dir' => $this->tmp])
            ->expectsOutputToContain('only "whereto_export" may be rebuilt')
            ->assertFailed();

        $this->assertTrue(Schema::hasTable('restaurants'));
        $this->assertTrue(Schema::hasTable('migrations'));
        $this->assertSame('Still here', Restaurant::find($place->id)?->name);
        $this->assertDirectoryDoesNotExist($this->tmp);
    }

    public function test_the_reference_command_applies_the_same_guard(): void
    {
        File::ensureDirectoryExists($this->tmp);
        File::put($this->tmp.'/requests.json', '["/restaurants/results?orderBy=ratings"]');
        $this->app->instance('config_loaded_from_cache', true);

        $this->artisan('app:export-reference', ['requests' => $this->tmp.'/requests.json', 'output' => $this->tmp.'/reference.json'])
            ->expectsOutputToContain('configuration is cached')
            ->assertFailed();
        $this->assertFileDoesNotExist($this->tmp.'/reference.json');

        // Aimed at another kind of database: refused too
        $this->app->instance('config_loaded_from_cache', false);
        config(['database.connections.'.ExportGuard::CONNECTION => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']]);
        DB::purge(ExportGuard::CONNECTION);
        $this->artisan('app:export-reference', ['requests' => $this->tmp.'/requests.json', 'output' => $this->tmp.'/reference.json'])
            ->expectsOutputToContain('only PostgreSQL is allowed')
            ->assertFailed();
        $this->assertFileDoesNotExist($this->tmp.'/reference.json');
    }

    public function test_a_connection_that_is_not_postgresql_is_refused(): void
    {
        config(['database.connections.'.ExportGuard::CONNECTION => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']]);
        DB::purge(ExportGuard::CONNECTION);

        $this->artisan('app:export-static', ['dir' => $this->tmp])
            ->expectsOutputToContain('only PostgreSQL is allowed')
            ->assertFailed();
        $this->assertDirectoryDoesNotExist($this->tmp);
    }
}
