<?php

namespace App\Services\Integrador;

use Exception;
use App\Models\Cliente;
use App\Models\Integrador;
use App\Models\IntegradorApi;
use App\Helpers\HelperBuscaId;
use Illuminate\Support\Facades\Validator;

class ClienteService
{
    protected $data;

    public function defineData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function indice($request, $uuidIntegrador)
    {
        try {
            $fk_int_id_integrador = HelperBuscaId::buscaId($uuidIntegrador, Integrador::class);

            $clientes = Cliente::select('uuid_cli_id', 'cli_id', 'cli_nome', 'cli_cidade', 'cli_bairro', 'cli_uf', 'cli_usuario')
                ->where('fk_int_id_integrador', $fk_int_id_integrador);
            $clientes = $request->input('page') ? $clientes->paginate() : $clientes->get();

            return ['status' => true, 'data' => $clientes];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listCliente($uuidIntegrador, $uuidCliente)
    {
        try {
            $fk_int_id_integrador = HelperBuscaId::buscaId($uuidIntegrador, Integrador::class);

            $cliente = Cliente::where('uuid_cli_id', $uuidCliente)->where('fk_int_id_integrador', $fk_int_id_integrador)->first();
            return ['status' => true, 'data' => $cliente];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function novoCliente($uuidIntegrador)
    {
        try {
            $validacao = $this->validaCampos();
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_int_id_integrador'] = HelperBuscaId::buscaId($uuidIntegrador, Integrador::class);
            
            $cliente = Cliente::create($this->data);

            return ['status' => true, 'data' => $cliente];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function atualizaCliente()
    {
        try {
            $validacao = $this->validaCampos(true);
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $cliente = Cliente::find(HelperBuscaId::buscaId($this->data['uuid_cli_id'], Cliente::class));
            $cliente->update($this->data);

            return ['status' => true, 'data' => $cliente];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function removeCliente($uuidIntegrador, $uuidCliente)
    {
        try {
            $fk_int_id_integrador = HelperBuscaId::buscaId($uuidIntegrador, Integrador::class);

            $cliente = Cliente::where('uuid_cli_id', $uuidCliente)->where('fk_int_id_integrador', $fk_int_id_integrador)->delete();
            return ['status' => true, 'msg' => $cliente ? 'Cliente removido com sucesso' : 'Erro ao remover cliente'];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    private function validaCampos($update = false)
    {
        $validacao = [
            'cli_nome' => 'required|string|max:255',
            'cli_cep' => 'string|max:255',
            'cli_uf' => 'string|max:255',
            'cli_cidade' => 'string|max:255',
            'cli_bairro' => 'string|max:255',
            'cli_rua' => 'string|max:255',
            'cli_numero' => 'integer',
            'cli_complemento' => 'string|max:255',
            'cli_telefone' => 'string|max:255',
            'cli_celular' => 'string|max:255',
            'cli_email' => 'string|email|max:255',
            'cli_usuario' => 'required|string|max:255',
            'cli_senha' => 'string|max:255',
            'cli_alterar_senha' => 'boolean',
        ];

        if ($update) {
            $validacao['uuid_cli_id'] = 'required|uuid';
        }

        return Validator::make($this->data, $validacao);
    }
}