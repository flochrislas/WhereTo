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

// about page (resources/views/about.blade.php)
Route::get('about', array('as' => 'about', function()
{
    return View::make('about');
}));

// work page (resources/views/work.blade.php)
Route::get('work', array('as' => 'work', function()
{
    return View::make('work');
}));

Route::get('test-html', array('as' => 'test', function()
{
    return view('test-html');
}));

// Restaurants Import Export
/*
Route::get('restaurants', 'RestaurantController@index');
Route::post('restaurants/import', 'RestaurantController@import');
Route::get('restaurants/export', 'RestaurantController@export');
*/
// restaurant CRUD tuto
/*
Route::get('/create/restaurant','RestaurantController@create');
Route::post('/create/restaurant','RestaurantController@store');
Route::get('restaurants-list', 'RestaurantController@index');
Route::get('/edit/restaurant/{id}','RestaurantController@edit');
Route::post('/edit/restaurant/{id}','RestaurantController@update');
Route::delete('/delete/restaurant/{id}','RestaurantController@destroy');
*/
Route::resource('restaurants','RestaurantController');


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
// LOGIN SECTION =================================
// ===============================================
// show the login page
Route::get('login', function()
{
    // show the login page (app/views/login.blade.php)
    return View::make('login');
});

// process the login
Route::post('login', function()
{
    // validate
    // process login
    // if successful, redirect
    return Redirect::intended();
});

// ===============================================
// ADMIN SECTION =================================
// ===============================================
// Without login
//Route::group(array('prefix' => 'admin'), function()
// With login
Route::group(array('prefix' => 'admin', 'before' => 'auth'), function()
{
    // main page for the admin section (resources/views/admin/dashboard.blade.php)
    Route::get('/', function()
    {
        // return View::make('admin.dashboard');
        return view('admin.dashboard');
    });
    
    // subpage for the posts found at /admin/posts (resources/views/admin/posts.blade.php)
    Route::get('posts', function()
    {
        return View::make('admin.posts');
    });
    
    // subpage to create a post found at /admin/posts/create (resources/views/admin/posts-create.blade.php)
    Route::get('posts/create', function()
    {
        return View::make('admin.posts-create');
    });
    
    // process the create blog post form
    Route::post('posts/create', function()
    {
        // add the post to the database
        // $post = new Post;
        // $post->title = Input::get('title');
        // more stuff here
        // $post->save();
        
        // create a success message
        Session::flash('message', 'Successfully created post!');
        
        // redirect
        return Redirect::to('admin/posts/create');
    });
});
