<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Grupo\GrupoService;

class GrupoController extends Controller
{
    protected $grupoService;

    public function __construct(
        GrupoService $grupoService
    )
    {
        $this->grupoService = $grupoService;
    }

    public function index(Request $request)
    {
        $data = $this->grupoService->indice($request);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function store(Request $request)
    {
        $this->grupoService->defineData($request->all());
        $data = $this->grupoService->novoGrupo();

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], isset($data['http_status']) ? $data['http_status'] : 400);
    }
   
    public function show($uuid)
    {
        $data = $this->grupoService->listGrupo($uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
   
    public function update(Request $request)
    {
        $this->grupoService->defineData($request->all());
        $data = $this->grupoService->atualizaGrupo();

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], 400);
    }
   
    public function destroy($uuid)
    {
        $data = $this->grupoService->removeGrupo($uuid);

        if ($data['status']) {
            return response()->json(['msg' => $data['msg']]);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
}
