<?php

namespace Tests\Unit;

use App\Support\ExportGuard;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * The decision that keeps "php artisan app:export-static" away from any database but whereto_export
 * (doc/16-standalone-apk-plan.md, Phase 3). No database here: the pure function only.
 */
class ExportGuardTest extends TestCase
{
    public function test_only_postgresql_whereto_export_with_a_fresh_configuration_is_allowed(): void
    {
        $this->assertNull(ExportGuard::refusal('pgsql', 'whereto_export', false));
    }

    /** @return array<string, array{string, ?string, bool}> */
    public static function refused(): array
    {
        return [
            'the dev database' => ['pgsql', 'whereto', false],
            'the test database' => ['pgsql', 'whereto_test', false],
            'an unknown database' => ['pgsql', null, false],
            'a near miss' => ['pgsql', 'whereto_export_old', false],
            'sqlite, whatever the name' => ['sqlite', 'whereto_export', false],
            'sqlite in memory' => ['sqlite', ':memory:', false],
            'mysql' => ['mysql', 'whereto_export', false],
            'a cached configuration' => ['pgsql', 'whereto_export', true],
        ];
    }

    #[DataProvider('refused')]
    public function test_everything_else_is_refused_with_a_reason(string $driver, ?string $database, bool $cached): void
    {
        $reason = ExportGuard::refusal($driver, $database, $cached);
        $this->assertNotNull($reason);
        $this->assertNotSame('', $reason);
    }
}
