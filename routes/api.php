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
    });
    
    Route::middleware('auth.jwt')->group(function () {
        Route::prefix('usuarios')->group(function () {
            Route::get('/', 'Api\UsuarioController@index');
            Route::get('master', 'Api\UsuarioController@master');
            Route::get('distribuidor/{uuid}', 'Api\UsuarioController@distribuidor');
            Route::get('integrador/{uuid}', 'Api\UsuarioController@integrador');
            Route::get('{uuid}', 'Api\UsuarioController@show');
            Route::post('novo', 'Api\UsuarioController@store');
            Route::put('edita', 'Api\UsuarioController@update');
            Route::delete('remove/{uuid}', 'Api\UsuarioController@destroy');
        });
        
        Route::get('logs', 'Api\LogController@index');
        Route::get('logs/{log}', 'Api\LogController@show');

        Route::prefix('grupos')->group(function () {
            Route::get('/', 'Api\GrupoController@index');
            Route::get('{uuid}', 'Api\GrupoController@show');
            Route::post('novo', 'Api\GrupoController@store');
            Route::put('edita', 'Api\GrupoController@update');
            Route::delete('remove/{uuid}', 'Api\GrupoController@destroy');
        });

        Route::prefix('distribuidores')->group(function () {
            Route::get('/', 'Api\DistribuidorController@index');
            Route::get('{uuid}', 'Api\DistribuidorController@show');
            Route::post('novo', 'Api\DistribuidorController@store');
            Route::put('edita', 'Api\DistribuidorController@update');
            Route::delete('remove/{uuid}', 'Api\DistribuidorController@destroy');
        });

        Route::prefix('integradores')->group(function () {
            Route::get('/', 'Api\IntegradorController@index');
            Route::get('{uuid}', 'Api\IntegradorController@show');
            Route::post('novo', 'Api\IntegradorController@store');
            Route::put('edita', 'Api\IntegradorController@update');
            Route::delete('remove/{uuid}', 'Api\IntegradorController@destroy');
        });
    });
});
