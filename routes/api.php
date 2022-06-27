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

Route::middleware('cors')->group(function () {
    Route::get('/', function () {
        return response(['API' => 'Works'], 200);
    });
});

Route::middleware('logs')->group(function () {
    Route::group([
        'prefix' => 'auth'
    ], function () {
        Route::post('login', 'Api\AuthController@login')->name('auth.login');
        Route::post('register', 'Api\AuthController@register')->name('auth.register');
    });

    Route::post('/patrocinador', 'Api\InterestedSponsor@create');
    Route::post('/lp', 'Api\LP@contact');
    Route::post('/save', 'Api\DirectSellController@save')->name('direct-sell-save');
    Route::post('/sellConfirm', 'Api\DirectSellController@sellConfirm')->name('confirm-sell');
    Route::post('/sellCancel', 'Api\DirectSellController@sellCancel')->name('cancel-sell');

    Route::prefix('cart')->group(function () {
        Route::post('/create', 'Api\CartController@create');
        Route::post('/sum', 'Api\CartController@sum')->name('cart.sum');
        Route::post('/sub', 'Api\CartController@sub')->name('cart.sub');
        Route::post('/promo', 'Api\CartController@promotion')->name('cart.promo');
        Route::post('/cancel', 'Api\CartController@cancel')->name('cart.cancel');
        Route::post('/attribution', 'Api\CartController@attribution');
        Route::post('/asaas', 'Api\CartController@asaas')->name('cart.asaas');
        Route::post('/webhook', 'Api\CartController@webhook');
        Route::post('/search-email', 'Api\ClientController@searchEMail')->name('search-email');

        Route::post('/check-code', 'Api\ClientController@checkCode')->name('cart.check_code');
        Route::post('/checkin', 'Api\ClientController@checkin')->name('cart.checkin');
        Route::post('/changeProfile', 'Api\ClientController@changeProfile')->name('cart.change-profile');
        Route::post('/sendRecover', 'Api\ClientController@sendRecover')->name('send-recover');
        Route::post('/verifyToken', 'Api\ClientController@verifyToken')->name('verify_token');
        Route::post('/changePassword', 'Api\ClientController@changePassword')->name('change-password');
        Route::post('/remove-checkin', 'Api\ClientController@removeCheckin')->name('cart.remove-checkin');
    });
});

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('user', 'Api\AuthController@user')->name('auth.user');
    Route::post('check-email', 'Api\AuthController@checkEmail')->name('auth.check-email');
});

Route::post('/retrieve-ongoing-sell', 'Api\DirectSellController@ongoingSell')->name('direct-sell-ongoing');
Route::post('/pagination', 'Api\DirectSellController@pagination')->name('pagination');
Route::post('/getSells', 'Api\DirectSellController@getSells')->name('get-sells');
Route::post('/getIngressos', 'Api\DirectSellController@getIngressos')->name('get-ingressos');
Route::post('/get-tickets', 'Api\DirectSellController@getTickets')->name('get-tickets-direct-sell');


Route::prefix('cart')->group(function () {
    Route::get('/timeout', 'Api\CartController@timeout');
    Route::post('/profile', 'Api\ClientController@profile')->name('profile');
    Route::post('/retrieve-open-cart', 'Api\ClientController@retrieveOpenCart')->name('retrieve-open-cart');
    Route::post('/retrieve-closed-cart', 'Api\ClientController@retrieveClosedCart')->name('retrieve-closed-cart');
    Route::get('/get-tickets', 'Api\ClientController@getTickets')->name('get-tickets');
    Route::post('/getIngressos', 'Api\ClientController@getIngressos')->name('cart.get-ingressos');
    Route::post('/retrieve-my-checkins', 'Api\ClientController@retrieveMyCheckIns')->name('cart.retrieve-my-checkins');
});

Route::get('/generator', 'Api\TempDataController@hashGenerator');


Route::prefix('app')->group(function () {
    Route::post('/palestrantes', 'Api\SpeakerController@list');
});
