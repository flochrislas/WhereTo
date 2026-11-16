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

// ===============================================
// PUBLIC PAGES ==================================
// ===============================================

Route::view('/', 'home')->name('home');
Route::view('info/main', 'info.main')->name('info');
Route::view('vip/main', 'vip.main')->name('vip');

// Lunch suggestion (HTML fragment, loaded by AJAX from the home page)
Route::get('suggestions', [SuggestionController::class, 'suggest'])->name('suggestions');

// Restaurants
Route::get('restaurants', [RestaurantTagController::class, 'restaurantsFilter'])->name('restaurants.main');
Route::get('restaurants/results', [RestaurantController::class, 'results'])->name('restaurants.results');
Route::get('restaurants/details/{id}', [RestaurantController::class, 'details'])->name('restaurants.details');
Route::get('restaurant/details/{id}', [RestaurantController::class, 'detailsSingle'])->name('restaurants.details.single');
Route::get('tags/autocomplete', [RestaurantTagController::class, 'autocomplete'])->name('tags.autocomplete');

// Stores
Route::get('stores', [StoreTagController::class, 'viewAllTags'])->name('stores.main');
Route::get('stores/results', [StoreController::class, 'results'])->name('stores.results');
Route::get('stores/details/{id}', [StoreController::class, 'details'])->name('stores.details');
Route::get('store/details/{id}', [StoreController::class, 'detailsSingle'])->name('stores.details.single');

// Bars
Route::get('bars', [BarTagController::class, 'viewAllTags'])->name('bars.main');
Route::get('bars/results', [BarController::class, 'results'])->name('bars.results');
Route::get('bars/details/{id}', [BarController::class, 'details'])->name('bars.details');
Route::get('bar/details/{id}', [BarController::class, 'detailsSingle'])->name('bars.details.single');

// Karaoke
Route::get('karaokes', [KaraokeTagController::class, 'viewAllTags'])->name('karaokes.main');
Route::get('karaokes/results', [KaraokeController::class, 'results'])->name('karaokes.results');
Route::get('karaokes/details/{id}', [KaraokeController::class, 'details'])->name('karaokes.details');
Route::get('karaoke/details/{id}', [KaraokeController::class, 'detailsSingle'])->name('karaokes.details.single');

// ===============================================
// AUTHENTICATION (login only, no registration) ==
// ===============================================

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->middleware('throttle:5,1');
});
Route::post('logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// ===============================================
// ADMIN SECTION =================================
// ===============================================

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::view('/', 'admin.dashboard')->name('admin.dashboard');

    // Debugbar on/off for the running process
    Route::post('debug', [AdminPanelController::class, 'debugModeSwitch'])->name('debug.switch');

    // Restaurants CRUD
    Route::resource('restaurants', RestaurantAdminController::class);

    // Restaurant types CRUD
    Route::get('restaurantType', [RestaurantTypeController::class, 'index'])->name('restaurantType.index');
    Route::get('restaurantType/details/{id}', [RestaurantTypeController::class, 'show'])->name('restaurantType.details');
    Route::get('restaurantType/add', [RestaurantTypeController::class, 'create'])->name('restaurantType.add');
    Route::post('restaurantType/insert', [RestaurantTypeController::class, 'store'])->name('restaurantType.insert');
    Route::get('restaurantType/edit/{id}', [RestaurantTypeController::class, 'edit'])->name('restaurantType.edit');
    Route::put('restaurantType/update/{id}', [RestaurantTypeController::class, 'update'])->name('restaurantType.update');
    Route::delete('restaurantType/delete/{id}', [RestaurantTypeController::class, 'destroy'])->name('restaurantType.delete');

    // Restaurant tags CRUD
    Route::resource('restaurantTag', RestaurantTagController::class);
});
