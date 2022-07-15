<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\UnidadeConsumidora\UnidadeConsumidoraService;

class UnidadeConsumidoraController extends Controller
{
    protected $unidadeConsumidoraService;

    public function __construct(
        UnidadeConsumidoraService $unidadeConsumidoraService
    )
    {
        $this->unidadeConsumidoraService = $unidadeConsumidoraService;
    }

    public function index(Request $request)
    {
        $data = $this->unidadeConsumidoraService->indice($request);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function store(Request $request)
    {
        $this->unidadeConsumidoraService->defineData($request->all());
        $data = $this->unidadeConsumidoraService->novaUnidade();

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], isset($data['http_status']) ? $data['http_status'] : 400);
    }
   
    public function show($uuid)
    {
        $data = $this->unidadeConsumidoraService->listUnidade($uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
   
    public function update(Request $request)
    {
        $this->unidadeConsumidoraService->defineData($request->all());
        $data = $this->unidadeConsumidoraService->atualizaUnidade();

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], 400);
    }
   
    public function destroy($uuid)
    {
        $data = $this->unidadeConsumidoraService->removeUnidade($uuid);

        if ($data['status']) {
            return response()->json(['msg' => $data['msg']]);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
}
