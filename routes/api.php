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
    
    Route::prefix('discord/config')->group(function () {
        Route::get('/', 'Api\ConfiguracaoController@index');
        Route::post('/', 'Api\ConfiguracaoController@update');
        Route::get('{uuid}', 'Api\ConfiguracaoController@show');
    });

    
    Route::middleware('auth.jwt')->group(function () {
        Route::get('dashboard', 'Api\DashboardController@index');

        Route::prefix('usuarios')->group(function () {
            Route::get('/', 'Api\UsuarioController@index');
            Route::get('master', 'Api\UsuarioController@master');
            Route::get('distribuidor/{uuid}', 'Api\UsuarioController@distribuidor');
            Route::get('integrador/{uuid}', 'Api\UsuarioController@integrador');
            Route::get('{uuid}', 'Api\UsuarioController@show');
            Route::post('novo', 'Api\UsuarioController@store');
            Route::post('edita', 'Api\UsuarioController@update');
            Route::delete('remove/{uuid}', 'Api\UsuarioController@destroy');
        });
        
        Route::get('logs', 'Api\LogController@index');
        Route::get('logs/{log}', 'Api\LogController@show');

        Route::prefix('grupos')->group(function () {
            Route::get('/', 'Api\GrupoController@index');
            Route::get('{uuid}', 'Api\GrupoController@show');
            Route::post('novo', 'Api\GrupoController@store');
            Route::post('edita', 'Api\GrupoController@update');
            Route::delete('remove/{uuid}', 'Api\GrupoController@destroy');
        });

        Route::prefix('modulos')->group(function () {
            Route::get('/', 'Api\ModuloController@index');
            Route::get('{uuid}', 'Api\ModuloController@show');
            Route::post('novo', 'Api\ModuloController@store');
            Route::post('edita', 'Api\ModuloController@update');
            Route::delete('remove/{uuid}', 'Api\ModuloController@destroy');
        });

        Route::prefix('distribuidores')->group(function () {
            Route::get('/', 'Api\DistribuidorController@index');
            Route::get('{uuid}', 'Api\DistribuidorController@show');
            Route::post('novo', 'Api\DistribuidorController@store');
            Route::post('edita', 'Api\DistribuidorController@update');
            Route::delete('remove/{uuid}', 'Api\DistribuidorController@destroy');
        });

        Route::prefix('integradores')->group(function () {
            Route::get('/', 'Api\IntegradorController@index');
            Route::get('distribuidor/{uuid}', 'Api\IntegradorController@distribuidor');
            Route::get('{uuid}', 'Api\IntegradorController@show');
            Route::post('novo', 'Api\IntegradorController@store');
            Route::post('edita', 'Api\IntegradorController@update');
            Route::delete('remove/{uuid}', 'Api\IntegradorController@destroy');
        });

        Route::prefix('apis')->group(function () {
            Route::get('/', 'Api\IntegradorApiController@index');
            Route::get('distribuidor/{uuid}', 'Api\IntegradorApiController@distribuidor');
            Route::get('integrador/{uuid}', 'Api\IntegradorApiController@integrador');
            Route::get('{uuid}', 'Api\IntegradorApiController@show');
            Route::post('novo', 'Api\IntegradorApiController@store');
            Route::post('edita', 'Api\IntegradorApiController@update');
            Route::delete('remove/{uuid}', 'Api\IntegradorApiController@destroy');
        });

        Route::prefix('clientes')->group(function () {
            Route::get('/', 'Api\IntegradorClienteController@index');
            Route::get('distribuidor/{uuid}', 'Api\IntegradorClienteController@distribuidor');
            Route::get('integrador/{uuid}', 'Api\IntegradorClienteController@integrador');
            Route::get('{uuid}', 'Api\IntegradorClienteController@show');
            Route::post('novo', 'Api\IntegradorClienteController@store');
            Route::post('edita', 'Api\IntegradorClienteController@update');
            Route::delete('remove/{uuid}', 'Api\IntegradorClienteController@destroy');
        });

        Route::prefix('chamados')->group(function () {
            Route::get('/', 'Api\ChamadoController@index');
            Route::get('cliente/{uuid}', 'Api\ChamadoController@cliente');
            Route::get('distribuidor/{uuid}', 'Api\ChamadoController@distribuidor');
            Route::get('integrador/{uuid}', 'Api\ChamadoController@integrador');
            Route::get('{uuid}', 'Api\ChamadoController@show');
            Route::post('novo', 'Api\ChamadoController@store');
            Route::post('edita', 'Api\ChamadoController@update');
            Route::delete('remove/{uuid}', 'Api\ChamadoController@destroy');
            
            Route::prefix('{uuidChamado}/comentarios')->group(function () {
                Route::get('/', 'Api\ChamadoComentarioController@index');
                Route::get('{uuid}', 'Api\ChamadoComentarioController@show');
                Route::post('novo', 'Api\ChamadoComentarioController@store');
                Route::post('edita', 'Api\ChamadoComentarioController@update');
                Route::delete('remove/{uuid}', 'Api\ChamadoComentarioController@destroy');
            });
        });

        Route::prefix('concessionarias')->group(function () {
            Route::get('/', 'Api\ConcessionariaController@index');
            Route::get('{uuid}', 'Api\ConcessionariaController@show');
            Route::post('novo', 'Api\ConcessionariaController@store');
            Route::post('edita', 'Api\ConcessionariaController@update');
            Route::delete('remove/{uuid}', 'Api\ConcessionariaController@destroy');
        });

        Route::prefix('inversores')->group(function () {
            Route::get('/', 'Api\InversorController@index');
            Route::get('disponiveis', 'Api\InversorController@disponiveis');
            Route::get('distribuidor/{uuid}', 'Api\InversorController@distribuidor');
            Route::get('integrador/{uuid}', 'Api\InversorController@integrador');
            Route::get('{uuid}', 'Api\InversorController@show');
            Route::post('novo', 'Api\InversorController@store');
            Route::post('edita', 'Api\InversorController@update');
            Route::delete('remove/{uuid}', 'Api\InversorController@destroy');
        });

        Route::prefix('usinas-status')->group(function () {
            Route::get('/', 'Api\UsinaStatusController@index');
            Route::get('{uuid}', 'Api\UsinaStatusController@show');
            Route::post('novo', 'Api\UsinaStatusController@store');
            Route::post('edita', 'Api\UsinaStatusController@update');
            Route::delete('remove/{uuid}', 'Api\UsinaStatusController@destroy');
        });

        Route::prefix('usinas')->group(function () {
            Route::get('/', 'Api\UsinaController@index');
            Route::get('status', 'Api\UsinaController@status');
            Route::post('status/edita', 'Api\UsinaController@updateStatus');
            Route::get('unidade-consumidora/{uuid}', 'Api\UsinaController@unidade');
            Route::get('distribuidor/{uuid}', 'Api\UsinaController@distribuidor');
            Route::get('integrador/{uuid}', 'Api\UsinaController@integrador');
            Route::get('cliente/{uuid}', 'Api\UsinaController@cliente');
            Route::get('{uuid}', 'Api\UsinaController@show');
            Route::post('novo', 'Api\UsinaController@store');
            Route::post('edita', 'Api\UsinaController@update');
            Route::delete('remove/{uuid}', 'Api\UsinaController@destroy');

            Route::prefix('{uuidUsina}/producao')->group(function () {
                Route::get('/', 'Api\UsinaProducaoController@index');
                Route::get('diaria', 'Api\UsinaProducaoController@diaria');
                Route::get('instantanea', 'Api\UsinaProducaoController@instantanea');
                Route::get('{uuid}', 'Api\UsinaProducaoController@show');
                Route::post('novo', 'Api\UsinaProducaoController@store');
                Route::post('edita', 'Api\UsinaProducaoController@update');
                Route::delete('remove/{uuid}', 'Api\UsinaProducaoController@destroy');
            });

            Route::prefix('{uuidUsina}/projeto')->group(function () {
                Route::get('/', 'Api\UsinaProjetoController@show');
                Route::post('edita', 'Api\UsinaProjetoController@update');
            });

            Route::prefix('{uuidUsina}/indicadores')->group(function () {
                Route::get('/', 'Api\UsinaIndicadorController@index');
                Route::get('{uuid}', 'Api\UsinaIndicadorController@show');
                Route::post('novo', 'Api\UsinaIndicadorController@store');
                Route::post('edita', 'Api\UsinaIndicadorController@update');
                Route::delete('remove/{uuid}', 'Api\UsinaIndicadorController@destroy');
            });

            Route::prefix('{uuidUsina}/inversores')->group(function () {
                Route::get('/', 'Api\UsinaInversorController@index');
                Route::get('{uuid}', 'Api\UsinaInversorController@show');
                Route::post('novo', 'Api\UsinaInversorController@store');
                Route::post('edita', 'Api\UsinaInversorController@update');
                Route::delete('remove/{uuid}', 'Api\UsinaInversorController@destroy');
            });

            Route::prefix('{uuidUsina}/sistema-credito')->group(function () {
                Route::get('/', 'Api\UsinaCreditoController@index');
                Route::get('{uuid}', 'Api\UsinaCreditoController@show');
                Route::post('novo', 'Api\UsinaCreditoController@store');
                Route::post('edita', 'Api\UsinaCreditoController@update');
                Route::delete('remove/{uuid}', 'Api\UsinaCreditoController@destroy');
            });
        });

        Route::prefix('unidades-consumidoras')->group(function () {
            Route::get('/', 'Api\UnidadeConsumidoraController@index');
            Route::get('{uuid}', 'Api\UnidadeConsumidoraController@show');
            Route::post('novo', 'Api\UnidadeConsumidoraController@store');
            Route::post('edita', 'Api\UnidadeConsumidoraController@update');
            Route::delete('remove/{uuid}', 'Api\UnidadeConsumidoraController@destroy');

            Route::prefix('{uuidUnidade}/lancamento-credito')->group(function () {
                Route::get('/', 'Api\UnidadeConsumidoraCreditoController@index');
                Route::get('{uuid}', 'Api\UnidadeConsumidoraCreditoController@show');
                Route::post('novo', 'Api\UnidadeConsumidoraCreditoController@store');
                Route::post('edita', 'Api\UnidadeConsumidoraCreditoController@update');
                Route::delete('remove/{uuid}', 'Api\UnidadeConsumidoraCreditoController@destroy');
            });

            Route::prefix('{uuidUnidade}/faturas')->group(function () {
                Route::get('/', 'Api\UnidadeConsumidoraFaturaController@index');
                Route::get('{uuid}', 'Api\UnidadeConsumidoraFaturaController@show');
                Route::post('novo', 'Api\UnidadeConsumidoraFaturaController@store');
                Route::post('edita', 'Api\UnidadeConsumidoraFaturaController@update');
                Route::delete('remove/{uuid}', 'Api\UnidadeConsumidoraFaturaController@destroy');
            });
        });

        Route::prefix('interfaces')->group(function () {
            Route::get('/', 'Api\InterfaceController@index');
            Route::get('{uuid}', 'Api\InterfaceController@show');
            Route::post('novo', 'Api\InterfaceController@store');
            Route::post('edita', 'Api\InterfaceController@update');
            Route::delete('remove/{uuid}', 'Api\InterfaceController@destroy');
        });
    });
});
