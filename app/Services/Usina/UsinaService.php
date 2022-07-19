<?php

namespace App\Services\Usina;

use Exception;
use App\Models\Usina;
use App\Models\Cliente;
use App\Models\Integrador;
use App\Models\UsinaStatus;
use App\Models\Distribuidor;
use App\Helpers\HelperBuscaId;
use App\Models\UnidadeConsumidora;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class UsinaService
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
            $usuario = auth()->user();

            if ($usuario->fk_int_id_integrador) {
                return $this->listUsinasIntegrador($request, $usuario->fk_int_id_integrador);
            }
            if ($usuario->fk_dis_id_distribuidor) {
                return $this->listUsinasDistribuidor($request, $usuario->fk_dis_id_distribuidor);
            }

            $usinas = Usina::select('uuid_usi_id', 'usi_id', 'usi_nome', 'usi_cidade', 'usi_desativada','fk_int_id_integrador', 'fk_cli_id_cliente', 'fk_uss_id_status')
                ->with('integrador', 'cliente', 'status');
            $usinas = $request->input('page') ? $usinas->paginate() : $usinas->get();
            
            return ['status' => true, 'data' => $usinas];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listUsinasIntegrador($request, $uuid)
    {
        try {
            $fk_int_id_integrador = is_integer($uuid) ? $uuid : HelperBuscaId::buscaId($uuid, Integrador::class);

            $usinas = Usina::select('uuid_usi_id', 'usi_id', 'usi_nome', 'usi_cidade', 'usi_desativada','fk_int_id_integrador', 'fk_cli_id_cliente', 'fk_uss_id_status')
                ->where('fk_int_id_integrador', $fk_int_id_integrador)
                ->with('integrador', 'cliente', 'status');
            $usinas = $request->input('page') ? $usinas->paginate() : $usinas->get();
            
            return ['status' => true, 'data' => $usinas];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listUsinasDistribuidor($request, $uuid)
    {
        try {
            $distribuidor = Distribuidor::find(is_integer($uuid) ? $uuid : HelperBuscaId::buscaId($uuid, Distribuidor::class));
            $integradores = $distribuidor->integradores()->get();

            $integradores_ids = [];

            foreach ($integradores as $integrador) {
                array_push($integradores_ids, $integrador->int_id);
            }

            $usinas = Usina::select('uuid_usi_id', 'usi_id', 'usi_nome', 'usi_cidade', 'usi_desativada','fk_int_id_integrador', 'fk_cli_id_cliente', 'fk_uss_id_status')
                ->whereIn('fk_int_id_integrador', $integradores_ids)
                ->with('integrador', 'cliente', 'status');
            $usinas = $request->input('page') ? $usinas->paginate() : $usinas->get();
            
            return ['status' => true, 'data' => $usinas];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listUsinasCliente($request, $uuid)
    {
        try {
            $fk_cli_id_cliente = HelperBuscaId::buscaId($uuid, Cliente::class);

            $usinas = Usina::select('uuid_usi_id', 'usi_id', 'usi_nome', 'usi_cidade', 'usi_desativada','fk_int_id_integrador', 'fk_cli_id_cliente', 'fk_uss_id_status')
                ->where('fk_cli_id_cliente', $fk_cli_id_cliente)
                ->with('integrador', 'cliente', 'status');
            $usinas = $request->input('page') ? $usinas->paginate() : $usinas->get();
            
            return ['status' => true, 'data' => $usinas];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listUsinasUnidade($request, $uuid)
    {
        try {
            $this->defineData(['fk_uco_id_unidade' => HelperBuscaId::buscaId($uuid, UnidadeConsumidora::class)]);

            $usinas = Usina::select('uuid_usi_id', 'usi_id', 'usi_nome', 'usi_cidade', 'usi_desativada','fk_int_id_integrador', 'fk_cli_id_cliente')
                ->with('integrador', 'cliente', 'status')
                ->whereHas('sistema_credito', function (Builder $credito) {
                    $credito->where('fk_uco_id_unidade', $this->data['fk_uco_id_unidade']);
                });
            $usinas = $request->input('page') ? $usinas->paginate() : $usinas->get();
            
            return ['status' => true, 'data' => $usinas];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listUsina($uuid)
    {
        try {
            $usina = Usina::where('uuid_usi_id', $uuid)->first();
            return ['status' => true, 'data' => $usina];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function novaUsina()
    {
        try {
            $validacao = $this->validaCampos();
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_int_id_integrador'] = HelperBuscaId::buscaId($this->data['uuid_int_id'], Integrador::class);
            $this->data['fk_cli_id_cliente'] = HelperBuscaId::buscaId($this->data['uuid_cli_id'], Cliente::class);
            $this->data['fk_uss_id_status'] = HelperBuscaId::buscaId($this->data['uuid_uss_id'], UsinaStatus::class);

            if ($this->data['usi_desativada']) {
                $this->data['usi_desativado_em'] = date('Y-m-d h:i:s');
            }
            
            $usina = Usina::create($this->data);

            return ['status' => true, 'data' => $usina];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function atualizaUsina()
    {
        try {
            $validacao = $this->validaCampos(true);
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_int_id_integrador'] = HelperBuscaId::buscaId($this->data['uuid_int_id'], Integrador::class);
            $this->data['fk_cli_id_cliente'] = HelperBuscaId::buscaId($this->data['uuid_cli_id'], Cliente::class);
            $this->data['fk_uss_id_status'] = HelperBuscaId::buscaId($this->data['uuid_uss_id'], UsinaStatus::class);
            
            $usina = Usina::find(HelperBuscaId::buscaId($this->data['uuid_usi_id'], Usina::class));

            if ($this->data['usi_desativada'] && !$usina->usi_desativado_em) {
                $this->data['usi_desativado_em'] = date('Y-m-d h:i:s');
            }

            if (!$this->data['usi_desativada']) {
                $this->data['usi_desativado_em'] = null;
            }

            $usina->update($this->data);

            return ['status' => true, 'data' => $usina];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function removeUsina($uuid)
    {
        try {
            $usina = Usina::where('uuid_usi_id', $uuid)->delete();
            return ['status' => true, 'msg' => $usina ? 'Usina removido com sucesso' : 'Erro ao remover usina'];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    private function validaCampos($update = false)
    {
        $validacao = [
            'usi_nome' => 'required|string|max:255',
            'usi_cep' => 'required|string|max:255',
            'usi_uf' => 'string|max:255|nullable',
            'usi_cidade' => 'string|max:255|nullable',
            'usi_bairro' => 'string|max:255|nullable',
            'usi_rua' => 'string|max:255|nullable',
            'usi_complemento' => 'string|max:255|nullable',
            'usi_numero' => 'string|max:255|nullable',
            'usi_latitude' => 'string|max:255|nullable',
            'usi_longitude' => 'string|max:255|nullable',
            'usi_desativada' => 'boolean|nullable',
            'usi_webhook_url' => 'url',
            'uuid_int_id' => 'required|uuid',
            'uuid_cli_id' => 'required|uuid',
            'uuid_uss_id' => 'required|uuid',
        ];

        if ($update) {
            $validacao['uuid_usi_id'] = 'required|uuid';
        }

        return Validator::make($this->data, $validacao);
    }
}