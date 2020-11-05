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
Route::namespace('Player')->group(function () {

    Route::get('login', 'PlayerAuthController@index')->name('login');
    Route::post('player-login', 'PlayerAuthController@postLogin')->name('post.login');
    Route::get('register', 'PlayerAuthController@register')->name('register');
    Route::post('player-register', 'PlayerAuthController@postRegister')->name('post.register');

    Route::get('auth/google', 'PlayerAuthController@redirectToGoogle');
    Route::get('callback/google', 'PlayerAuthController@callbackPlayer');

    Route::get('dashboard', 'PlayerAuthController@dashboard')->name('dashboard');
    Route::get('logout', 'PlayerAuthController@logout')->name('logout');
});
