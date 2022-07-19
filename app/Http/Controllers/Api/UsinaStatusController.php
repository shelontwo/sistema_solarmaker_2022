<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Usina\StatusService;

class UsinaStatusController extends Controller
{
    protected $statusService;

    public function __construct(
        StatusService $statusService
    )
    {
        $this->statusService = $statusService;
    }

    public function index(Request $request)
    {
        $data = $this->statusService->indice($request);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function store(Request $request)
    {
        $this->statusService->defineData($request->all());
        $data = $this->statusService->novoStatus();

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], isset($data['http_status']) ? $data['http_status'] : 400);
    }
   
    public function show($uuid)
    {
        $data = $this->statusService->listStatus($uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
   
    public function update(Request $request)
    {
        $this->statusService->defineData($request->all());
        $data = $this->statusService->atualizaStatus();

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], 400);
    }
   
    public function destroy($uuid)
    {
        $data = $this->statusService->removeStatus($uuid);

        if ($data['status']) {
            return response()->json(['msg' => $data['msg']]);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
}
