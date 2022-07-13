<?php

namespace App\Services\Modulo;

use Exception;
use App\Models\Modulo;
use App\Helpers\HelperBuscaId;
use Illuminate\Support\Facades\Validator;

class ModuloService
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
            $modulos = Modulo::select('uuid_mod_id', 'mod_id', 'mod_nome', 'fk_mod_id_modulo');
            $modulos = $request->input('page') ? $modulos->paginate() : $modulos->get();
                
            return ['status' => true, 'data' => $modulos];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listModulo($uuid)
    {
        try {
            $modulo = Modulo::where('uuid_mod_id', $uuid)->first();
            return ['status' => true, 'data' => $modulo];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function novoModulo()
    {
        try {
            $validacao = $this->validaCampos();
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            if (!empty($this->data['uuid_mod_id'])) {
                $this->data['fk_mod_id_modulo'] = HelperBuscaId::buscaId($this->data['uuid_mod_id'], Modulo::class);
                unset($this->data['uuid_mod_id']);
            }

            $modulo = Modulo::create($this->data);

            return ['status' => true, 'data' => $modulo];
        } catch (\Exception $error) {
                return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function atualizaModulo()
    {
        try {
            $validacao = $this->validaCampos(true);
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $modulo = Modulo::find(HelperBuscaId::buscaId($this->data['uuid_mod_id'], Modulo::class));
            $modulo->update($this->data);

            return ['status' => true, 'data' => $modulo];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function removeModulo($uuid)
    {
        try {
            $modulo = Modulo::where('uuid_mod_id', $uuid)->delete();
            return ['status' => true, 'msg' => $modulo ? 'Módulo removido com sucesso' : 'Erro ao remover módulo'];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    private function validaCampos($update = false)
    {
        $validacao = [
            'mod_nome' => 'required|string|max:255',
            'mod_ordem' => 'integer',
            'mod_icone' => 'string|max:255',
            'uuid_mod_id' => 'uuid|nullable'
        ];

        if ($update) {
            $validacao['uuid_mod_id'] = 'required|uuid';
        }

        return Validator::make($this->data, $validacao);
    }
}