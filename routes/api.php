<?php

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

    Route::middleware('apiJwt')->group(function () {
        Route::get('/', function () {
            return response(['API' => 'Works'], 200);
        });
    });
});
