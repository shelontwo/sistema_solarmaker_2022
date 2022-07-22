<?php

namespace App\Services\Dashboard;

use Exception;
use App\Models\Usina;
use App\Models\Modulo;
use App\Models\Chamado;
use App\Models\Integrador;
use App\Models\UsinaStatus;
use App\Models\Distribuidor;
use App\Helpers\HelperBuscaId;
use Illuminate\Support\Facades\Validator;

class DashboardService
{
    protected $data;

    public function defineData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function indice($request)
    {
        try {
            $dashboar = [
                'usinas' => $this->infoUsinas(),
                'chamados' => $this->infoChamados(),
            ];
                
            return ['status' => true, 'data' => $dashboar];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function infoUsinas()
    {
        try {
            $usuario = auth()->user();

            // Lista de usinas baseado nos acessos do user, trazendo o cliente e o status
            $usinas = Usina::select('uuid_usi_id', 'usi_id', 'usi_nome', 'usi_latitude', 'usi_desativado_em', 'usi_longitude', 'fk_cli_id_cliente', 'fk_uss_id_status')
                ->with('cliente', 'status');

            if ($usuario->fk_int_id_integrador) {
                $usinas->where('fk_int_id_integrador', $usuario->fk_int_id_integrador);
            }

            if ($usuario->fk_dis_id_distribuidor) {
                $distribuidor = Distribuidor::find($usuario->fk_dis_id_distribuidor);
                $integradores = $distribuidor->integradores()->get();

                $integradores_ids = [];

                foreach ($integradores as $integrador) {
                    array_push($integradores_ids, $integrador->int_id);
                }

                $usinas->whereIn('fk_int_id_integrador', $integradores_ids);
            }
            $usinas = $usinas->get();

            // Lista total de usinas em cada tipo de status
            
            $statusUsinas = UsinaStatus::with('usinas')->get();
            $statusSend = [
                'labels' => [],
                'data' => []
            ];

            foreach ($statusUsinas as $status) {
                $statusSend['labels'][] = $status->uss_nome;
                $statusSend['data'][] = $status->usinas->count();
            }

            return [
                'total' => $usinas->count(),
                'potencia_instalada' => 0,
                'lista' => $usinas,
                'status' => $statusSend
            ];
        } catch (\Exception $error) {
            // dd($error->getMessage());
            return [];
        }
    }

    public function infoChamados()
    {
        try {
            $usuario = auth()->user();

            // Lista chamados abertos/fechados baseado nos acessos do user
            $chamadosAbertos = Chamado::select('cha_id');
            $chamadosFinalizados = Chamado::select('cha_id');

            if ($usuario->fk_int_id_integrador) {
                $integrador = Integrador::find($usuario->fk_int_id_integrador);
                $clientes = $integrador->clientes()->get();
                
                $clientes_ids = [];

                foreach ($clientes as $cliente) {
                    array_push($clientes_ids, $cliente->cli_id);
                }

                $chamadosAbertos->whereIn('fk_cli_id_cliente', $clientes_ids);
                $chamadosFinalizados->whereIn('fk_cli_id_cliente', $clientes_ids);
            }

            if ($usuario->fk_dis_id_distribuidor) {
                $distribuidor = Distribuidor::find($usuario->fk_dis_id_distribuidor);
                $integradores = $distribuidor->integradores()->get();

                $clientes_ids = [];

                foreach ($integradores as $integrador) {
                    $clientes = $integrador->clientes()->get();
                    foreach ($clientes as $cliente) {
                        array_push($clientes_ids, $cliente->cli_id);
                    }
                }

                $chamadosAbertos->whereIn('fk_cli_id_cliente', $clientes_ids);
                $chamadosFinalizados->whereIn('fk_cli_id_cliente', $clientes_ids);
            }

            $chamadosSend = [
                'labels' => [
                    'Abertos',
                    'Fechados',
                    'Não respondidos'
                ],
                'data' => []
            ];

            $chamadosSend['data'][] = $chamadosAbertos->whereNull('cha_finalizado_em')->count();
            $chamadosSend['data'][] = $chamadosFinalizados->whereNotNull('cha_finalizado_em')->count();
            $chamadosSend['data'][] = $chamadosAbertos->doesntHave('comentarios')->count();

            return $chamadosSend;
        } catch (\Exception $error) {
            // dd($error->getMessage());
            return [];
        }
    }
}