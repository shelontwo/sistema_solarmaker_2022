<?php

namespace App\Services\Usina;

use Exception;
use App\Models\Usina;
use App\Models\UsinaIndicador;
use App\Helpers\HelperBuscaId;
use Illuminate\Support\Facades\Validator;

class IndicadorService
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
            $indicadores = UsinaIndicador::select('uuid_uin_id', 'uin_id', 'uin_data', 'uin_campo', 'uin_valor')
                ->where('fk_usi_id_usina', $fk_usi_id_usina);
            $indicadores = $request->input('page') ? $indicadores->paginate() : $indicadores->get();
            
            return ['status' => true, 'data' => $indicadores];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listIndicador($uuidUsina, $uuidInd)
    {
        try {
            $fk_usi_id_usina = HelperBuscaId::buscaId($uuidUsina, Usina::class);
            $indicador = UsinaIndicador::where('fk_usi_id_usina', $fk_usi_id_usina)->where('uuid_uin_id', $uuidInd)->first();
            return ['status' => true, 'data' => $indicador];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function novoIndicador($uuidUsina)
    {
        try {
            $this->data['uuid_usi_id'] = $uuidUsina;
            $validacao = $this->validaCampos();
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_usi_id_usina'] = HelperBuscaId::buscaId($this->data['uuid_usi_id'], Usina::class);
            
            $indicador = UsinaIndicador::create($this->data);

            return ['status' => true, 'data' => $indicador];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function atualizaIndicador($uuidUsina)
    {
        try {
            $this->data['uuid_usi_id'] = $uuidUsina;
            $validacao = $this->validaCampos(true);
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_usi_id_usina'] = HelperBuscaId::buscaId($this->data['uuid_usi_id'], Usina::class);

            $indicador = UsinaIndicador::find(HelperBuscaId::buscaId($this->data['uuid_uin_id'], UsinaIndicador::class));
            $indicador->update($this->data);

            return ['status' => true, 'data' => $indicador];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function removeIndicador($uuidUsina, $uuidInd)
    {
        try {
            $fk_usi_id_usina = HelperBuscaId::buscaId($uuidUsina, Usina::class);
            $indicador = UsinaIndicador::where('fk_usi_id_usina', $fk_usi_id_usina)->where('uuid_uin_id', $uuidInd)->delete();
            return ['status' => true, 'msg' => $indicador ? 'Indicador removido com sucesso' : 'Erro ao remover indicador'];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    private function validaCampos($update = false)
    {
        $validacao = [
            'uin_data' => 'required|date',
            'uin_campo' => 'required|string|max:255',
            'uin_valor' => 'required|string|max:255',
            'uuid_usi_id' => 'required|uuid',
        ];

        if ($update) {
            $validacao['uuid_uin_id'] = 'required|uuid';
        }

        return Validator::make($this->data, $validacao);
    }
}