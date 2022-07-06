<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['cors', 'logs'])->group(function () {
    Route::prefix('user')->group(function () {
        Route::post('login', 'Api\AuthController@login')->name('auth.login');
        Route::post('register', 'Api\AuthController@register')->name('auth.register');
    });
    
    Route::middleware('auth.jwt')->group(function () {
        Route::post('user/logout', 'Api\AuthController@logout')->name('auth.logout');

        Route::get('logs', 'Api\LogController@index');
        Route::get('logs/{log}', 'Api\LogController@show');

        Route::get('/', function () {
            return response(['API' => 'Works'], 200);
        });
    });
});
