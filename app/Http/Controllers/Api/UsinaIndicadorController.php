<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Usina\IndicadorService;

class UsinaIndicadorController extends Controller
{
    protected $indicadorService;

    public function __construct(
        IndicadorService $indicadorService
    )
    {
        $this->indicadorService = $indicadorService;
    }

    public function index(Request $request, $uuidUsina)
    {
        $data = $this->indicadorService->indice($request, $uuidUsina);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function store(Request $request, $uuidUsina)
    {
        $this->indicadorService->defineData($request->all());
        $data = $this->indicadorService->novoIndicador($uuidUsina);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], isset($data['http_status']) ? $data['http_status'] : 400);
    }
   
    public function show($uuidUsina, $uuidInd)
    {
        $data = $this->indicadorService->listIndicador($uuidUsina, $uuidInd);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
   
    public function update(Request $request, $uuidUsina)
    {
        $this->indicadorService->defineData($request->all());
        $data = $this->indicadorService->atualizaIndicador($uuidUsina);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], 400);
    }
   
    public function destroy($uuidUsina, $uuidInd)
    {
        $data = $this->indicadorService->removeIndicador($uuidUsina, $uuidInd);

        if ($data['status']) {
            return response()->json(['msg' => $data['msg']]);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
}
