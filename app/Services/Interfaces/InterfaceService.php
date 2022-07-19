<?php

namespace App\Services\Interfaces;

use Exception;
use App\Models\Usina;
use App\Models\Integrador;
use App\Models\Interfaces;
use App\Helpers\HelperBuscaId;
use App\Models\UnidadeConsumidora;
use Illuminate\Support\Facades\Validator;

class InterfaceService
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
            $interfaces = Interfaces::select('uuid_ite_id', 'ite_id', 'ite_nome', 'fk_ite_id_Interface');
            $interfaces = $request->input('page') ? $interfaces->paginate() : $interfaces->get();
                
            return ['status' => true, 'data' => $interfaces];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listInterface($uuid)
    {
        try {
            $interface = Interfaces::where('uuid_ite_id', $uuid)->first();
            return ['status' => true, 'data' => $interface];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function novoInterface()
    {
        try {
            $validacao = $this->validaCampos();
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            if (isset($this->data['uuid_usi_id'])) {
                $this->data['fk_usi_id_usina'] = HelperBuscaId::buscaId($this->data['uuid_usi_id'], Usina::class);
            }

            if (isset($this->data['uuid_uni_id'])) {
                $this->data['fk_uco_id_unidade'] = HelperBuscaId::buscaId($this->data['uuid_uni_id'], UnidadeConsumidora::class);
            }

            if (isset($this->data['uuid_int_id'])) {
                $this->data['fk_int_id_integrador'] = HelperBuscaId::buscaId($this->data['uuid_int_id'], Integrador::class);
            }

            $interface = Interfaces::create($this->data);

            return ['status' => true, 'data' => $interface];
        } catch (\Exception $error) {
                return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function atualizaInterface()
    {
        try {
            $validacao = $this->validaCampos(true);
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            if (isset($this->data['uuid_usi_id'])) {
                $this->data['fk_usi_id_usina'] = HelperBuscaId::buscaId($this->data['uuid_usi_id'], Usina::class);
            }

            if (isset($this->data['uuid_uni_id'])) {
                $this->data['fk_uco_id_unidade'] = HelperBuscaId::buscaId($this->data['uuid_uni_id'], UnidadeConsumidora::class);
            }

            if (isset($this->data['uuid_int_id'])) {
                $this->data['fk_int_id_integrador'] = HelperBuscaId::buscaId($this->data['uuid_int_id'], Integrador::class);
            }

            $interface = Interfaces::find(HelperBuscaId::buscaId($this->data['uuid_ite_id'], Interfaces::class));
            $interface->update($this->data);

            return ['status' => true, 'data' => $interface];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function removeInterface($uuid)
    {
        try {
            $interface = Interfaces::where('uuid_ite_id', $uuid)->delete();
            return ['status' => true, 'msg' => $interface ? 'Interface removido com sucesso' : 'Erro ao remover interface'];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    private function validaCampos($update = false)
    {
        $validacao = [
            'ite_data' => 'required|date',
            'ite_nsu' => 'required|string|max:255',
            'ite_usuario' => 'string|max:255',
            'ite_senha' => 'string|max:255',
            'uuid_usi_id' => 'uuid|nullable',
            'uuid_uni_id' => 'uuid|nullable',
            'uuid_int_id' => 'uuid|nullable'
        ];

        if ($update) {
            $validacao['uuid_ite_id'] = 'required|uuid';
        }

        return Validator::make($this->data, $validacao);
    }
}