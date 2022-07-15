<?php

namespace App\Services\Chamado;

use Exception;
use App\Models\Chamado;
use App\Models\Cliente;
use App\Models\Integrador;
use App\Models\Distribuidor;
use App\Helpers\HelperBuscaId;
use Illuminate\Support\Facades\Validator;

class ChamadoService
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
                return $this->listChamadosIntegrador($request, $usuario->fk_int_id_integrador);
            }
            if ($usuario->fk_dis_id_distribuidor) {
                return $this->listChamadosDistribuidor($request, $usuario->fk_dis_id_distribuidor);
            }

            $chamados = Chamado::select('uuid_cha_id', 'cha_id', 'cha_status', 'cha_descricao', 'fk_cli_id_cliente', 'cha_finalizado_em', 'cha_criado_em', 'cha_atualizado_em')
                ->with('cliente');
            $chamados = $request->input('page') ? $chamados->paginate() : $chamados->get();
                
            return ['status' => true, 'data' => $chamados];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listChamadosCliente($request, $uuid)
    {
        try {
            $fk_cli_id_cliente = $uuid;

            if (!is_integer($uuid)) {
                $fk_cli_id_cliente = HelperBuscaId::buscaId($uuid, Cliente::class);
            }
            
            $chamados = Chamado::select('uuid_cha_id', 'cha_id', 'cha_status', 'cha_descricao', 'fk_cli_id_cliente', 'cha_finalizado_em', 'cha_criado_em', 'cha_atualizado_em')
                ->where('fk_cli_id_cliente', $fk_cli_id_cliente)
                ->with('cliente');
            $chamados = $request->input('page') ? $chamados->paginate() : $chamados->get();
                
            return ['status' => true, 'data' => $chamados];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listChamadosIntegrador($request, $uuid)
    {
        try {
            $integrador = Integrador::find(is_integer($uuid) ? $uuid : HelperBuscaId::buscaId($uuid, Integrador::class));
            $clientes = $integrador->clientes()->get();
            
            $clientes_ids = [];

            foreach ($clientes as $cliente) {
                array_push($clientes_ids, $cliente->cli_id);
            }

            $chamados = Chamado::select('uuid_cha_id', 'cha_id', 'cha_status', 'cha_descricao', 'fk_cli_id_cliente', 'cha_finalizado_em', 'cha_criado_em', 'cha_atualizado_em')
                ->whereIn('fk_cli_id_cliente', $clientes_ids)
                ->with('cliente');
            $chamados = $request->input('page') ? $chamados->paginate() : $chamados->get();
                
            return ['status' => true, 'data' => $chamados];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listChamadosDistribuidor($request, $uuid)
    {
        try {
            $distribuidor = Distribuidor::find(is_integer($uuid) ? $uuid : HelperBuscaId::buscaId($uuid, Distribuidor::class));
            $integradores = $distribuidor->integradores()->get();

            $clientes_ids = [];

            foreach ($integradores as $integrador) {
                $clientes = $integrador->clientes()->get();
                foreach ($clientes as $cliente) {
                    array_push($clientes_ids, $cliente->cli_id);
                }
            }

            $chamados = Chamado::select('uuid_cha_id', 'cha_id', 'cha_status', 'cha_descricao', 'fk_cli_id_cliente', 'cha_finalizado_em', 'cha_criado_em', 'cha_atualizado_em')
                ->whereIn('fk_cli_id_cliente', $clientes_ids)
                ->with('cliente.integrador');
            $chamados = $request->input('page') ? $chamados->paginate() : $chamados->get();
                
            return ['status' => true, 'data' => $chamados];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listChamado($uuid)
    {
        try {
            $chamado = Chamado::where('uuid_cha_id', $uuid)->with('cliente')->first();
            return ['status' => true, 'data' => $chamado];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function novoChamado()
    {
        try {
            $validacao = $this->validaCampos();
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_cli_id_cliente'] = HelperBuscaId::buscaId($this->data['uuid_cli_id'], Cliente::class);
            unset($this->data['uuid_cli_id']);

            if ($this->data['cha_status'] && empty($this->data['cha_solucao'])) {
                $this->data['cha_status'] = false;
            }

            if ($this->data['cha_status'] && !empty($this->data['cha_solucao'])) {
                $this->data['cha_finalizado_em'] = date('Y-m-d h:i:s');
            }

            $chamado = Chamado::create($this->data);

            return ['status' => true, 'data' => $chamado];
        } catch (\Exception $error) {
                return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function atualizaChamado()
    {
        try {
            $validacao = $this->validaCampos(true);
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_cli_id_cliente'] = HelperBuscaId::buscaId($this->data['uuid_cli_id'], Cliente::class);
            unset($this->data['uuid_cli_id']);

            if ($this->data['cha_status'] && empty($this->data['cha_solucao'])) {
                $this->data['cha_status'] = false;
            }

            if ($this->data['cha_status'] && !empty($this->data['cha_solucao'])) {
                $this->data['cha_finalizado_em'] = date('Y-m-d h:i:s');
            }

            $chamado = Chamado::find(HelperBuscaId::buscaId($this->data['uuid_cha_id'], Chamado::class));
            $chamado->update($this->data);

            return ['status' => true, 'data' => $chamado];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function removeChamado($uuid)
    {
        try {
            $chamado = Chamado::where('uuid_cha_id', $uuid)->delete();
            return ['status' => true, 'msg' => $chamado ? 'Chamado removido com sucesso' : 'Erro ao remover chamado'];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    private function validaCampos($update = false)
    {
        $validacao = [
            'cha_status' => 'required|integer',
            'cha_descricao' => 'required|string|max:255',
            'cha_solucao' => 'string|max:255|nullable',
            'uuid_cli_id' => 'required|uuid'
        ];

        if ($update) {
            $validacao['uuid_cha_id'] = 'required|uuid';
        }

        return Validator::make($this->data, $validacao);
    }
}