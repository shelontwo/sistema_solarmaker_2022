<?php

namespace App\Services\Inversor;

use Exception;
use App\Models\Inversor;
use App\Models\Integrador;
use App\Helpers\HelperBuscaId;
use Illuminate\Support\Facades\Validator;

class InversorService
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
            $inversores = Inversor::select('uuid_inv_id', 'inv_id', 'inv_marca', 'inv_modelo', 'inv_status', 'inv_garantia', 'fk_int_id_integrador')
                ->with('integrador');
            $inversores = $request->input('page') ? $inversores->paginate() : $inversores->get();
                
            return ['status' => true, 'data' => $inversores];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listInversoresIntegrador($request, $uuid)
    {
        try {
            $fk_int_id_integrador = HelperBuscaId::buscaId($uuid, Integrador::class);

            $inversores = Inversor::select('uuid_inv_id', 'inv_id', 'inv_marca', 'inv_modelo', 'inv_status', 'inv_garantia', 'fk_int_id_integrador')
                ->where('fk_int_id_integrador', $fk_int_id_integrador)
                ->with('integrador');
            $inversores = $request->input('page') ? $inversores->paginate() : $inversores->get();
                
            return ['status' => true, 'data' => $inversores];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listInversor($uuid)
    {
        try {
            $inversor = Inversor::where('uuid_inv_id', $uuid)->first();
            return ['status' => true, 'data' => $inversor];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function novoInversor()
    {
        try {
            $validacao = $this->validaCampos();
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_int_id_integrador'] = HelperBuscaId::buscaId($this->data['uuid_int_id'], Integrador::class);
            unset($this->data['uuid_int_id']);

            $inversor = Inversor::create($this->data);

            return ['status' => true, 'data' => $inversor];
        } catch (\Exception $error) {
                return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function atualizaInversor()
    {
        try {
            $validacao = $this->validaCampos(true);
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_int_id_integrador'] = HelperBuscaId::buscaId($this->data['uuid_int_id'], Integrador::class);
            unset($this->data['uuid_int_id']);

            $inversor = Inversor::find(HelperBuscaId::buscaId($this->data['uuid_inv_id'], Inversor::class));
            $inversor->update($this->data);

            return ['status' => true, 'data' => $inversor];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function removeInversor($uuid)
    {
        try {
            $inversor = Inversor::where('uuid_inv_id', $uuid)->delete();
            return ['status' => true, 'msg' => $inversor ? 'Inversor removido com sucesso' : 'Erro ao remover inversor'];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    private function validaCampos($update = false)
    {
        $validacao = [
            'inv_marca' => 'required|string|max:255',
            'inv_modelo' => 'required|string|max:255',
            'inv_status' => 'boolean|nullable',
            'inv_potencia' => 'string|max:255|nullable',
            'inv_serial' => 'string|max:255|nullable',
            'inv_garantia' => 'date|nullable',
            'uuid_int_id' => 'required|uuid'
        ];

        if ($update) {
            $validacao['uuid_inv_id'] = 'required|uuid';
        }

        return Validator::make($this->data, $validacao);
    }
}