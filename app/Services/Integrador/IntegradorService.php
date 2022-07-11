<?php

namespace App\Services\Integrador;

use Exception;
use App\Models\Integrador;
use App\Models\Distribuidor;
use App\Helpers\HelperBuscaId;
use Illuminate\Support\Facades\Validator;

class IntegradorService
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
            $integradores = Integrador::select('uuid_int_id', 'int_nome')->paginate();
            return ['status' => true, 'data' => $integradores];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listIntegrador($uuid)
    {
        try {
            $integrador = Integrador::where('uuid_int_id', $uuid)->first();
            return ['status' => true, 'data' => $integrador];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function novoIntegrador()
    {
        try {
            $validacao = $this->validaCampos();
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $fk_dis_id_distribuidor = HelperBuscaId::buscaId($this->data['uuid_dis_id'], Distribuidor::class);
            
            $integrador = Integrador::create([
                'int_nome' => $this->data['int_nome'],
                'fk_dis_id_distribuidor' => $fk_dis_id_distribuidor,
            ]);

            return ['status' => true, 'data' => $integrador];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function atualizaIntegrador()
    {
        try {
            $validacao = $this->validaCampos(true);
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $integrador = Integrador::find(HelperBuscaId::buscaId($this->data['uuid_int_id'], Integrador::class));
            $integrador->update($this->data);

            return ['status' => true, 'data' => $integrador];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function removeIntegrador($uuid)
    {
        try {
            $integrador = Integrador::where('uuid_int_id', $uuid)->delete();
            return ['status' => true, 'msg' => $integrador ? 'Integrador removido com sucesso' : 'Erro ao remover integrador'];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    private function validaCampos($update = false)
    {
        $validacao = [
            'int_nome' => 'required|string|max:255',
            'uuid_dis_id' => 'required|string|max:255'
        ];

        if ($update) {
            $validacao = [
                'uuid_int_id' => 'required|string|max:255',
                'int_nome' => 'string|max:255',
            ];
        }

        return Validator::make($this->data, $validacao);
    }
}