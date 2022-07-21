<?php

namespace App\Traits;

use Exception;
use App\Models\Integrador;
use App\Models\Configuracao;
use App\Models\Distribuidor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

trait DiscordTrait
{
    public function send($data)
    {
        try {
            $canal = Configuracao::where('con_tipo', $data['con_tipo']);

            if (isset($data['fk_int_id_integrador'])) {
                $canal->where('fk_int_id_integrador', $data['fk_int_id_integrador']);
            }

            if (isset($data['fk_dis_id_distribuidor'])) {
                $canal->where('fk_dis_id_distribuidor', $data['fk_dis_id_distribuidor']);
            }

            $canal = $canal->first();

            if (!$canal) {
                throw new Exception('Canal não encontrado', 1);
            }

            $dados = [
                'channel' => $canal->con_canal_id,
                'msg' => $data['body'],
            ];

            $urlApi = env('DISCORD_API') . 'msg';
            $body = json_encode($dados);

            Http::post($urlApi, $body);
        } catch (\Throwable $th) {
            return false;
        }
    }
}