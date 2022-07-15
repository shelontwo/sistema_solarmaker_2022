<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Usina\SistemaCreditoService;

class UsinaCreditoController extends Controller
{
    protected $sistemaCreditoServiceService;

    public function __construct(
        SistemaCreditoService $sistemaCreditoServiceService
    )
    {
        $this->sistemaCreditoServiceService = $sistemaCreditoServiceService;
    }

    public function index(Request $request, $uuidUsina)
    {
        $data = $this->sistemaCreditoServiceService->indice($request, $uuidUsina);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function store(Request $request, $uuidUsina)
    {
        $this->sistemaCreditoServiceService->defineData($request->all());
        $data = $this->sistemaCreditoServiceService->novoCredito($uuidUsina);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], isset($data['http_status']) ? $data['http_status'] : 400);
    }
   
    public function show($uuidUsina, $uuidInd)
    {
        $data = $this->sistemaCreditoServiceService->listCredito($uuidUsina, $uuidInd);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
   
    public function update(Request $request, $uuidUsina)
    {
        $this->sistemaCreditoServiceService->defineData($request->all());
        $data = $this->sistemaCreditoServiceService->atualizaCredito($uuidUsina);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], 400);
    }
   
    public function destroy($uuidUsina, $uuidInd)
    {
        $data = $this->sistemaCreditoServiceService->removeCredito($uuidUsina, $uuidInd);

        if ($data['status']) {
            return response()->json(['msg' => $data['msg']]);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
}
