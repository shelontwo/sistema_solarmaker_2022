<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\UnidadeConsumidora\LancamentoCreditoService;

class UnidadeConsumidoraCreditoController extends Controller
{
    protected $lancamentoCreditoService;

    public function __construct(
        LancamentoCreditoService $lancamentoCreditoService
    )
    {
        $this->lancamentoCreditoService = $lancamentoCreditoService;
    }

    public function index(Request $request, $uuidUnidade)
    {
        $data = $this->lancamentoCreditoService->indice($request, $uuidUnidade);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function store(Request $request, $uuidUnidade)
    {
        $this->lancamentoCreditoService->defineData($request->all());
        $data = $this->lancamentoCreditoService->novoCredito($uuidUnidade);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], isset($data['http_status']) ? $data['http_status'] : 400);
    }
   
    public function show($uuidUnidade, $uuidInd)
    {
        $data = $this->lancamentoCreditoService->listCredito($uuidUnidade, $uuidInd);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
   
    public function update(Request $request, $uuidUnidade)
    {
        $this->lancamentoCreditoService->defineData($request->all());
        $data = $this->lancamentoCreditoService->atualizaCredito($uuidUnidade);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], 400);
    }
   
    public function destroy($uuidUnidade, $uuidInd)
    {
        $data = $this->lancamentoCreditoService->removeCredito($uuidUnidade, $uuidInd);

        if ($data['status']) {
            return response()->json(['msg' => $data['msg']]);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
}
