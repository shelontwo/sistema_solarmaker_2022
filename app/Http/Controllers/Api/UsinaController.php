<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Usina\UsinaService;

class UsinaController extends Controller
{
    protected $usinaService;

    public function __construct(
        UsinaService $usinaService
    )
    {
        $this->usinaService = $usinaService;
    }

    public function index(Request $request)
    {
        $data = $this->usinaService->indice($request);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function unidade(Request $request, $uuid)
    {
        $data = $this->usinaService->listUsinasUnidade($request, $uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function distribuidor(Request $request, $uuid)
    {
        $data = $this->usinaService->listUsinasDistribuidor($request, $uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function integrador(Request $request, $uuid)
    {
        $data = $this->usinaService->listUsinasIntegrador($request, $uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function cliente(Request $request, $uuid)
    {
        $data = $this->usinaService->listUsinasCliente($request, $uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function store(Request $request)
    {
        $this->usinaService->defineData($request->all());
        $data = $this->usinaService->novaUsina();

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], isset($data['http_status']) ? $data['http_status'] : 400);
    }
   
    public function show($uuid)
    {
        $data = $this->usinaService->listUsina($uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
   
    public function update(Request $request)
    {
        $this->usinaService->defineData($request->all());
        $data = $this->usinaService->atualizaUsina();

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], 400);
    }
   
    public function destroy($uuid)
    {
        $data = $this->usinaService->removeUsina($uuid);

        if ($data['status']) {
            return response()->json(['msg' => $data['msg']]);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
}
