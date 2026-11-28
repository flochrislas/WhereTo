<?php

namespace App\Services;

use App\Enums\OrderBy;
use App\Enums\TagOperator;
use App\Models\Bar;
use App\Models\Karaoke;
use App\Models\Place;
use App\Models\Restaurant;
use App\Models\Store;
use App\Support\LocaleUrl;
use App\Support\Standalone;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use LogicException;
use RuntimeException;

/**
 * Writes the site as files for the stand-alone Android app (doc/16-standalone-apk-plan.md): every page and fragment
 * that does not depend on the visitor, exactly as the application answers it, plus the data whereto-local.js needs
 * to compute the rest (the results and the suggestion) in the phone.
 *
 * The pages are asked from the application itself, through its HTTP kernel (no web server), so a change to a view
 * reaches the app with the next export. It reads whatever database is the default connection, and never migrates
 * nor seeds: "php artisan app:export-static" rebuilds the export database first (App\Support\ExportGuard).
 *
 * The one class of app/Services that builds HTTP requests: requests to the application, never the visitor's
 * (the architecture tests make an exception for it).
 */
class StaticExporter
{
    /**
     * The kinds of place: the name in the URLs (/restaurants) and the files, the model and the search
     *
     * @var array<string, array{class-string<Model&Place>, class-string<PlaceSearch<covariant Model&Place>>}>
     */
    public const KINDS = [
        'restaurants' => [Restaurant::class, RestaurantSearch::class],
        'stores' => [Store::class, StoreSearch::class],
        'bars' => [Bar::class, BarSearch::class],
        'karaokes' => [Karaoke::class, KaraokeSearch::class],
    ];

    /** The public files the pages load. Not index.php, sw.js (no service worker in the app) nor manifest.json */
    private const ASSET_DIRECTORIES = ['css', 'js', 'fonts', 'icons'];

    private const ASSET_FILES = ['favicon.ico'];

    /** How many files were written by the current export */
    private int $files = 0;

    /**
     * Prepare the run, before the database is rebuilt or anything is asked: the array cache and session, and the
     * stand-alone mode of the views (or the website's answers, for the reference of the parity tests: null).
     *
     * The array cache first of all: the seeders end with Cache::flush(), which must not empty the dev cache, and the
     * search caches its results forever, which must neither be read from an earlier database nor left behind.
     * The array session: thousands of internal requests must not write a session file each. Both are read when a
     * store is first used, and nothing uses one before this in a console command.
     */
    public static function prepare(?string $standaloneDate): void
    {
        config([
            'cache.default' => 'array',
            'session.driver' => 'array',
            'whereto.standalone' => $standaloneDate,
        ]);
    }

    /** Was prepare() called, so that the export may run */
    public static function isPrepared(): bool
    {
        return config('cache.default') === 'array' && config('session.driver') === 'array';
    }

    /**
     * Write the export into $dir, which must not exist or be empty.
     *
     * @param  callable(string): void|null  $report  told one line per kind
     * @return array{files: int, places: array<string, int>}
     */
    public function export(string $dir, ?callable $report = null): array
    {
        if (! self::isPrepared() || ! Standalone::active()) {
            throw new LogicException('StaticExporter::prepare() with an export date must run before export().');
        }
        $report ??= function (string $line): void {};
        $this->files = 0;
        File::ensureDirectoryExists($dir);

        $places = [];
        foreach (LocaleUrl::locales() as $locale) {
            foreach (['home', 'info', 'vip'] as $route) {
                $this->writePage($dir, LocaleUrl::route($route, [], false, $locale));
            }
        }
        foreach (self::KINDS as $kind => [$model]) {
            $ids = $model::query()->orderBy('id')->pluck('id')->all();
            foreach (LocaleUrl::locales() as $locale) {
                $this->writePage($dir, LocaleUrl::route("$kind.main", [], false, $locale));
                foreach ($ids as $id) {
                    $this->writePage($dir, LocaleUrl::route("$kind.details", $id, false, $locale));
                    $this->writePage($dir, LocaleUrl::route("$kind.details.single", $id, false, $locale));
                }
            }
            $places[$kind] = count($ids);
            $listed = $this->writeSearchData($dir, $kind);
            $report(sprintf('%-12s %4d places, %4d listed', $kind, count($ids), $listed));
        }
        $this->writeSuggestions($dir);
        $this->writeStrings($dir);
        $this->copyAssets($dir);
        $this->writeJson($dir.'/app-data/meta.json', ['exportDate' => Standalone::exportDate(), 'places' => $places]);
        $this->checkNoDebugbar($dir);

        return ['files' => $this->files, 'places' => $places];
    }

    /**
     * Ask the application for a path (with its query string) through the HTTP kernel, as a browser would:
     * the routes, their middleware (the language of the page) and the views all run.
     *
     * @return array{status: int, body: string}
     */
    public function dispatch(string $uri): array
    {
        $kernel = app(Kernel::class);
        $request = Request::create($uri, 'GET');
        $response = $kernel->handle($request);
        $body = (string) $response->getContent();
        $kernel->terminate($request, $response);

        return ['status' => $response->getStatusCode(), 'body' => $body];
    }

    /** The body of a path, which must answer 200 */
    private function fetch(string $uri): string
    {
        $answer = $this->dispatch($uri);
        if ($answer['status'] !== 200) {
            throw new RuntimeException("$uri answered {$answer['status']}, not 200.");
        }

        return $answer['body'];
    }

    /** A page or fragment at <dir>/<path>/index.html (the home page: <dir>/index.html), as the app looks for it */
    private function writePage(string $dir, string $path): void
    {
        $folder = trim($path, '/');
        $this->writeFile($dir.($folder === '' ? '' : '/'.$folder).'/index.html', $this->fetch($path));
    }

    /**
     * The data of one kind for whereto-local.js: the ids in the two orders the server would return them (byCost only
     * for a kind with a price), the tags and coordinates of each listed place, and the results fragment with every
     * row in each language. Returns the number of places listed (the open ones).
     */
    private function writeSearchData(string $dir, string $kind): int
    {
        $search = new (self::KINDS[$kind][1]);
        $byRatings = $search->find(null, TagOperator::And, OrderBy::Ratings);
        $places = [];
        foreach ($byRatings as $place) {
            $position = $place->position();
            $places[(string) $place->getAttribute('id')] = [
                // The English labels: the values the filter sends (data-tag)
                'tags' => $place->getAttribute('tags')->pluck('label')->values()->all(),
                'lat' => $position?->lat,
                'lon' => $position?->lon,
            ];
        }
        $data = ['byRatings' => $byRatings->map(fn ($place) => $place->getAttribute('id'))->values()->all()];
        if ($search->hasCost()) {
            $data['byCost'] = $search->find(null, TagOperator::And, OrderBy::Cost)->map(fn ($place) => $place->getAttribute('id'))->values()->all();
        }
        // An object even when empty: the script reads places[id]
        $data['places'] = (object) $places;
        $this->writeJson("$dir/app-data/$kind.json", $data);

        foreach (LocaleUrl::locales() as $locale) {
            $this->writeFile("$dir/app-data/$locale/$kind-results.html", $this->fetch(LocaleUrl::route("$kind.results", ['orderBy' => 'ratings'], false, $locale)));
        }

        return count($places);
    }

    /**
     * The sentence of each restaurant the suggestion may pick (the 100 best, as SuggestionController), in each language
     */
    private function writeSuggestions(string $dir): void
    {
        $candidates = (new RestaurantSearch)->find(null, TagOperator::And, OrderBy::Ratings, 100);
        $previous = app()->getLocale();
        try {
            foreach (LocaleUrl::locales() as $locale) {
                // The view translates with the current language: set it around the direct render
                app()->setLocale($locale);
                $sentences = [];
                foreach ($candidates as $place) {
                    $sentences[(string) $place->getAttribute('id')] = view('suggestions.suggestion-text', ['place' => $place])->render();
                }
                $this->writeJson("$dir/app-data/$locale/suggestions.json", (object) $sentences);
            }
        } finally {
            app()->setLocale($previous);
        }
    }

    /** The sentences of the results fragment that depend on the count, in each language, with their :count */
    private function writeStrings(string $dir): void
    {
        foreach (LocaleUrl::locales() as $locale) {
            $this->writeJson("$dir/app-data/$locale/strings.json", [
                'one' => __('1 result', [], $locale),
                'many' => __(':count results', [], $locale),
                'end' => __('End of :count amazing results', [], $locale),
            ]);
        }
    }

    private function copyAssets(string $dir): void
    {
        foreach (self::ASSET_DIRECTORIES as $folder) {
            File::copyDirectory(public_path($folder), "$dir/$folder");
            $this->files += count(File::allFiles("$dir/$folder"));
        }
        foreach (self::ASSET_FILES as $file) {
            File::copy(public_path($file), "$dir/$file");
            $this->files++;
        }
    }

    /** Debugbar turns itself off in the console; if it ever did not, its markup must not reach the app */
    private function checkNoDebugbar(string $dir): void
    {
        foreach (File::allFiles($dir) as $file) {
            if (in_array($file->getExtension(), ['html', 'json'], true) && str_contains($file->getContents(), 'phpdebugbar')) {
                throw new RuntimeException($file->getPathname().' carries the Debugbar: turn it off for the export.');
            }
        }
    }

    private function writeJson(string $path, mixed $data): void
    {
        $this->writeFile($path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
    }

    private function writeFile(string $path, string $content): void
    {
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);
        $this->files++;
    }
}
