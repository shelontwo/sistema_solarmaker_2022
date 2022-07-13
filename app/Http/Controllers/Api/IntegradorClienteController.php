<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Integrador\ClienteService;

class IntegradorClienteController extends Controller
{
    protected $clienteService;

    public function __construct(
        ClienteService $clienteService
    )
    {
        $this->clienteService = $clienteService;
    }

    public function index(Request $request, $uuidIntegrador)
    {
        $data = $this->clienteService->indice($request, $uuidIntegrador);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function store(Request $request, $uuidIntegrador)
    {
        $this->clienteService->defineData($request->all());
        $data = $this->clienteService->novoCliente($uuidIntegrador);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], isset($data['http_status']) ? $data['http_status'] : 400);
    }
   
    public function show($uuidIntegrador, $uuidCliente)
    {
        $data = $this->clienteService->listCliente($uuidIntegrador, $uuidCliente);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
   
    public function update(Request $request)
    {
        $this->clienteService->defineData($request->all());
        $data = $this->clienteService->atualizaCliente();

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], 400);
    }
   
    public function destroy($uuidIntegrador, $uuidCliente)
    {
        $data = $this->clienteService->removeCliente($uuidIntegrador, $uuidCliente);

        if ($data['status']) {
            return response()->json(['msg' => $data['msg']]);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
}
