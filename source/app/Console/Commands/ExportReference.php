<?php

namespace App\Console\Commands;

use App\Services\StaticExporter;
use App\Support\ExportGuard;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use JsonException;
use Throwable;

/**
 * The answers of the website to a list of requests, from the export database (doc/16-standalone-apk-plan.md, Phase 6):
 * the reference the parity tests of tools/android-app compare the app's own results with. Run right after
 * app:export-static, on the same database, so the ids are the export's, and through the same application code.
 *
 * Not the stand-alone mode: this is what the site answers. But the array cache and session, as the export: the real
 * cache could hold search results of the dev database. The database is not rebuilt, only checked by the same guard.
 * Files in, file out (under storage/app/, which the dev container shares with the host): anything PHP prints must
 * not end up in the JSON.
 */
class ExportReference extends Command
{
    protected $signature = 'app:export-reference
                            {requests : A JSON file: a list of paths with their query string, e.g. ["/restaurants/results?orderBy=cost"]}
                            {output : The JSON file to write: {"<path>": {"status": 200, "body": "..."}}}';

    protected $description = 'Write the answers of the site to a list of requests, from the export database (parity tests of the Android app)';

    public function handle(StaticExporter $exporter): int
    {
        try {
            $paths = json_decode(File::get((string) $this->argument('requests')), true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->components->error('The requests file is not JSON: '.$e->getMessage());

            return self::FAILURE;
        }
        if (! is_array($paths) || ! array_is_list($paths)) {
            $this->components->error('The requests file must hold a list of paths.');

            return self::FAILURE;
        }

        StaticExporter::prepare(null);
        try {
            $refusal = ExportGuard::check(DB::connection(ExportGuard::CONNECTION), $this->laravel->configurationIsCached());
        } catch (Throwable $e) {
            $this->components->error('Cannot reach the export database ('.ExportGuard::DATABASE.'): '.$e->getMessage());

            return self::FAILURE;
        }
        if ($refusal !== null) {
            $this->components->error($refusal);

            return self::FAILURE;
        }
        DB::setDefaultConnection(ExportGuard::CONNECTION);

        $answers = [];
        foreach ($paths as $path) {
            $answers[(string) $path] = $exporter->dispatch((string) $path);
        }
        File::put((string) $this->argument('output'), json_encode($answers, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
        $this->info(count($answers).' answers written in '.$this->argument('output'));

        return self::SUCCESS;
    }
}
