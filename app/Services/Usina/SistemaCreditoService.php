<?php

namespace App\Services\Usina;

use Exception;
use App\Models\Usina;
use App\Helpers\HelperBuscaId;
use App\Models\UnidadeConsumidora;
use App\Models\UsinaSistemaCredito;
use Illuminate\Support\Facades\Validator;

class SistemaCreditoService
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
            $creditos = UsinaSistemaCredito::select('uuid_usc_id', 'usc_id', 'usc_vigencia', 'usc_percentual', 'fk_uco_id_unidade')
                ->where('fk_usi_id_usina', $fk_usi_id_usina)
                ->with('unidade');
            $creditos = $request->input('page') ? $creditos->paginate() : $creditos->get();
            
            return ['status' => true, 'data' => $creditos];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listCredito($uuidUsina, $uuidInd)
    {
        try {
            $fk_usi_id_usina = HelperBuscaId::buscaId($uuidUsina, Usina::class);
            $credito = UsinaSistemaCredito::where('fk_usi_id_usina', $fk_usi_id_usina)->where('uuid_usc_id', $uuidInd)->first();
            return ['status' => true, 'data' => $credito];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function novoCredito($uuidUsina)
    {
        try {
            $this->data['uuid_usi_id'] = $uuidUsina;
            $validacao = $this->validaCampos();
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_usi_id_usina'] = HelperBuscaId::buscaId($this->data['uuid_usi_id'], Usina::class);
            $this->data['fk_uco_id_unidade'] = HelperBuscaId::buscaId($this->data['uuid_uco_id'], UnidadeConsumidora::class);
            
            $credito = UsinaSistemaCredito::create($this->data);

            return ['status' => true, 'data' => $credito];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function atualizaCredito($uuidUsina)
    {
        try {
            $this->data['uuid_usi_id'] = $uuidUsina;
            $validacao = $this->validaCampos(true);
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_usi_id_usina'] = HelperBuscaId::buscaId($this->data['uuid_usi_id'], Usina::class);
            $this->data['fk_uco_id_unidade'] = HelperBuscaId::buscaId($this->data['uuid_uco_id'], UnidadeConsumidora::class);

            $credito = UsinaSistemaCredito::find(HelperBuscaId::buscaId($this->data['uuid_usc_id'], UsinaSistemaCredito::class));
            $credito->update($this->data);

            return ['status' => true, 'data' => $credito];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function removeCredito($uuidUsina, $uuidInd)
    {
        try {
            $fk_usi_id_usina = HelperBuscaId::buscaId($uuidUsina, Usina::class);
            $credito = UsinaSistemaCredito::where('fk_usi_id_usina', $fk_usi_id_usina)->where('uuid_usc_id', $uuidInd)->delete();
            return ['status' => true, 'msg' => $credito ? 'Sistema de crédito removido com sucesso' : 'Erro ao remover sistema de crédito'];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    private function validaCampos($update = false)
    {
        $validacao = [
            'usc_vigencia' => 'required|date',
            'usc_percentual' => 'required|numeric',
            'uuid_usi_id' => 'required|uuid',
            'uuid_uco_id' => 'required|uuid'
        ];

        if ($update) {
            $validacao['uuid_usc_id'] = 'required|uuid';
        }

        return Validator::make($this->data, $validacao);
    }
}