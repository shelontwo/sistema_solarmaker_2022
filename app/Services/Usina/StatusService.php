<?php

namespace App\Services\Usina;

use Exception;
use App\Models\UsinaStatus;
use App\Helpers\HelperBuscaId;
use Illuminate\Support\Facades\Validator;

class StatusService
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
            $status = UsinaStatus::select('uuid_uss_id', 'uss_id', 'uss_nome', 'uss_tipo');
            $status = $request->input('page') ? $status->paginate() : $status->get();
                
            return ['status' => true, 'data' => $status];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listStatus($uuid)
    {
        try {
            $status = UsinaStatus::where('uuid_uss_id', $uuid)->first();
            return ['status' => true, 'data' => $status];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function novoStatus()
    {
        try {
            $validacao = $this->validaCampos();
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $status = UsinaStatus::create($this->data);

            return ['status' => true, 'data' => $status];
        } catch (\Exception $error) {
                return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function atualizaStatus()
    {
        try {
            $validacao = $this->validaCampos(true);
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $status = UsinaStatus::find(HelperBuscaId::buscaId($this->data['uuid_uss_id'], UsinaStatus::class));
            $status->update($this->data);

            return ['status' => true, 'data' => $status];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function removeStatus($uuid)
    {
        try {
            $status = UsinaStatus::where('uuid_uss_id', $uuid)->delete();
            return ['status' => true, 'msg' => $status ? 'Status removido com sucesso' : 'Erro ao remover status'];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    private function validaCampos($update = false)
    {
        $validacao = [
            'uss_nome' => 'required|string|max:255',
            'uss_tipo' => 'required|integer',
        ];

        if ($update) {
            $validacao['uuid_uss_id'] = 'required|uuid';
        }

        return Validator::make($this->data, $validacao);
    }
}