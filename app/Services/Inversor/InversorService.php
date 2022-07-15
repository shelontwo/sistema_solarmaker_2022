<?php

namespace App\Services\Inversor;

use Exception;
use App\Models\Inversor;
use App\Models\Integrador;
use App\Models\Distribuidor;
use App\Helpers\HelperBuscaId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
class InversorService
{
    protected $data;

    public function defineData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function indice($request, $somenteDisponiveis = false)
    {
        try {
            $usuario = auth()->user();

            if ($usuario->fk_int_id_integrador) {
                return $this->listInversoresIntegrador($request, $usuario->fk_int_id_integrador, $somenteDisponiveis);
            }
            if ($usuario->fk_dis_id_distribuidor) {
                return $this->listInversoresDistribuidor($request, $usuario->fk_dis_id_distribuidor, $somenteDisponiveis);
            }

            $inversores = Inversor::select('uuid_inv_id', 'inv_id', 'inv_marca', 'inv_modelo', 'inv_status', 'inv_garantia', 'fk_int_id_integrador')
                ->with(array('integrador', 'usinaInversor' => function ($filho) {
                    $filho->select('uuid_inu_id', 'inu_id', 'fk_inv_id_inversor');
                }));

            if ($somenteDisponiveis) {
                $inversores->doesntHave('usinaInversor');
            }
                
            $inversores = $request->input('page') ? $inversores->paginate() : $inversores->get();

            return ['status' => true, 'data' => $inversores];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listInversoresIntegrador($request, $uuid, $somenteDisponiveis = false)
    {
        try {
            $fk_int_id_integrador = is_integer($uuid) ? $uuid : HelperBuscaId::buscaId($uuid, Integrador::class);

            $inversores = Inversor::select('uuid_inv_id', 'inv_id', 'inv_marca', 'inv_modelo', 'inv_status', 'inv_garantia', 'fk_int_id_integrador')
                ->where('fk_int_id_integrador', $fk_int_id_integrador)
                ->with(array('integrador', 'usinaInversor' => function ($filho) {
                    $filho->select('uuid_inu_id', 'inu_id', 'fk_inv_id_inversor');
                }));

            if ($somenteDisponiveis) {
                $inversores->doesntHave('usinaInversor');
            }

            $inversores = $request->input('page') ? $inversores->paginate() : $inversores->get();
                
            return ['status' => true, 'data' => $inversores];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listInversoresDistribuidor($request, $uuid, $somenteDisponiveis = false)
    {
        try {
            $distribuidor = Distribuidor::find(is_integer($uuid) ? $uuid : HelperBuscaId::buscaId($uuid, Distribuidor::class));
            $integradores = $distribuidor->integradores()->get();

            $integradores_ids = [];

            foreach ($integradores as $integrador) {
                array_push($integradores_ids, $integrador->int_id);
            }

            $inversores = Inversor::select('uuid_inv_id', 'inv_id', 'inv_marca', 'inv_modelo', 'inv_status', 'inv_garantia', 'fk_int_id_integrador')
                ->whereIn('fk_int_id_integrador', $integradores_ids)
                ->with(array('integrador', 'usinaInversor' => function ($filho) {
                    $filho->select('uuid_inu_id', 'inu_id', 'fk_inv_id_inversor');
                }));

            if ($somenteDisponiveis) {
                $inversores->doesntHave('usinaInversor');
            }
            
            $inversores = $request->input('page') ? $inversores->paginate() : $inversores->get();
                
            return ['status' => true, 'data' => $inversores];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listInversor($uuid)
    {
        try {
            $inversor = Inversor::where('uuid_inv_id', $uuid)->with('integrador')->first();
            return ['status' => true, 'data' => $inversor];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function novoInversor()
    {
        try {
            $validacao = $this->validaCampos();
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_int_id_integrador'] = HelperBuscaId::buscaId($this->data['uuid_int_id'], Integrador::class);
            unset($this->data['uuid_int_id']);

            $inversor = Inversor::create($this->data);

            return ['status' => true, 'data' => $inversor];
        } catch (\Exception $error) {
                return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function atualizaInversor()
    {
        try {
            $validacao = $this->validaCampos(true);
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_int_id_integrador'] = HelperBuscaId::buscaId($this->data['uuid_int_id'], Integrador::class);
            unset($this->data['uuid_int_id']);

            $inversor = Inversor::find(HelperBuscaId::buscaId($this->data['uuid_inv_id'], Inversor::class));
            $inversor->update($this->data);

            return ['status' => true, 'data' => $inversor];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function removeInversor($uuid)
    {
        try {
            $inversor = Inversor::where('uuid_inv_id', $uuid)->delete();
            return ['status' => true, 'msg' => $inversor ? 'Inversor removido com sucesso' : 'Erro ao remover inversor'];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    private function validaCampos($update = false)
    {
        $validacao = [
            'inv_marca' => 'required|string|max:255',
            'inv_modelo' => 'required|string|max:255',
            'inv_status' => 'boolean|nullable',
            'inv_potencia' => 'string|max:255|nullable',
            'inv_serial' => 'string|max:255|nullable',
            'inv_garantia' => 'date|nullable',
            'uuid_int_id' => 'required|uuid'
        ];

        if ($update) {
            $validacao['uuid_inv_id'] = 'required|uuid';
        }

        return Validator::make($this->data, $validacao);
    }
}