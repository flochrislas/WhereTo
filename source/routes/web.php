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

// Laravel's welcome page
Route::get('laravel', function () {
    return view('welcome');
});

// Tuto 01: https://scotch.io/tutorials/simple-and-easy-laravel-routing

// ===============================================
// STATIC PAGES ==================================
// ===============================================

// show a static view for the home page (resources/views/home.blade.php)
Route::get('/', array('as' => 'home', function()
{
    return View::make('home');
}));

// authenticated home ex
Route::get('/home', 'HomeController@index')->name('home');

// about page (resources/views/about.blade.php)
Route::get('about', array('as' => 'about', function()
{
    return View::make('about');
}));

Route::get('test-html', array('as' => 'test', function()
{
    return view('test-html');
}));


// Restaurants Import Export
Route::get('restaurantsfiles2', array('as' => 'restaurantsfiles2', function()
{
    return view('restaurantsfiles2');
}));
// Route::get('robots/import-export', ['uses' => 'ImportExportController@importExport', 'as' => 'robots.import.show'])->middleware('auth');
Route::get('restaurants/export', ['uses' => 'RestaurantController@export', 'as' => 'restaurants.export']);
Route::get('restaurants/exportType/{type}', ['uses' => 'RestaurantController@exportType', 'as' => 'restaurants.exportType']);
Route::post('restaurants/import', ['uses' => 'RestaurantController@importAlt', 'as' => 'restaurants.import']);

Route::get('restaurantsfiles', array('as' => 'restaurantsfiles', function()
{
    return view('restaurantsfiles');
}));
// Route::get('restaurants', 'RestaurantController@index');
Route::post('restaurants/import', 'RestaurantController@import');
Route::get('restaurants/export', 'RestaurantController@export');

Route::get('/restaurants', 'RestaurantController@main')->name('restaurants.main');

// restaurant type CRUD
// tuto https://www.codexworld.com/laravel-tutorial-crud-add-edit-delete-operations/
Route::get('/restaurantType', 'RestaurantTypeController@index')->name('restaurantType.index');
Route::get('/restaurantType/details/{id}', 'RestaurantTypeController@show')->name('restaurantType.details');
Route::get('/restaurantType/add', 'RestaurantTypeController@create')->name('restaurantType.add');
Route::post('/restaurantType/insert', 'RestaurantTypeController@store')->name('restaurantType.insert');
Route::get('/restaurantType/edit/{id}', 'RestaurantTypeController@edit')->name('restaurantType.edit');
Route::post('/restaurantType/update/{id}', 'RestaurantTypeController@update')->name('restaurantType.update');
Route::get('/restaurantType/delete/{id}', 'RestaurantTypeController@destroy')->name('restaurantType.delete');

// ===============================================
// BLOG PAGES ====================================
// ===============================================

// blogroll to show all blog posts
Route::get('blog/{category?}', function($category = null)
{
    // get all the blog stuff from database
    // if a category was passed, use that
    // if no category, get all posts
    if ($category)
        $posts = Post::where('category', '=', $category);
        else
            $posts = Post::all();

            // show the view with blog posts (app/views/blog.blade.php)
            return View::make('blog')->with('posts', $posts);
});

// ===============================================
// ADMIN SECTION =================================
// ===============================================
// With login
Route::middleware(['auth'])->prefix('admin')->group(function()
{
    // main page for the admin section (resources/views/admin/dashboard.blade.php)
    Route::get('/', function()
    {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Restaurants CRUD
    Route::resource('restaurants','RestaurantController');

});

// ===============================================
// Authentication ================================
// ===============================================
Auth::routes();
// For admin auth see https://laracasts.com/discuss/channels/laravel/user-admin-authentication
