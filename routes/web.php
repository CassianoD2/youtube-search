<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/youtube/search', 'YoutubeController@index');

Route::get('/youtube/regkey', 'YoutubeController@registerApiKey');
Route::post('/youtube/saveapi', 'YoutubeController@saveApiKey');
