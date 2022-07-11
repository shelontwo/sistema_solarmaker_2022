<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Integrador\IntegradorService;

class IntegradorController extends Controller
{
    protected $integradorService;

    public function __construct(
        IntegradorService $integradorService
    )
    {
        $this->integradorService = $integradorService;
    }

    public function index(Request $request)
    {
        $data = $this->integradorService->indice($request);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $logout['msg']],400);
    }

    public function store(Request $request)
    {
        $this->integradorService->defineData($request->all());
        $data = $this->integradorService->novoIntegrador();

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], isset($data['http_status']) ? $data['http_status'] : 400);
    }
   
    public function show($uuid)
    {
        $data = $this->integradorService->listIntegrador($uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $logout['msg']],400);
    }
   
    public function update(Request $request)
    {
        $this->integradorService->defineData($request->all());
        $data = $this->integradorService->atualizaIntegrador();

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], 400);
    }
   
    public function destroy($uuid)
    {
        $data = $this->integradorService->removeIntegrador($uuid);

        if ($data['status']) {
            return response()->json(['msg' => $data['msg']]);
        }
        return response()->json(['msg' => $logout['msg']],400);
    }
}
