<?php

namespace App\Services\Usina;

use Exception;
use App\Models\Usina;
use App\Models\UsinaProducao;
use App\Helpers\HelperBuscaId;
use Illuminate\Support\Facades\Validator;

class ProducaoService
{
    protected $data;

    public function defineData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function indice($request, $uuidUsina)
    {
        try {
            $fk_usi_id_usina = HelperBuscaId::buscaId($uuidUsina, Usina::class);
            $producoes = UsinaProducao::select('uuid_upr_id', 'upr_id', 'upr_data', 'upr_hora', 'upr_producao', 'upr_tipo')
                ->where('fk_usi_id_usina', $fk_usi_id_usina);
            $producoes = $request->input('page') ? $producoes->paginate() : $producoes->get();
            
            return ['status' => true, 'data' => $producoes];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listProducoes($request, $uuidUsina, $tipo)
    {
        try {
            $fk_usi_id_usina = HelperBuscaId::buscaId($uuidUsina, Usina::class);
            $producoes = UsinaProducao::select('uuid_upr_id', 'upr_id', 'upr_data', 'upr_hora', 'upr_producao', 'upr_tipo')
                ->where('fk_usi_id_usina', $fk_usi_id_usina)
                ->where('upr_tipo', $tipo);
            $producoes = $request->input('page') ? $producoes->paginate() : $producoes->get();
            
            return ['status' => true, 'data' => $producoes];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listProducao($uuidUsina, $uuidProd)
    {
        try {
            $fk_usi_id_usina = HelperBuscaId::buscaId($uuidUsina, Usina::class);
            $producao = UsinaProducao::where('fk_usi_id_usina', $fk_usi_id_usina)->where('uuid_upr_id', $uuidProd)->first();
            return ['status' => true, 'data' => $producao];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function novaProducao($uuidUsina)
    {
        try {
            $this->data['uuid_usi_id'] = $uuidUsina;
            $validacao = $this->validaCampos();
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_usi_id_usina'] = HelperBuscaId::buscaId($this->data['uuid_usi_id'], Usina::class);
            
            $producao = UsinaProducao::create($this->data);

            return ['status' => true, 'data' => $producao];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function atualizaProducao($uuidUsina)
    {
        try {
            $this->data['uuid_usi_id'] = $uuidUsina;
            $validacao = $this->validaCampos(true);
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_usi_id_usina'] = HelperBuscaId::buscaId($this->data['uuid_usi_id'], Usina::class);

            $producao = UsinaProducao::find(HelperBuscaId::buscaId($this->data['uuid_upr_id'], UsinaProducao::class));
            $producao->update($this->data);

            return ['status' => true, 'data' => $producao];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function removeProducao($uuidUsina, $uuidProd)
    {
        try {
            $fk_usi_id_usina = HelperBuscaId::buscaId($uuidUsina, Usina::class);
            $producao = UsinaProducao::where('fk_usi_id_usina', $fk_usi_id_usina)->where('uuid_upr_id', $uuidProd)->delete();
            return ['status' => true, 'msg' => $producao ? 'Produção removida com sucesso' : 'Erro ao remover produção'];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    private function validaCampos($update = false)
    {
        $validacao = [
            'upr_data' => 'required|date',
            'upr_hora' => 'string|nullable',
            'upr_producao' => 'required|numeric',
            'upr_tipo' => 'required|integer',
            'uuid_usi_id' => 'required|uuid',
        ];

        if ($update) {
            $validacao['uuid_upr_id'] = 'required|uuid';
        }

        return Validator::make($this->data, $validacao);
    }
}