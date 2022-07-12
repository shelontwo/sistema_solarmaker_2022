<?php

namespace App\Services\Distribuidor;

use Exception;
use App\Models\Distribuidor;
use App\Helpers\HelperBuscaId;
use Illuminate\Support\Facades\Validator;

class DistribuidorService
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
            $distribuidores = Distribuidor::select('uuid_dis_id', 'dis_nome');
            $distribuidores = $request->input('page') ? $distribuidores->paginate() : $distribuidores->get();
            
            return ['status' => true, 'data' => $distribuidores];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listDistribuidor($uuid)
    {
        try {
            $distribuidor = Distribuidor::where('uuid_dis_id', $uuid)->first();
            return ['status' => true, 'data' => $distribuidor];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function novoDistribuidor()
    {
        try {
            $validacao = $this->validaCampos();
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $distribuidor = Distribuidor::create([
                'dis_nome' => $this->data['dis_nome'],
            ]);

            return ['status' => true, 'data' => $distribuidor];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function atualizaDistribuidor()
    {
        try {
            $validacao = $this->validaCampos(true);
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $distribuidor = Distribuidor::find(HelperBuscaId::buscaId($this->data['uuid_dis_id'], Distribuidor::class));
            $distribuidor->update($this->data);

            return ['status' => true, 'data' => $distribuidor];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function removeDistribuidor($uuid)
    {
        try {
            $distribuidor = Distribuidor::where('uuid_dis_id', $uuid)->delete();
            return ['status' => true, 'msg' => $distribuidor ? 'Distribuidor removido com sucesso' : 'Erro ao remover distribuidor'];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    private function validaCampos($update = false)
    {
        $validacao = [
            'dis_nome' => 'required|string|max:255',
        ];

        if ($update) {
            $validacao = [
                'uuid_dis_id' => 'required|string|max:255',
                'dis_nome' => 'string|max:255',
            ];
        }

        return Validator::make($this->data, $validacao);
    }
}