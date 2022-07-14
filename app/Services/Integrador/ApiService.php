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

    public function indice($request)
    {
        try {
            $usuario = auth()->user();

            if ($usuario->fk_int_id_integrador) {
                return $this->listApisIntegrador($request, $usuario->fk_int_id_integrador);
            }
            if ($usuario->fk_dis_id_distribuidor) {
                return $this->listApisDistribuidor($request, $usuario->fk_dis_id_distribuidor);
            }

            $apis = IntegradorApi::select('uuid_ina_id', 'ina_id', 'ina_usuario', 'ina_api', 'ina_senha', 'ina_token');
            $apis = $request->input('page') ? $apis->paginate() : $apis->get();

            return ['status' => true, 'data' => $apis];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listApisIntegrador($request, $uuid)
    {
        try {
            $fk_int_id_integrador = is_integer($uuid) ? $uuid : HelperBuscaId::buscaId($uuid, Integrador::class);
            
            $apis = IntegradorApi::select('uuid_ina_id', 'ina_id', 'ina_usuario', 'ina_api', 'ina_senha', 'ina_token')
                ->where('fk_int_id_integrador', $fk_int_id_integrador);
            $apis = $request->input('page') ? $apis->paginate() : $apis->get();

            return ['status' => true, 'data' => $apis];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listApisDistribuidor($request, $uuid)
    {
        try {
            $distribuidor = Distribuidor::find(is_integer($uuid) ? $uuid : HelperBuscaId::buscaId($uuid, Distribuidor::class));
            $integradores = $distribuidor->integradores()->get();

            $integradores_ids = [];

            foreach ($integradores as $integrador) {
                array_push($integradores_ids, $integrador->int_id);
            }

            $apis = IntegradorApi::select('uuid_ina_id', 'ina_id', 'ina_usuario', 'ina_api', 'ina_senha', 'ina_token')
                ->whereIn('fk_int_id_integrador', $integradores_ids);
            $apis = $request->input('page') ? $apis->paginate() : $apis->get();

            return ['status' => true, 'data' => $apis];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listApi($uuid)
    {
        try {
            $api = IntegradorApi::where('uuid_ina_id', $uuid)->first();
            return ['status' => true, 'data' => $api];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function novaApi()
    {
        try {
            $validacao = $this->validaCampos();
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_int_id_integrador'] = HelperBuscaId::buscaId($this->data['uuid_int_id'], Integrador::class);
            
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

            $this->data['fk_int_id_integrador'] = HelperBuscaId::buscaId($this->data['uuid_int_id'], Integrador::class);

            $api = IntegradorApi::find(HelperBuscaId::buscaId($this->data['uuid_ina_id'], IntegradorApi::class));
            $api->update($this->data);

            return ['status' => true, 'data' => $api];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function removeApi($uuid)
    {
        try {
            $api = IntegradorApi::where('uuid_ina_id', $uuid)->delete();
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
            'ina_token' => 'string|max:255|nullable',
            'uuid_int_id' => 'required|uuid',
        ];

        if ($update) {
            $validacao['uuid_ina_id'] = 'required|uuid';
        }

        return Validator::make($this->data, $validacao);
    }
}