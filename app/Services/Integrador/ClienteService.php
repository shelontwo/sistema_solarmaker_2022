<?php

namespace App\Services\Integrador;

use Exception;
use App\Models\Cliente;
use App\Models\Integrador;
use App\Models\Distribuidor;
use App\Models\IntegradorApi;
use App\Helpers\HelperBuscaId;
use App\Services\Webhook\WebhookService;
use Illuminate\Support\Facades\Validator;
class ClienteService
{
    protected $data;

    protected $webhookService;
    
    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

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
                return $this->listClientesIntegrador($request, $usuario->fk_int_id_integrador);
            }
            if ($usuario->fk_dis_id_distribuidor) {
                return $this->listClientesDistribuidor($request, $usuario->fk_dis_id_distribuidor);
            }

            $clientes = Cliente::select('uuid_cli_id', 'cli_id', 'cli_nome', 'cli_cidade', 'cli_bairro', 'cli_uf', 'cli_usuario', 'fk_int_id_integrador')
                ->with('integrador');
            $clientes = $request->input('page') ? $clientes->paginate() : $clientes->get();

            return ['status' => true, 'data' => $clientes];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listClientesIntegrador($request, $uuid)
    {
        try {
            $fk_int_id_integrador = is_integer($uuid) ? $uuid : HelperBuscaId::buscaId($uuid, Integrador::class);
            
            $clientes = Cliente::select('uuid_cli_id', 'cli_id', 'cli_nome', 'cli_cidade', 'cli_bairro', 'cli_uf', 'cli_usuario', 'fk_int_id_integrador')
                ->with('integrador')
                ->where('fk_int_id_integrador', $fk_int_id_integrador);
            $clientes = $request->input('page') ? $clientes->paginate() : $clientes->get();

            return ['status' => true, 'data' => $clientes];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listClientesDistribuidor($request, $uuid)
    {
        try {
            $distribuidor = Distribuidor::find(is_integer($uuid) ? $uuid : HelperBuscaId::buscaId($uuid, Distribuidor::class));
            $integradores = $distribuidor->integradores()->get();

            $integradores_ids = [];

            foreach ($integradores as $integrador) {
                array_push($integradores_ids, $integrador->int_id);
            }

            $clientes = Cliente::select('uuid_cli_id', 'cli_id', 'cli_nome', 'cli_cidade', 'cli_bairro', 'cli_uf', 'cli_usuario', 'fk_int_id_integrador')
                ->with('integrador')
                ->whereIn('fk_int_id_integrador', $integradores_ids);
            $clientes = $request->input('page') ? $clientes->paginate() : $clientes->get();
                
            return ['status' => true, 'data' => $clientes];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function listCliente($uuid)
    {
        try {
            $cliente = Cliente::where('uuid_cli_id', $uuid)->with('integrador')->first();
            return ['status' => true, 'data' => $cliente];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function novoCliente()
    {
        try {
            $validacao = $this->validaCampos();
            
            if ($validacao->fails()) {
                return ['status' => false, 'msg' => $validacao->errors(), 'http_status' => 406];
            }

            $this->data['fk_int_id_integrador'] = HelperBuscaId::buscaId($this->data['uuid_int_id'], Integrador::class);
            
            $cliente = Cliente::create($this->data);
            $this->webhookService->novoWebhook(['fk_cli_id_cliente' => $cliente->cli_id]);

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

            $this->data['fk_int_id_integrador'] = HelperBuscaId::buscaId($this->data['uuid_int_id'], Integrador::class);

            $cliente = Cliente::find(HelperBuscaId::buscaId($this->data['uuid_cli_id'], Cliente::class));
            $cliente->update($this->data);
            
            $this->webhookService->novoWebhook(['fk_cli_id_cliente' => $cliente->cli_id]);

            return ['status' => true, 'data' => $cliente];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    public function removeCliente($uuid)
    {
        try {
            $cliente = Cliente::where('uuid_cli_id', $uuid)->first();
            $this->webhookService->novoWebhook(['fk_cli_id_cliente' => $cliente->cli_id]);
            $cliente->delete();
            return ['status' => true, 'msg' => $cliente ? 'Cliente removido com sucesso' : 'Erro ao remover cliente'];
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }

    private function validaCampos($update = false)
    {
        $validacao = [
            'cli_nome' => 'required|string|max:255',
            'cli_cep' => 'string|max:255|nullable',
            'cli_uf' => 'string|max:255|nullable',
            'cli_cidade' => 'string|max:255|nullable',
            'cli_bairro' => 'string|max:255|nullable',
            'cli_rua' => 'string|max:255|nullable',
            'cli_numero' => 'string|max:255|nullable',
            'cli_complemento' => 'string|max:255|nullable',
            'cli_telefone' => 'string|max:255|nullable',
            'cli_celular' => 'string|max:255|nullable',
            'cli_email' => 'string|email|max:255|nullable',
            'cli_usuario' => 'required|string|max:255|nullable',
            'cli_senha' => 'string|max:255|nullable',
            'cli_alterar_senha' => 'boolean',
            // 'cli_webhook_url' => 'url',
            'uuid_int_id' => 'required|uuid',
        ];

        if ($update) {
            $validacao['uuid_cli_id'] = 'required|uuid';
        }

        return Validator::make($this->data, $validacao);
    }
}