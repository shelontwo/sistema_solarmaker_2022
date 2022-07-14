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
            $usuario = auth()->user();

            if ($usuario->fk_dis_id_distribuidor) {
                return $this->listIntegradoresDistribuidor($request, $usuario->fk_dis_id_distribuidor);
            }

            $integradores = Integrador::select('uuid_int_id', 'int_id', 'int_nome', 'int_nome_fantasia', 'int_telefone', 'int_celular', 'fk_dis_id_distribuidor')
                ->with('distribuidor');
            $integradores = $request->input('page') ? $integradores->paginate() : $integradores->get();

            return ['status' => true, 'data' => $integradores];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listIntegradoresDistribuidor($request, $uuid)
    {
        try {
            $fk_dis_id_distribuidor = is_integer($uuid) ? $uuid : HelperBuscaId::buscaId($uuid, Distribuidor::class);

            $integradores = Integrador::select('uuid_int_id', 'int_id', 'int_nome', 'int_nome_fantasia', 'int_telefone', 'int_celular', 'fk_dis_id_distribuidor')
                ->where('fk_dis_id_distribuidor', $fk_dis_id_distribuidor)
                ->with('distribuidor');
            $integradores = $request->input('page') ? $integradores->paginate() : $integradores->get();

            return ['status' => true, 'data' => $integradores];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listIntegrador($uuid)
    {
        try {
            $integrador = Integrador::where('uuid_int_id', $uuid)
                ->with('distribuidor')
                ->first();
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

            $this->data['fk_dis_id_distribuidor'] = HelperBuscaId::buscaId($this->data['uuid_dis_id'], Distribuidor::class);
            
            $integrador = Integrador::create($this->data);

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

            if (!empty($this->data['uuid_dis_id'])) {
                $this->data['fk_dis_id_distribuidor'] = HelperBuscaId::buscaId($this->data['uuid_dis_id'], Distribuidor::class);
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
            'int_nome_fantasia' => 'required|string|max:255',
            'int_cnpj' => 'string|max:255|nullable',
            'int_cep' => 'string|max:255|nullable',
            'int_uf' => 'string|max:255|nullable',
            'int_cidade' => 'string|max:255|nullable',
            'int_bairro' => 'string|max:255|nullable',
            'int_rua' => 'string|max:255|nullable',
            'int_numero' => 'string|max:255|nullable',
            'int_complemento' => 'string|max:255|nullable',
            'int_telefone' => 'string|max:255|nullable',
            'int_celular' => 'string|max:255|nullable',
            'int_email' => 'string|email|max:255|nullable',
            'uuid_dis_id' => 'required|uuid'
        ];

        if ($update) {
            $validacao['uuid_int_id'] = 'required|uuid';
            $validacao['uuid_dis_id'] = 'uuid';
        }

        return Validator::make($this->data, $validacao);
    }
}