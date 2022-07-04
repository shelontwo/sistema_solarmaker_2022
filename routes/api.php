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

// Route::middleware('cors')->group(function () {
//     Route::get('/', function () {
//         return response(['API' => 'Works'], 200);
//     });
// });

// Route::middleware('logs')->group(function () {
    Route::prefix('user')->group(function () {
        Route::post('login', 'Api\AuthController@login')->name('auth.login');
        Route::post('register', 'Api\AuthController@register')->name('auth.register');
    });

    Route::middleware('auth:api')->group(function () {
        Route::get('/', function () {
            return response(['API' => 'Works'], 200);
        });
    });

    Route::post('/patrocinador', 'Api\InterestedSponsor@create');
    Route::post('/lp', 'Api\LP@contact');
    Route::post('/save', 'Api\DirectSellController@save')->name('direct-sell-save');
    Route::post('/sellConfirm', 'Api\DirectSellController@sellConfirm')->name('confirm-sell');
    Route::post('/sellCancel', 'Api\DirectSellController@sellCancel')->name('cancel-sell');
// });
