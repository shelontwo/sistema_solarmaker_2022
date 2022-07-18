<?php

namespace App\Services\UnidadeConsumidora;

use Exception;
use App\Helpers\HelperBuscaId;
use App\Models\Concessionaria;
use App\Models\UnidadeConsumidora;
use Illuminate\Support\Facades\Validator;

class UnidadeConsumidoraService
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
            $unidades = UnidadeConsumidora::select('uuid_uco_id', 'uco_id', 'uco_nome', 'uco_codigo', 'fk_con_id_concessionaria')
                ->with('concessionaria');
            $unidades = $request->input('page') ? $unidades->paginate() : $unidades->get();
                
            return ['status' => true, 'data' => $unidades];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listUnidade($uuid)
    {
        try {
            $unidade = UnidadeConsumidora::where('uuid_uco_id', $uuid)->with('concessionaria')->first();
            return ['status' => true, 'data' => $unidade];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function novaUnidade()
    {
        try {
            $validacao = $this->validaCampos();
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_con_id_concessionaria'] = HelperBuscaId::buscaId($this->data['uuid_con_id'], Concessionaria::class);

            $unidade = UnidadeConsumidora::create($this->data);

            return ['status' => true, 'data' => $unidade];
        } catch (\Exception $error) {
                return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function atualizaUnidade()
    {
        try {
            $validacao = $this->validaCampos(true);
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_con_id_concessionaria'] = HelperBuscaId::buscaId($this->data['uuid_con_id'], Concessionaria::class);

            $unidade = UnidadeConsumidora::find(HelperBuscaId::buscaId($this->data['uuid_uco_id'], UnidadeConsumidora::class));
            $unidade->update($this->data);

            return ['status' => true, 'data' => $unidade];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function removeUnidade($uuid)
    {
        try {
            $unidade = UnidadeConsumidora::where('uuid_uco_id', $uuid)->delete();
            return ['status' => true, 'msg' => $unidade ? 'Unidade consumidora removida com sucesso' : 'Erro ao remover unidade consumidora'];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    private function validaCampos($update = false)
    {
        $validacao = [
            'uco_codigo' => 'string|max:255',
            'uco_nome' => 'required|string|max:255',
            'uco_classificacao' => 'required|integer',
            'uco_tipo' => 'required|integer',
            'uco_modalidade' => 'required|integer',
            'uuid_con_id' => 'required|uuid'
        ];

        if ($update) {
            $validacao['uuid_uco_id'] = 'required|uuid';
        }

        return Validator::make($this->data, $validacao);
    }
}