<?php

namespace App\Services\Concessionaria;

use Exception;
use App\Models\Concessionaria;
use App\Helpers\HelperBuscaId;
use Illuminate\Support\Facades\Validator;

class ConcessionariaService
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
            $concessionarias = Concessionaria::select('uuid_con_id', 'con_id', 'con_nome', 'con_cnpj', 'con_uf');
            $concessionarias = $request->input('page') ? $concessionarias->paginate() : $concessionarias->get();
                
            return ['status' => true, 'data' => $concessionarias];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listConcessionaria($uuid)
    {
        try {
            $concessionaria = Concessionaria::where('uuid_con_id', $uuid)->first();
            return ['status' => true, 'data' => $concessionaria];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function novoConcessionaria()
    {
        try {
            $validacao = $this->validaCampos();
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }
            
            $concessionaria = Concessionaria::create($this->data);

            return ['status' => true, 'data' => $concessionaria];
        } catch (\Exception $error) {
                return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function atualizaConcessionaria()
    {
        try {
            $validacao = $this->validaCampos(true);
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $concessionaria = Concessionaria::find(HelperBuscaId::buscaId($this->data['uuid_con_id'], Concessionaria::class));
            $concessionaria->update($this->data);

            return ['status' => true, 'data' => $concessionaria];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function removeConcessionaria($uuid)
    {
        try {
            $concessionaria = Concessionaria::where('uuid_con_id', $uuid)->delete();
            return ['status' => true, 'msg' => $concessionaria ? 'Concessionária removido com sucesso' : 'Erro ao remover concessionária'];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    private function validaCampos($update = false)
    {
        $validacao = [
            'con_nome' => 'required|string|max:255',
            'con_cnpj' => 'required|string|max:255',
            'con_uf' => 'required|string|max:255'
        ];

        if ($update) {
            $validacao['uuid_con_id'] = 'required|uuid';
        }

        return Validator::make($this->data, $validacao);
    }
}