<?php

namespace App\Services\Configuracao;

use Exception;
use App\Models\Integrador;
use App\Models\Configuracao;
use App\Models\Distribuidor;
use App\Helpers\HelperBuscaId;
use Illuminate\Support\Facades\Validator;

class ConfiguracaoService
{
    protected $data;

    public function defineData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function indice()
    {
        try {
            $usuario = auth()->user();

            $configuracao = Configuracao::with('distribuidor', 'integrador');

            if ($usuario->fk_int_id_integrador) {
                $configuracao->where('fk_int_id_integrador', $usuario->fk_int_id_integrador);
            }
            if ($usuario->fk_dis_id_distribuidor) {
                $configuracao->where('fk_dis_id_distribuidor', $usuario->fk_dis_id_distribuidor);
            }

            $configuracao = $configuracao->get();

            return ['status' => true, 'data' => $configuracao];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listConfiguracao($uuid)
    {
        try {
            $configuracao = Configuracao::where('uuid_con_id', $uuid)
                ->with('distribuidor', 'integrador')
                ->first();
            return ['status' => true, 'data' => $configuracao];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function atualizaConfiguracao()
    {
        try {
            // Valida mensagens para alterar status
            if ($this->data['mensagem'] == '!solarmaker parar') {
                return $this->pararCanal();
            }

            // Ajusta campos
            $mensagem = explode('!solarmaker c-', $this->data['mensagem'])[1];
            $geral = explode('-geral', $mensagem);
            $critico = explode('-critico', $mensagem);
            $uuid = null;

            if (count($geral) == 2) {
                $tipo = 'geral';
                $uuid = $geral[0];
            } 
            if (count($critico) == 2) {
                $tipo = 'critico';
                $uuid = $critico[0];
            }
            if (!$uuid) {
                throw new Exception('Mensagem mal formatada', 1);
            }

            // Seta campos
            $this->data['fk_int_id_integrador'] = null;
            $this->data['fk_dis_id_distribuidor'] = null;
            $this->data['con_tipo'] = $tipo;
            $this->data['con_canal_id'] = $this->data['canal_id'];
            $this->data['con_ativo'] = 1;
            unset($this->data['canal_id']);
            unset($this->data['mensagem']);

            // Busca infos para relacionar canal do discord
            $configuracao = Configuracao::where('con_tipo', $this->data['con_tipo']);

            $relacao = Distribuidor::where('uuid_dis_id', $uuid)->first();
            if (!$relacao) {
                $relacao = Integrador::where('uuid_int_id', $uuid)->first();
                $this->data['fk_int_id_integrador'] = $relacao->int_id;
                $configuracao->where('fk_int_id_integrador', $this->data['fk_int_id_integrador']);
            } else {
                $this->data['fk_dis_id_distribuidor'] = $relacao->dis_id;
                $configuracao->where('fk_dis_id_distribuidor', $this->data['fk_dis_id_distribuidor']);
            }

            if (!$relacao) {
                throw new Exception('UUID não válido', 1);
            }

            $configuracao = $configuracao->first();

            if (!$configuracao) {
                $configuracao = Configuracao::create($this->data);
                return ['status' => true, 'msg' => 'Canal do discord configurado com sucesso'];

            } else {
                $configuracao->update($this->data);
                return ['status' => true, 'msg' => 'Canal do discord atualizado com sucesso'];
            }

        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function pararCanal()
    {
        try {
            $configuracao = Configuracao::where('con_canal_id',$this->data['canal_id'])->update([
                'con_ativo' => 0
            ]);
            return ['status' => true, 'msg' => $configuracao ? 'Integração com canal do discord desativada' : 'Erro ao desativada integração com canal do discord'];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }
}