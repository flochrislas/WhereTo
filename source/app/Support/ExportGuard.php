<?php

namespace App\Support;

use Illuminate\Database\ConnectionInterface;

/**
 * The check that keeps "php artisan app:export-static" away from any database but its own (doc/16-standalone-apk-plan.md).
 * The command drops every table of the database it exports from, so it must be impossible to aim it at the dev
 * database (or the test one): only a PostgreSQL database actually named whereto_export, reached through a
 * configuration read from the files (a cached configuration may describe another connection than the files do).
 * No exception for tests: the tests never go through the rebuild.
 */
final class ExportGuard
{
    /** The connection of config/database.php the export uses */
    public const CONNECTION = 'export';

    /** The only database the export may rebuild */
    public const DATABASE = 'whereto_export';

    /**
     * The decision, with no side effect: null when the export may rebuild this database, else why not
     *
     * @param  string  $driver  the driver of the connection, e.g. 'pgsql'
     * @param  string|null  $database  the name the server gives for the database (select current_database()), null when unknown
     * @param  bool  $configurationCached  is the configuration read from bootstrap/cache/config.php
     */
    public static function refusal(string $driver, ?string $database, bool $configurationCached): ?string
    {
        if ($configurationCached) {
            return 'The configuration is cached (php artisan config:cache): run "php artisan config:clear" first, so that the export connection is the one of config/database.php.';
        }
        if ($driver !== 'pgsql') {
            return "The export connection uses the driver \"$driver\": only PostgreSQL is allowed.";
        }
        if ($database !== self::DATABASE) {
            return 'The export connection reaches the database "'.($database ?? '?').'": only "'.self::DATABASE.'" may be rebuilt.';
        }

        return null;
    }

    /**
     * The same decision for a live connection: its driver, and the database its server says it is connected to.
     * Asked on the connection object that the rebuild then uses, so what is checked is what is rebuilt.
     */
    public static function check(ConnectionInterface $connection, bool $configurationCached): ?string
    {
        // getDriverName() is on Illuminate\Database\Connection, not on the interface
        $driver = method_exists($connection, 'getDriverName') ? (string) $connection->getDriverName() : 'unknown';
        // A cached configuration or another driver is refused without asking the server anything
        if ($configurationCached || $driver !== 'pgsql') {
            return self::refusal($driver, null, $configurationCached);
        }
        $name = $connection->scalar('select current_database()');

        return self::refusal($driver, is_string($name) ? $name : null, false);
    }
}
