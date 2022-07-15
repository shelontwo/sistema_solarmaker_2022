<?php

namespace App\Services\UnidadeConsumidora;

use Exception;
use App\Models\Usina;
use App\Helpers\HelperBuscaId;
use App\Models\UnidadeConsumidora;
use App\Models\UnidadeConsumidoraCredito;
use Illuminate\Support\Facades\Validator;

class LancamentoCreditoService
{
    protected $data;

    public function defineData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function indice($request, $uuidUnidade)
    {
        try {
            $fk_usi_id_usina = HelperBuscaId::buscaId($uuidUnidade, UnidadeConsumidora::class);
            $creditos = UnidadeConsumidoraCredito::where('fk_usi_id_usina', $fk_usi_id_usina)
                ->with('usina');
            $creditos = $request->input('page') ? $creditos->paginate() : $creditos->get();
            
            return ['status' => true, 'data' => $creditos];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listCredito($uuidUnidade, $uuid)
    {
        try {
            $fk_usi_id_usina = HelperBuscaId::buscaId($uuidUnidade, UnidadeConsumidora::class);
            $credito = UnidadeConsumidoraCredito::where('fk_usi_id_usina', $fk_usi_id_usina)->where('uuid_ucc_id', $uuid)->first();
            return ['status' => true, 'data' => $credito];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function novoCredito($uuidUnidade)
    {
        try {
            $this->data['uuid_uco_id'] = $uuidUnidade;
            $validacao = $this->validaCampos();
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_usi_id_usina'] = HelperBuscaId::buscaId($this->data['uuid_usi_id'], Usina::class);
            $this->data['fk_uco_id_unidade'] = HelperBuscaId::buscaId($this->data['uuid_uco_id'], UnidadeConsumidora::class);
            
            $credito = UnidadeConsumidoraCredito::create($this->data);

            return ['status' => true, 'data' => $credito];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function atualizaCredito($uuidUnidade)
    {
        try {
            $this->data['uuid_uco_id'] = $uuidUnidade;
            $validacao = $this->validaCampos(true);
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_usi_id_usina'] = HelperBuscaId::buscaId($this->data['uuid_usi_id'], Usina::class);
            $this->data['fk_uco_id_unidade'] = HelperBuscaId::buscaId($this->data['uuid_uco_id'], UnidadeConsumidora::class);

            $credito = UnidadeConsumidoraCredito::find(HelperBuscaId::buscaId($this->data['uuid_ucc_id'], UnidadeConsumidoraCredito::class));
            $credito->update($this->data);

            return ['status' => true, 'data' => $credito];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function removeCredito($uuidUnidade, $uuid)
    {
        try {
            $fk_usi_id_usina = HelperBuscaId::buscaId($uuidUnidade, UnidadeConsumidora::class);
            $credito = UnidadeConsumidoraCredito::where('fk_usi_id_usina', $fk_usi_id_usina)->where('uuid_ucc_id', $uuid)->delete();
            return ['status' => true, 'msg' => $credito ? 'Lançamento de crédito removido com sucesso' : 'Erro ao remover lançamento de crédito'];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    private function validaCampos($update = false)
    {
        $validacao = [
            'ucc_quantidade' => 'required|numeric',
            'ucc_vigencia' => 'required|date',
            'ucc_posto_tarifario' => 'required|integer',
            'ucc_observacao' => 'required|string',
            'uuid_usi_id' => 'required|uuid',
            'uuid_uco_id' => 'required|uuid'
        ];

        if ($update) {
            $validacao['uuid_ucc_id'] = 'required|uuid';
        }

        return Validator::make($this->data, $validacao);
    }
}