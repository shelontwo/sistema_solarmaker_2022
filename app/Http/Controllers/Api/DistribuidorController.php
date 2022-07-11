<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Distribuidor\DistribuidorService;

class DistribuidorController extends Controller
{
    protected $distribuidorService;

    public function __construct(
        DistribuidorService $distribuidorService
    )
    {
        $this->distribuidorService = $distribuidorService;
    }

    public function index(Request $request)
    {
        $data = $this->distribuidorService->indice($request);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $logout['msg']],400);
    }

    public function store(Request $request)
    {
        $this->distribuidorService->defineData($request->all());
        $data = $this->distribuidorService->novoDistribuidor();

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], isset($data['http_status']) ? $data['http_status'] : 400);
    }
   
    public function show($uuid)
    {
        $data = $this->distribuidorService->listDistribuidor($uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $logout['msg']],400);
    }
   
    public function update(Request $request)
    {
        $this->distribuidorService->defineData($request->all());
        $data = $this->distribuidorService->atualizaDistribuidor();

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], 400);
    }
   
    public function destroy($uuid)
    {
        $data = $this->distribuidorService->removeDistribuidor($uuid);

        if ($data['status']) {
            return response()->json(['msg' => $data['msg']]);
        }
        return response()->json(['msg' => $logout['msg']],400);
    }
}
