<?php

namespace App\Services\Integrador;

use Exception;
use App\Models\Integrador;
use App\Models\Distribuidor;
use App\Models\IntegradorApi;
use App\Helpers\HelperBuscaId;
use Illuminate\Support\Facades\Validator;

class ApiService
{
    protected $data;

    public function defineData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function indice($request, $uuidIntegrador)
    {
        try {
            $fk_int_id_integrador = HelperBuscaId::buscaId($uuidIntegrador, Integrador::class);

            $apis = IntegradorApi::where('fk_int_id_integrador', $fk_int_id_integrador);
            $apis = $request->input('page') ? $apis->paginate() : $apis->get();

            return ['status' => true, 'data' => $apis];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listApi($uuidIntegrador, $uuidApi)
    {
        try {
            $fk_int_id_integrador = HelperBuscaId::buscaId($uuidIntegrador, Integrador::class);

            $api = IntegradorApi::where('uuid_ina_id', $uuidApi)->where('fk_int_id_integrador', $fk_int_id_integrador)->first();
            return ['status' => true, 'data' => $api];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function novaApi($uuidIntegrador)
    {
        try {
            $validacao = $this->validaCampos();
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_int_id_integrador'] = HelperBuscaId::buscaId($uuidIntegrador, Integrador::class);
            
            $api = IntegradorApi::create($this->data);

            return ['status' => true, 'data' => $api];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function atualizaApi()
    {
        try {
            $validacao = $this->validaCampos(true);
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $api = IntegradorApi::find(HelperBuscaId::buscaId($this->data['uuid_ina_id'], IntegradorApi::class));
            $api->update($this->data);

            return ['status' => true, 'data' => $api];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function removeApi($uuidIntegrador, $uuidApi)
    {
        try {
            $fk_int_id_integrador = HelperBuscaId::buscaId($uuidIntegrador, Integrador::class);

            $api = IntegradorApi::where('uuid_ina_id', $uuidApi)->where('fk_int_id_integrador', $fk_int_id_integrador)->delete();
            return ['status' => true, 'msg' => $api ? 'API removida com sucesso' : 'Erro ao remover API'];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    private function validaCampos($update = false)
    {
        $validacao = [
            'ina_usuario' => 'required|string|max:255',
            'ina_api' => 'required|string|max:255',
            'ina_senha' => 'required|string|max:255',
            'ina_token' => 'string|max:255',
        ];

        if ($update) {
            $validacao['uuid_ina_id'] = 'required|uuid';
        }

        return Validator::make($this->data, $validacao);
    }
}