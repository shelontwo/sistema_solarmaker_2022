<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Chamado\ChamadoService;

class ChamadoController extends Controller
{
    protected $chamadoService;

    public function __construct(
        ChamadoService $chamadoService
    )
    {
        $this->chamadoService = $chamadoService;
    }

    public function index(Request $request)
    {
        $data = $this->chamadoService->indice($request);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function cliente(Request $request, $uuid)
    {
        $data = $this->chamadoService->listChamadosCliente($request, $uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function integrador(Request $request, $uuid)
    {
        $data = $this->chamadoService->listChamadosIntegrador($request, $uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function distribuidor(Request $request, $uuid)
    {
        $data = $this->chamadoService->listChamadosDistribuidor($request, $uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function store(Request $request)
    {
        $this->chamadoService->defineData($request->all());
        $data = $this->chamadoService->novoChamado();

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], isset($data['http_status']) ? $data['http_status'] : 400);
    }
   
    public function show($uuid)
    {
        $data = $this->chamadoService->listChamado($uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
   
    public function update(Request $request)
    {
        $this->chamadoService->defineData($request->all());
        $data = $this->chamadoService->atualizaChamado();

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], 400);
    }
   
    public function destroy($uuid)
    {
        $data = $this->chamadoService->removeChamado($uuid);

        if ($data['status']) {
            return response()->json(['msg' => $data['msg']]);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
}
