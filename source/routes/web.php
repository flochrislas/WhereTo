<?php

use App\Http\Controllers\BarController;
use App\Http\Controllers\KaraokeController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\SuggestionController;
use App\Support\LocaleUrl;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Each line maps a URL to either a static view (Route::view) or a controller
| method ([Controller::class, 'method']). The ->name() is used by the views
| to build URLs: LocaleUrl::route('restaurants.results').
|
*/

// ===============================================
// ONCE FOR THE WHOLE SITE =======================
// ===============================================

// For the search engines: what to crawl, and the list of every page in every language (see doc/10-seo.md)
Route::get('robots.txt', [SitemapController::class, 'robots'])->name('robots');
Route::get('sitemap.xml', [SitemapController::class, 'sitemap'])->name('sitemap');

// ===============================================
// PUBLIC PAGES, ONCE PER LANGUAGE ===============
// ===============================================

// The pages of the site, registered once per language below (doc/12-multilingual-ui.md). A variable holding a
// function, not a named function: the tests load this file once per test, and a named function cannot be declared twice.
// The function only registers routes; the routes themselves point at controllers, so "php artisan route:cache" works.
$publicPages = function (): void {
    // Static view for the home page (resources/views/home.blade.php)
    Route::view('/', 'home')->name('home');

    // Placeholder pages (resources/views/info/main.blade.php, vip/main.blade.php)
    Route::view('info/main', 'info.main')->name('info');
    Route::view('vip/main', 'vip.main')->name('vip');

    // Lunch suggestion (HTML fragment, loaded by AJAX from the home page)
    Route::get('suggestions', [SuggestionController::class, 'suggest'])->name('suggestions');

    // Main pages of the four kinds of place: the tag filter and the default list (every place, nearest to the station first)
    Route::get('restaurants', [RestaurantController::class, 'index'])->name('restaurants.main');
    Route::get('stores', [StoreController::class, 'index'])->name('stores.main');
    Route::get('bars', [BarController::class, 'index'])->name('bars.main');
    Route::get('karaokes', [KaraokeController::class, 'index'])->name('karaokes.main');

    // Results fragments, details fragments and standalone details pages.
    // 'cache.headers:etag;no_cache': the response gets an ETag (a hash of its content) and the browser must ask again
    // each time (no_cache), but sends the ETag it has; when the content did not change the answer is an empty "304 Not
    // Modified" instead of the full HTML. Repeated searches cost almost no bandwidth.
    Route::middleware('cache.headers:etag;no_cache')->group(function () {
        // Standalone pages of a place (shareable URL, the ones the search engines should index)
        Route::get('restaurant/details/{place}', [RestaurantController::class, 'detailsSingle'])->name('restaurants.details.single');
        Route::get('store/details/{place}', [StoreController::class, 'detailsSingle'])->name('stores.details.single');
        Route::get('bar/details/{place}', [BarController::class, 'detailsSingle'])->name('bars.details.single');
        Route::get('karaoke/details/{place}', [KaraokeController::class, 'detailsSingle'])->name('karaokes.details.single');

        // Fragments loaded by AJAX: pieces of a page, hence 'noindex' (App\Http\Middleware\NoIndex)
        Route::middleware('noindex')->group(function () {
            // restaurants
            Route::get('restaurants/results', [RestaurantController::class, 'results'])->name('restaurants.results');
            Route::get('restaurants/details/{place}', [RestaurantController::class, 'details'])->name('restaurants.details');
            // stores
            Route::get('stores/results', [StoreController::class, 'results'])->name('stores.results');
            Route::get('stores/details/{place}', [StoreController::class, 'details'])->name('stores.details');
            // bars
            Route::get('bars/results', [BarController::class, 'results'])->name('bars.results');
            Route::get('bars/details/{place}', [BarController::class, 'details'])->name('bars.details');
            // karaoke
            Route::get('karaokes/results', [KaraokeController::class, 'results'])->name('karaokes.results');
            Route::get('karaokes/details/{place}', [KaraokeController::class, 'details'])->name('karaokes.details');
        });
    });
};

// The default language (English) at the plain URLs, with the plain route names: /restaurants, 'restaurants.main'.
// 'locale:en' sets it explicitly: the default needs it only when one process answers several requests (the tests do).
Route::middleware('locale:'.LocaleUrl::default())->group($publicPages);

// Every other language under its prefix: /ja/restaurants, named 'ja.restaurants.main'
foreach (LocaleUrl::prefixed() as $locale) {
    Route::prefix($locale)->name($locale.'.')->middleware('locale:'.$locale)->group($publicPages);
}
