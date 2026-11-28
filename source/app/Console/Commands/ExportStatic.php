<?php

namespace App\Console\Commands;

use App\Services\StaticExporter;
use App\Support\ExportGuard;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Throwable;

/**
 * Exports the site as files for the stand-alone Android app (doc/16-standalone-apk-plan.md), from a database
 * rebuilt for it: the export connection (database whereto_export), migrated and seeded from scratch, never the
 * dev database. tools/android-app/export.mjs runs it in the dev container.
 *
 * The order matters: the directory is checked before any database is touched, the run is prepared (array cache and
 * session) before the seeders flush the cache, and the guard checks the very connection that is then rebuilt.
 */
class ExportStatic extends Command
{
    protected $signature = 'app:export-static
                            {dir : The directory to write the export into; it must not exist or be empty}
                            {--seeder= : The root seeder class, instead of Database\Seeders\DatabaseSeeder}';

    protected $description = 'Rebuild the export database from the seeders and write the site as files for the stand-alone Android app';

    public function handle(StaticExporter $exporter): int
    {
        $dir = (string) $this->argument('dir');
        // 1. Nothing is overwritten, and this is decided before any database is involved
        if (File::exists($dir) && (! File::isDirectory($dir) || ! File::isEmptyDirectory($dir))) {
            $this->components->error("$dir exists and is not an empty directory.");

            return self::FAILURE;
        }

        // 2. The array cache and session, and the stand-alone mode of the views
        StaticExporter::prepare(now()->toIso8601String());

        // 3. The guard, on the connection that is rebuilt next
        try {
            $connection = DB::connection(ExportGuard::CONNECTION);
            $refusal = ExportGuard::check($connection, $this->laravel->configurationIsCached());
        } catch (Throwable $e) {
            $this->components->error('Cannot reach the export database ('.ExportGuard::DATABASE.'): '.$e->getMessage());
            $this->line('Create it once, from dev/: podman compose exec db psql -U wheretouser -d whereto -c "CREATE DATABASE '.ExportGuard::DATABASE.';"');
            $this->line('(tools/android-app/export.mjs does it for you.)');

            return self::FAILURE;
        }
        if ($refusal !== null) {
            $this->components->error($refusal);

            return self::FAILURE;
        }

        // 4. Rebuild it from the seeders; db:seed makes it the default connection while seeding, so the seeders and
        // the models write there. Then it stays the default for the export
        $status = $this->call('migrate:fresh', array_filter([
            '--database' => ExportGuard::CONNECTION,
            '--force' => true,
            '--seed' => true,
            '--seeder' => $this->option('seeder'),
        ]));
        if ($status !== self::SUCCESS) {
            $this->components->error('The export database could not be rebuilt.');

            return self::FAILURE;
        }
        DB::setDefaultConnection(ExportGuard::CONNECTION);

        // 5. The files
        $summary = $exporter->export($dir, fn (string $line) => $this->line($line));
        $this->info(sprintf('%d places, %d files written in %s', array_sum($summary['places']), $summary['files'], $dir));

        return self::SUCCESS;
    }
}
