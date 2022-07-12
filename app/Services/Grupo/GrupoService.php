<?php

namespace App\Services\Grupo;

use Exception;
use App\Models\Grupo;
use App\Helpers\HelperBuscaId;
use Illuminate\Support\Facades\Validator;

class GrupoService
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
            $grupos = Grupo::select('uuid_gru_id', 'gru_nome');
            $grupos = $request->input('page') ? $grupos->paginate() : $grupos->get();
                
            return ['status' => true, 'data' => $grupos];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listGrupo($uuid)
    {
        try {
            $grupo = Grupo::where('uuid_gru_id', $uuid)->first();
            return ['status' => true, 'data' => $grupo];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function novoGrupo()
    {
        try {
            $validacao = $this->validaCampos();
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $grupo = Grupo::create([
                'gru_nome' => $this->data['gru_nome'],
            ]);

            return ['status' => true, 'data' => $grupo];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function atualizaGrupo()
    {
        try {
            $validacao = $this->validaCampos(true);
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $grupo = Grupo::find(HelperBuscaId::buscaId($this->data['uuid_gru_id'], Grupo::class));
            $grupo->update($this->data);

            return ['status' => true, 'data' => $grupo];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function removeGrupo($uuid)
    {
        try {
            $grupo = Grupo::where('uuid_gru_id', $uuid)->delete();
            return ['status' => true, 'msg' => $grupo ? 'Grupo removido com sucesso' : 'Erro ao remover grupo'];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    private function validaCampos($update = false)
    {
        $validacao = [
            'gru_nome' => 'required|string|max:255',
        ];

        if ($update) {
            $validacao = [
                'uuid_gru_id' => 'required|string|max:255',
                'gru_nome' => 'string|max:255',
            ];
        }

        return Validator::make($this->data, $validacao);
    }
}