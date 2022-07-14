<?php

namespace App\Services\Grupo;

use Exception;
use App\Models\Grupo;
use App\Models\Modulo;
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
            $grupos = Grupo::select('uuid_gru_id', 'gru_id', 'gru_nome')
                ->with('modulos');
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
            $grupo->gru_modulos = $grupo->modulos()->get();
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

            $modulos = $this->data['gru_modulos'];
            unset($this->data['gru_modulos']);

            $modulos_ids = [];
            foreach ($modulos as $modulo) {
                $mod = Modulo::find(HelperBuscaId::buscaId($modulo['uuid_mod_id'], Modulo::class));
                
                if (!empty($mod->fk_mod_id_modulo) && !in_array($mod->fk_mod_id_modulo, $modulos_ids)) {
                    array_push($modulos_ids, $mod->fk_mod_id_modulo);
                }

                array_push($modulos_ids, $mod->mod_id);
            }

            $grupo = Grupo::create($this->data);
            $grupo->modulos()->attach($modulos_ids);

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

            $modulos = $this->data['gru_modulos'];
            unset($this->data['gru_modulos']);

            $modulos_ids = [];
            foreach ($modulos as $modulo) {
                $mod = Modulo::find(HelperBuscaId::buscaId($modulo['uuid_mod_id'], Modulo::class));
                
                if (!empty($mod->fk_mod_id_modulo) && !in_array($mod->fk_mod_id_modulo, $modulos_ids)) {
                    array_push($modulos_ids, $mod->fk_mod_id_modulo);
                }

                array_push($modulos_ids, $mod->mod_id);
            }

            $grupo = Grupo::find(HelperBuscaId::buscaId($this->data['uuid_gru_id'], Grupo::class));
            $grupo->update($this->data);
            $grupo->modulos()->sync($modulos_ids);

            return ['status' => true, 'data' => $grupo];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function removeGrupo($uuid)
    {
        try {
            $grupo = Grupo::find(HelperBuscaId::buscaId($uuid, Grupo::class));
            $grupo->modulos()->detach();
            $grupo->delete();

            return ['status' => true, 'msg' => $grupo ? 'Grupo removido com sucesso' : 'Erro ao remover grupo'];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    private function validaCampos($update = false)
    {
        $validacao = [
            'gru_nome' => 'required|string|max:255',
            'gru_modulos' => 'required|array'
        ];

        if ($update) {
            $validacao = [
                'uuid_gru_id' => 'required|uuid',
                'gru_nome' => 'string|max:255',
                'gru_modulos' => 'required|array'
            ];
        }

        return Validator::make($this->data, $validacao);
    }
}