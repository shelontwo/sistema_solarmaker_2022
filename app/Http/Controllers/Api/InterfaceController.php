<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Interfaces\InterfaceService;

class InterfaceController extends Controller
{
    protected $interfaceService;

    public function __construct(
        InterfaceService $interfaceService
    )
    {
        $this->interfaceService = $interfaceService;
    }

    public function index(Request $request)
    {
        $data = $this->interfaceService->indice($request);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function store(Request $request)
    {
        $this->interfaceService->defineData($request->all());
        $data = $this->interfaceService->novoInterface();

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], isset($data['http_status']) ? $data['http_status'] : 400);
    }
   
    public function show($uuid)
    {
        $data = $this->interfaceService->listInterface($uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
   
    public function update(Request $request)
    {
        $this->interfaceService->defineData($request->all());
        $data = $this->interfaceService->atualizaInterface();

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], 400);
    }
   
    public function destroy($uuid)
    {
        $data = $this->interfaceService->removeInterface($uuid);

        if ($data['status']) {
            return response()->json(['msg' => $data['msg']]);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
}
