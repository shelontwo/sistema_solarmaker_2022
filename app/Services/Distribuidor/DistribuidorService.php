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
            $distribuidores = Distribuidor::select('uuid_dis_id', 'dis_id', 'dis_nome', 'dis_nome_fantasia', 'dis_telefone', 'dis_celular');
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

            $distribuidor = Distribuidor::create($this->data);

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
            'dis_nome_fantasia' => 'required|string|max:255',
            'dis_cnpj' => 'string|max:255|nullable',
            'dis_cep' => 'string|max:255|nullable',
            'dis_uf' => 'string|max:255|nullable',
            'dis_cidade' => 'string|max:255|nullable',
            'dis_bairro' => 'string|max:255|nullable',
            'dis_rua' => 'string|max:255|nullable',
            'dis_numero' => 'string|max:255|nullable',
            'dis_complemento' => 'string|max:255|nullable',
            'dis_telefone' => 'string|max:255|nullable',
            'dis_celular' => 'string|max:255|nullable',
            'dis_email' => 'string|email|max:255|nullable',
        ];

        if ($update) {
            $validacao['uuid_dis_id'] = 'required|uuid';
        }

        return Validator::make($this->data, $validacao);
    }
}