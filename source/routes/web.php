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

Route::get('/', function () {
    return view('welcome');
});
