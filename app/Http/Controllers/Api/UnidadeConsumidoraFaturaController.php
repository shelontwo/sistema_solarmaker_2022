<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\UnidadeConsumidora\FaturaService;

class UnidadeConsumidoraFaturaController extends Controller
{
    protected $faturaService;

    public function __construct(
        FaturaService $faturaService
    )
    {
        $this->faturaService = $faturaService;
    }

    public function index(Request $request, $uuidUnidade)
    {
        $data = $this->faturaService->indice($request, $uuidUnidade);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function store(Request $request, $uuidUnidade)
    {
        $this->faturaService->defineData($request->all());
        $data = $this->faturaService->novaFatura($uuidUnidade);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], isset($data['http_status']) ? $data['http_status'] : 400);
    }
   
    public function show($uuidUnidade, $uuid)
    {
        $data = $this->faturaService->listFatura($uuidUnidade, $uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
   
    public function update(Request $request, $uuidUnidade)
    {
        $this->faturaService->defineData($request->all());
        $data = $this->faturaService->atualizaFatura($uuidUnidade);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], 400);
    }
   
    public function destroy($uuidUnidade, $uuid)
    {
        $data = $this->faturaService->removeFatura($uuidUnidade, $uuid);

        if ($data['status']) {
            return response()->json(['msg' => $data['msg']]);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
}
