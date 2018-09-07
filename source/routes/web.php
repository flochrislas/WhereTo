<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// The following use has been added by and for Eclipse
use Illuminate\Support\Facades\Route;

// ===============================================
// PUBLIC PAGES ==================================
// ===============================================

// show a static view for the home page (resources/views/home.blade.php)
Route::get('/', array('as' => 'home', function() {
    return View::make('home');
}));

Route::get('info/main', array('as' => 'info', function() {
    return view('info.main');
}));

Route::get('vip/main', array('as' => 'vip', function() {
    return view('vip.main');
}));

// about page (resources/views/about.blade.php)
Route::get('about', array('as' => 'about', function() {
    return View::make('about');
}));

Route::get('test-html', array('as' => 'test', function() {
    return view('test-html');
}));

// Public restaurants pages
Route::get('restaurants', 'RestaurantTagController@restaurantsFilter')->name('restaurants.main');
Route::get('restaurants/results', 'RestaurantController@results')->name('restaurants.results');
Route::get('restaurants/details/{id}', 'RestaurantController@details')->name('restaurants.details');
Route::get('tags/autocomplete', 'RestaurantTagController@autocomplete')->name('tags.autocomplete');

// Public stores pages
Route::get('stores', 'StoreTagController@viewAllTags')->name('stores.main');
Route::get('stores/results', 'StoreController@results')->name('stores.results');
Route::get('stores/details/{id}', 'StoreController@details')->name('stores.details');

// Public karaoke pages
Route::get('karaoke', 'KaraokeTagController@viewAllTags')->name('karaokes.main');
Route::get('karaoke/results', 'KaraokeController@results')->name('karaokes.results');
Route::get('karaoke/details/{id}', 'KaraokeController@details')->name('karaokes.details');

// ===============================================
// ADMIN SECTION =================================
// ===============================================
Route::middleware(['auth'])->prefix('admin')->group(function()
{
    // main page for the admin section (resources/views/admin/dashboard.blade.php)
    Route::get('/', function() {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // To switch debug mode ON or OFF or possibly some other setups
    Route::post('debug', 'AdminPanelController@debugModeSwitch')->name('debug.switch');

    // Restaurants CRUD
    Route::resource('restaurants','RestaurantController');

    // RestaurantType CRUD
    // tuto https://www.codexworld.com/laravel-tutorial-crud-add-edit-delete-operations/
    Route::get('restaurantType', 'RestaurantTypeController@index')->name('restaurantType.index');
    Route::get('restaurantType/details/{id}', 'RestaurantTypeController@show')->name('restaurantType.details');
    Route::get('restaurantType/add', 'RestaurantTypeController@create')->name('restaurantType.add');
    Route::post('restaurantType/insert', 'RestaurantTypeController@store')->name('restaurantType.insert');
    Route::get('restaurantType/edit/{id}', 'RestaurantTypeController@edit')->name('restaurantType.edit');
    Route::post('restaurantType/update/{id}', 'RestaurantTypeController@update')->name('restaurantType.update');
    Route::get('restaurantType/delete/{id}', 'RestaurantTypeController@destroy')->name('restaurantType.delete');

    // Restaurant Tags CRUD
    Route::resource('restaurantTag','RestaurantTagController');

    // Restaurants Import Export
    Route::get('restaurantsFiles', function() {
        return view('admin.restaurantsFiles');
    })->name('restaurants.imex');
    Route::get('restaurants/export/{type}', 'RestaurantController@export')->name('restaurants.export');
    Route::post('restaurants/import', 'RestaurantController@importAlt')->name('restaurants.import');

    // Laravel's welcome page
    Route::get('laravel', function () {
        return view('admin.laravel');
    })->name('laravel');
});

// ===============================================
// Authentication ================================
// ===============================================
Auth::routes();
// For admin auth see https://laracasts.com/discuss/channels/laravel/user-admin-authentication
