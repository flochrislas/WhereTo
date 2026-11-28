<?php

namespace Tests\Architecture;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PlaceController;
use App\Models\Place;
use App\Support\LocaleUrl;
use App\Traits\Location;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\ViewController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use ReflectionClass;
use ReflectionEnum;
use Tests\TestCase;

/**
 * Architecture tests: rules about how the code is organised, not about what it does.
 * They fail early when a change breaks a convention of the project (doc/02-architecture.md).
 * Hand-written with reflection and file scans, so there is no extra package to learn.
 */
class ArchitectureTest extends TestCase
{
    /** Every class of a folder of app/, e.g. classesIn('Models') gives App\Models\Restaurant, ...
     * @return array<int, class-string>
     */
    private function classesIn(string $folder): array
    {
        $classes = [];
        foreach (File::allFiles(app_path($folder)) as $file) {
            // app/Http/Controllers/BarController.php -> App\Http\Controllers\BarController (sub-folders included)
            $relative = substr($file->getRelativePathname(), 0, -strlen('.php'));
            $classes[] = 'App\\'.str_replace('/', '\\', trim($folder.'/'.$relative, '/'));
        }
        sort($classes);

        return $classes;
    }

    /** The PHP source of a folder of app/ as [file name => content]
     * @return array<string, string>
     */
    private function sourcesIn(string $folder): array
    {
        $sources = [];
        foreach (File::allFiles(app_path($folder)) as $file) {
            $sources[$file->getRelativePathname()] = $file->getContents();
        }

        return $sources;
    }

    /** Assert that no file of the given sources contains the pattern (a regular expression) */
    private function assertNoneContains(string $pattern, array $sources, string $why): void
    {
        foreach ($sources as $name => $source) {
            $this->assertDoesNotMatchRegularExpression($pattern, $source, "$name: $why");
        }
    }

    public function test_controllers_extend_the_base_controller(): void
    {
        foreach ($this->classesIn('Http/Controllers') as $class) {
            if ($class === Controller::class) {
                continue;
            }
            $this->assertTrue(is_subclass_of($class, Controller::class), "$class must extend Controller");
            // The four kinds of place share PlaceController; their names end with "Controller" without "Tag"
            if (preg_match('/\\\\(Restaurant|Store|Bar|Karaoke)Controller$/', $class)) {
                $this->assertTrue(is_subclass_of($class, PlaceController::class), "$class must extend PlaceController");
                $this->assertFalse((new ReflectionClass($class))->isAbstract(), "$class must be concrete");
            }
        }
    }

    public function test_controllers_do_not_use_other_controllers_nor_the_database_facade(): void
    {
        $sources = $this->sourcesIn('Http/Controllers');
        // A controller answers one request; it must not build or call another controller
        $this->assertNoneContains('/new\s+\w*Controller\b|app\(\w*Controller::class\)/', $sources, 'controllers must not instantiate controllers');
        // Data access goes through the search services and the models, never through raw queries in a controller
        $this->assertNoneContains('/\bDB::|::query\(\)/', $sources, 'controllers must not build queries: use a service or a model method');
    }

    public function test_models_live_in_app_models_and_place_models_share_the_location_trait(): void
    {
        foreach ($this->classesIn('Models') as $class) {
            if (interface_exists($class)) {
                continue; // Place: the interface the four kinds share
            }
            $this->assertTrue(is_subclass_of($class, Model::class), "$class must be an Eloquent model");
        }
        foreach (['Restaurant', 'Store', 'Bar', 'Karaoke'] as $kind) {
            $class = 'App\\Models\\'.$kind;
            $reflection = new ReflectionClass($class);
            $this->assertContains(Location::class, $reflection->getTraitNames(), "$class must use the Location trait");
            $this->assertTrue($reflection->implementsInterface(Place::class), "$class must implement Place (what PlaceSearch needs)");
            $this->assertTrue($reflection->hasMethod('tags'), "$class must have a tags() relation");
            $this->assertTrue($reflection->hasMethod('tagsCached'), "$class must have tagsCached()");
            // Mass assignment allow-list: the seeders and any future form rely on it (doc/08-findings.md)
            $this->assertNotEmpty((new $class)->getFillable(), "$class must declare \$fillable");
        }
    }

    public function test_models_and_support_classes_do_not_depend_on_http(): void
    {
        foreach (['Models', 'Services', 'Support', 'Enums', 'Tools', 'Traits'] as $folder) {
            $sources = $this->sourcesIn($folder);
            if ($folder === 'Services') {
                // The exporter of the Android app builds requests to the application itself (doc/16-standalone-apk-plan.md):
                // it never reads a visitor's request
                unset($sources['StaticExporter.php']);
            }
            $this->assertNoneContains('/Illuminate\\\\Http\\\\|\$request\b/', $sources, "$folder must not know about the HTTP request");
        }
    }

    public function test_enums_are_string_backed_with_a_default_for_bad_input(): void
    {
        foreach ($this->classesIn('Enums') as $class) {
            $enum = new ReflectionEnum($class);
            $this->assertTrue($enum->isBacked(), "$class must be a backed enum (values go in URLs and cache keys)");
            $this->assertSame('string', (string) $enum->getBackingType(), "$class must be backed by strings");
            $this->assertTrue($enum->hasMethod('fromRequest'), "$class must have fromRequest() (unknown values fall back to a default)");
            $this->assertTrue($enum->getMethod('fromRequest')->isStatic());
        }
    }

    public function test_no_debug_calls_left_in_the_app(): void
    {
        $sources = array_merge($this->sourcesIn(''), ['routes/web.php' => File::get(base_path('routes/web.php'))]);
        $this->assertNoneContains('/\b(dd|dump|var_dump|print_r|die|exit|error_log)\s*\(/', $sources, 'debug helpers must not ship');
        // The Debugbar is a dev package: production code must not reference it (doc/08-findings.md)
        $this->assertNoneContains('/\\\\Debugbar\b|Debugbar::|use (Fruitcake|Barryvdh)\\\\/', $sources, 'the Debugbar is a dev package');
    }

    public function test_env_is_only_read_in_config(): void
    {
        // env() returns null once the configuration is cached (php artisan optimize), so only config/ may use it
        // env('KEY'...) with a quoted key: the CSS env(safe-area-inset-*) mentioned in comments is not PHP
        $pattern = '/\benv\([\'"]/';
        $this->assertNoneContains($pattern, $this->sourcesIn(''), 'env() only in config/');
        $this->assertNoneContains($pattern, ['routes/web.php' => File::get(base_path('routes/web.php'))], 'env() only in config/');
        foreach (File::allFiles(resource_path('views')) as $view) {
            $this->assertDoesNotMatchRegularExpression($pattern, $view->getContents(), $view->getRelativePathname().': env() only in config/');
        }
    }

    public function test_no_raw_request_input_is_mass_assigned(): void
    {
        $this->assertNoneContains('/\$request->all\(\)/', $this->sourcesIn('Http'), 'use validated() or explicit fields, never $request->all()');
    }

    public function test_every_application_route_is_a_named_get(): void
    {
        // The site is read-only: no login, no admin, no form. The framework's own routes (_debugbar, storage, up) are not ours.
        foreach (Route::getRoutes() as $route) {
            // Ours: a method of one of our controllers, or a Route::view() (served by the framework's ViewController)
            $action = $route->getActionName();
            $ours = str_starts_with($action, 'App\\') || $action === ViewController::class;
            if (! $ours) {
                continue;
            }
            $this->assertSame(['GET', 'HEAD'], $route->methods(), $route->uri().' must be a GET');
            $this->assertNotNull($route->getName(), $route->uri().' must be named (the views build URLs with route())');
        }
    }

    public function test_routes_can_be_cached(): void
    {
        // "php artisan route:cache" (part of optimize, in the deploy) cannot store a closure: every route points at
        // a controller method or a view. The language loop of routes/web.php registers routes, it is not one.
        // The framework's own routes (local storage, health check, Debugbar) are not ours
        foreach (Route::getRoutes() as $route) {
            if (preg_match('#^(storage/|up$|_debugbar)#', $route->uri())) {
                continue;
            }
            $this->assertNotInstanceOf(\Closure::class, $route->getAction('uses'), $route->uri().' must not be a closure');
        }
    }

    public function test_every_page_exists_in_every_language(): void
    {
        // The default language at the plain names, every other one under its prefix with the same names
        $names = array_filter(array_map(fn ($route) => $route->getName(), iterator_to_array(Route::getRoutes())));
        foreach (LocaleUrl::prefixed() as $locale) {
            foreach (['home', 'restaurants.main', 'restaurants.results', 'restaurants.details', 'restaurants.details.single', 'suggestions'] as $name) {
                $this->assertContains($name, $names);
                $this->assertContains($locale.'.'.$name, $names);
            }
        }
    }

    public function test_views_build_urls_in_the_page_language(): void
    {
        // route('x') always gives the English page: the views use LocaleUrl::route('x') (doc/12-multilingual-ui.md)
        foreach (File::allFiles(resource_path('views')) as $view) {
            $this->assertDoesNotMatchRegularExpression('/(?<![\w:>])route\(/', $view->getContents(), $view->getRelativePathname().': use LocaleUrl::route(), not route()');
            $this->assertStringNotContainsString("url('/')", $view->getContents(), $view->getRelativePathname().": use LocaleUrl::route('home')");
        }
    }

    public function test_views_only_call_functions_that_exist_in_the_script(): void
    {
        $script = File::get(public_path('js/whereto.js'));
        foreach (File::allFiles(resource_path('views')) as $view) {
            // Every onclick="name(" in the HTML must be a global function of whereto.js
            preg_match_all('/on\w+="(\w+)\(/', $view->getContents(), $matches);
            foreach (array_unique($matches[1]) as $function) {
                $this->assertMatchesRegularExpression('/^function '.$function.'\(/m', $script, $view->getRelativePathname().": $function() is not defined in whereto.js");
            }
        }
    }

    public function test_views_escape_their_output(): void
    {
        // {!! !!} prints without escaping: only the store experience, whose seeded text carries links, and the
        // JSON-LD component (JSON must not be HTML-escaped; StructuredData::json() encodes < > & itself) may use it
        $allowed = ['stores/details.blade.php', 'stores/details-single.blade.php', 'components/structured-data.blade.php'];
        foreach (File::allFiles(resource_path('views')) as $view) {
            if (in_array($view->getRelativePathname(), $allowed, true)) {
                continue;
            }
            $this->assertStringNotContainsString('{!!', $view->getContents(), $view->getRelativePathname().': use {{ }} (escaped), not {!! !!}');
        }
    }

    public function test_only_the_layout_has_an_inline_script(): void
    {
        // The JavaScript lives in public/js/whereto.js; the exceptions, with how many inline scripts each may have:
        // the layout's tiny theme snippet that must run before the first paint, and its window.whereToStrings, the
        // messages of whereto.js in the page's language (data printed by the server, doc/12-multilingual-ui.md);
        // the JSON-LD data block, which is not code (components/structured-data.blade.php)
        $exceptions = ['layouts/app.blade.php' => 2, 'components/structured-data.blade.php' => 1];
        foreach (File::allFiles(resource_path('views')) as $view) {
            $inline = preg_match_all('/<script(?![^>]*\bsrc=)/', $view->getContents());
            $allowed = $exceptions[$view->getRelativePathname()] ?? 0;
            $this->assertSame($allowed, $inline, $view->getRelativePathname().': no inline <script>, the code goes in whereto.js');
        }
    }
}
