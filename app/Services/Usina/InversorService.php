<?php

namespace App\Services\Usina;

use Exception;
use App\Models\Usina;
use App\Models\Inversor;
use App\Models\UsinaInversor;
use App\Helpers\HelperBuscaId;
use Illuminate\Support\Facades\Validator;

class InversorService
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

            $inversores = UsinaInversor::select('uuid_inu_id', 'inu_id', 'inu_painel_quantidade', 'inu_painel_potencia', 'fk_inv_id_inversor')
                ->where('fk_usi_id_usina', $fk_usi_id_usina)
                ->with('inversor');
            $inversores = $request->input('page') ? $inversores->paginate() : $inversores->get();
            
            return ['status' => true, 'data' => $inversores];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listInversor($uuidUsina, $uuid)
    {
        try {
            $fk_usi_id_usina = HelperBuscaId::buscaId($uuidUsina, Usina::class);
            $inversor = UsinaInversor::where('fk_usi_id_usina', $fk_usi_id_usina)
                ->where('uuid_inu_id', $uuid)
                ->with('inversor')
                ->first();
            return ['status' => true, 'data' => $inversor];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function novoInversor($uuidUsina)
    {
        try {
            $this->data['uuid_usi_id'] = $uuidUsina;
            $validacao = $this->validaCampos();
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_usi_id_usina'] = HelperBuscaId::buscaId($this->data['uuid_usi_id'], Usina::class);
            $this->data['fk_inv_id_inversor'] = HelperBuscaId::buscaId($this->data['uuid_inv_id'], Inversor::class);
            
            $inversor = UsinaInversor::create($this->data);

            return ['status' => true, 'data' => $inversor];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function atualizaInversor($uuidUsina)
    {
        try {
            $this->data['uuid_usi_id'] = $uuidUsina;
            $validacao = $this->validaCampos(true);
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_usi_id_usina'] = HelperBuscaId::buscaId($this->data['uuid_usi_id'], Usina::class);
            $this->data['fk_inv_id_inversor'] = HelperBuscaId::buscaId($this->data['uuid_inv_id'], Inversor::class);

            $inversor = UsinaInversor::find(HelperBuscaId::buscaId($this->data['uuid_inu_id'], UsinaInversor::class));
            $inversor->update($this->data);

            return ['status' => true, 'data' => $inversor];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function removeInversor($uuidUsina, $uuid)
    {
        try {
            $inversor = UsinaInversor::where('uuid_inu_id', $uuid)->delete();
            return ['status' => true, 'msg' => $inversor ? 'Inversor removido da usina com sucesso' : 'Erro ao remover inversor da usina'];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    private function validaCampos($update = false)
    {
        $validacao = [
            'inu_painel_quantidade' => 'required|integer',
            'inu_painel_potencia' => 'required|string',
            'uuid_usi_id' => 'required|uuid',
            'uuid_inv_id' => 'required|uuid',
        ];

        if ($update) {
            $validacao['uuid_inu_id'] = 'required|uuid';
        }

        return Validator::make($this->data, $validacao);
    }
}