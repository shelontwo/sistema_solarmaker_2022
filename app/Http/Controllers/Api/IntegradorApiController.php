<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Integrador\ApiService;

class IntegradorApiController extends Controller
{
    protected $apiService;

    public function __construct(
        ApiService $apiService
    )
    {
        $this->apiService = $apiService;
    }

    public function index(Request $request)
    {
        $data = $this->apiService->indice($request);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function distribuidor(Request $request, $uuid)
    {
        $data = $this->apiService->listApisDistribuidor($request, $uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function integrador(Request $request, $uuid)
    {
        $data = $this->apiService->listApisIntegrador($request, $uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function store(Request $request)
    {
        $this->apiService->defineData($request->all());
        $data = $this->apiService->novaApi();

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], isset($data['http_status']) ? $data['http_status'] : 400);
    }
   
    public function show($uuid)
    {
        $data = $this->apiService->listApi($uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
   
    public function update(Request $request)
    {
        $this->apiService->defineData($request->all());
        $data = $this->apiService->atualizaApi();

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], 400);
    }
   
    public function destroy($uuid)
    {
        $data = $this->apiService->removeApi($uuid);

        if ($data['status']) {
            return response()->json(['msg' => $data['msg']]);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
}
