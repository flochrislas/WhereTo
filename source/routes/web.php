<?php

use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BarController;
use App\Http\Controllers\BarTagController;
use App\Http\Controllers\KaraokeController;
use App\Http\Controllers\KaraokeTagController;
use App\Http\Controllers\RestaurantAdminController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\RestaurantTagController;
use App\Http\Controllers\RestaurantTypeController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\StoreTagController;
use App\Http\Controllers\SuggestionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Each line maps a URL to either a static view (Route::view) or a controller
| method ([Controller::class, 'method']). The ->name() is used by the views
| to build URLs: route('restaurants.results').
|
*/

// ===============================================
// PUBLIC PAGES ==================================
// ===============================================

// Static view for the home page (resources/views/home.blade.php)
Route::view('/', 'home')->name('home');
// Placeholder pages (resources/views/info/main.blade.php, vip/main.blade.php)
Route::view('info/main', 'info.main')->name('info');
Route::view('vip/main', 'vip.main')->name('vip');

// Lunch suggestion (HTML fragment, loaded by AJAX from the home page)
Route::get('suggestions', [SuggestionController::class, 'suggest'])->name('suggestions');

// Public filter pages (tag cloud) of the four kinds of place
Route::get('restaurants', [RestaurantTagController::class, 'restaurantsFilter'])->name('restaurants.main');
Route::get('stores', [StoreTagController::class, 'viewAllTags'])->name('stores.main');
Route::get('bars', [BarTagController::class, 'viewAllTags'])->name('bars.main');
Route::get('karaokes', [KaraokeTagController::class, 'viewAllTags'])->name('karaokes.main');

// Results fragments, details fragments and standalone details pages.
// 'cache.headers:etag;no_cache': the response gets an ETag (a hash of its content) and the browser must ask again
// each time (no_cache), but sends the ETag it has; when the content did not change the answer is an empty "304 Not
// Modified" instead of the full HTML. Repeated searches cost almost no bandwidth.
Route::middleware('cache.headers:etag;no_cache')->group(function () {
    // restaurants
    Route::get('restaurants/results', [RestaurantController::class, 'results'])->name('restaurants.results');
    Route::get('restaurants/details/{place}', [RestaurantController::class, 'details'])->name('restaurants.details');
    Route::get('restaurant/details/{place}', [RestaurantController::class, 'detailsSingle'])->name('restaurants.details.single');
    Route::get('tags/autocomplete', [RestaurantTagController::class, 'autocomplete'])->name('tags.autocomplete');

    // stores
    Route::get('stores/results', [StoreController::class, 'results'])->name('stores.results');
    Route::get('stores/details/{place}', [StoreController::class, 'details'])->name('stores.details');
    Route::get('store/details/{place}', [StoreController::class, 'detailsSingle'])->name('stores.details.single');

    // bars
    Route::get('bars/results', [BarController::class, 'results'])->name('bars.results');
    Route::get('bars/details/{place}', [BarController::class, 'details'])->name('bars.details');
    Route::get('bar/details/{place}', [BarController::class, 'detailsSingle'])->name('bars.details.single');

    // karaoke
    Route::get('karaokes/results', [KaraokeController::class, 'results'])->name('karaokes.results');
    Route::get('karaokes/details/{place}', [KaraokeController::class, 'details'])->name('karaokes.details');
    Route::get('karaoke/details/{place}', [KaraokeController::class, 'detailsSingle'])->name('karaokes.details.single');
});

// ===============================================
// AUTHENTICATION (login only, no registration) ==
// ===============================================

// 'guest' middleware: only for visitors who are NOT logged in (others are sent to the dashboard)
Route::middleware('guest')->group(function () {
    // Show the form, then handle it; the name 'login' is where the 'auth' middleware redirects guests
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    // At most 5 attempts per minute per IP (brute-force protection)
    Route::post('login', [LoginController::class, 'login'])->middleware('throttle:5,1');
});
// Logout is a POST (with CSRF token) so a link on another site cannot log you out
Route::post('logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// ===============================================
// ADMIN SECTION =================================
// ===============================================

// 'auth': everything below requires a logged-in user; 'can:admin': and the 'admin' gate (users.level, see
// AppServiceProvider) must allow them, else 403; 'admin' prefix: all URLs start with /admin
Route::middleware(['auth', 'can:admin'])->prefix('admin')->group(function () {
    // Main page of the admin section (resources/views/admin/dashboard.blade.php)
    Route::view('/', 'admin.dashboard')->name('admin.dashboard');

    // To switch the debug mode (Debugbar) ON or OFF
    Route::post('debug', [AdminPanelController::class, 'debugModeSwitch'])->name('debug.switch');

    // Restaurants CRUD: one line gives the 7 routes index/create/store/show/edit/update/destroy
    Route::resource('restaurants', RestaurantAdminController::class);

    // Restaurant types CRUD, with explicit routes (tutorial https://www.codexworld.com/laravel-tutorial-crud-add-edit-delete-operations/)
    Route::get('restaurantType', [RestaurantTypeController::class, 'index'])->name('restaurantType.index');
    Route::get('restaurantType/details/{restaurantType}', [RestaurantTypeController::class, 'show'])->name('restaurantType.details');
    Route::get('restaurantType/add', [RestaurantTypeController::class, 'create'])->name('restaurantType.add');
    Route::post('restaurantType/insert', [RestaurantTypeController::class, 'store'])->name('restaurantType.insert');
    Route::get('restaurantType/edit/{restaurantType}', [RestaurantTypeController::class, 'edit'])->name('restaurantType.edit');
    Route::put('restaurantType/update/{restaurantType}', [RestaurantTypeController::class, 'update'])->name('restaurantType.update');
    Route::delete('restaurantType/delete/{restaurantType}', [RestaurantTypeController::class, 'destroy'])->name('restaurantType.delete');

    // Restaurant tags CRUD (same 7 routes as the restaurants)
    Route::resource('restaurantTag', RestaurantTagController::class);
});
