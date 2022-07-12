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

    public function index(Request $request, $uuidIntegrador)
    {
        $data = $this->apiService->indice($request, $uuidIntegrador);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function store(Request $request, $uuidIntegrador)
    {
        $this->apiService->defineData($request->all());
        $data = $this->apiService->novaApi($uuidIntegrador);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], isset($data['http_status']) ? $data['http_status'] : 400);
    }
   
    public function show($uuidIntegrador, $uuidApi)
    {
        $data = $this->apiService->listApi($uuidIntegrador, $uuidApi);

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
   
    public function destroy($uuidIntegrador, $uuidApi)
    {
        $data = $this->apiService->removeApi($uuidIntegrador, $uuidApi);

        if ($data['status']) {
            return response()->json(['msg' => $data['msg']]);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
}
