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

    public function index(Request $request)
    {
        $data = $this->clienteService->indice($request);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function integrador(Request $request, $uuid)
    {
        $data = $this->clienteService->listClientesIntegrador($request, $uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function distribuidor(Request $request, $uuid)
    {
        $data = $this->clienteService->listClientesDistribuidor($request, $uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function store(Request $request)
    {
        $this->clienteService->defineData($request->all());
        $data = $this->clienteService->novoCliente();

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], isset($data['http_status']) ? $data['http_status'] : 400);
    }
   
    public function show($uuid)
    {
        $data = $this->clienteService->listCliente($uuid);

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
   
    public function destroy($uuid)
    {
        $data = $this->clienteService->removeCliente($uuid);

        if ($data['status']) {
            return response()->json(['msg' => $data['msg']]);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
}
