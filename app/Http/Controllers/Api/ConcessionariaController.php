<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Concessionaria\ConcessionariaService;

class ConcessionariaController extends Controller
{
    protected $concessionariaService;

    public function __construct(
        ConcessionariaService $concessionariaService
    )
    {
        $this->concessionariaService = $concessionariaService;
    }

    public function index(Request $request)
    {
        $data = $this->concessionariaService->indice($request);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function store(Request $request)
    {
        $this->concessionariaService->defineData($request->all());
        $data = $this->concessionariaService->novoConcessionaria();

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], isset($data['http_status']) ? $data['http_status'] : 400);
    }
   
    public function show($uuid)
    {
        $data = $this->concessionariaService->listConcessionaria($uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
   
    public function update(Request $request)
    {
        $this->concessionariaService->defineData($request->all());
        $data = $this->concessionariaService->atualizaConcessionaria();

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], 400);
    }
   
    public function destroy($uuid)
    {
        $data = $this->concessionariaService->removeConcessionaria($uuid);

        if ($data['status']) {
            return response()->json(['msg' => $data['msg']]);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
}
