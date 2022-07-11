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
    Route::get('/', function () {
        return response(['API' => 'Works'], 200);
    });

    Route::prefix('usuario')->group(function () {
        Route::post('login', 'Api\AuthController@login');
        Route::post('logout', 'Api\AuthController@logout');
        Route::post('novo', 'Api\AuthController@store');
    });
    
    Route::middleware('auth.jwt')->group(function () {
        Route::get('logs', 'Api\LogController@index');
        Route::get('logs/{log}', 'Api\LogController@show');

        Route::prefix('distribuidores')->group(function () {
            Route::get('/', 'Api\DistribuidorController@index');
            Route::get('{uuid}', 'Api\DistribuidorController@show');
            Route::post('novo', 'Api\DistribuidorController@store');
            Route::put('edita', 'Api\DistribuidorController@update');
            Route::delete('remove/{uuid}', 'Api\DistribuidorController@destroy');
        });

    });
});
