<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Usina\ProjetoService;

class UsinaProjetoController extends Controller
{
    protected $projetoService;

    public function __construct(
        ProjetoService $projetoService
    )
    {
        $this->projetoService = $projetoService;
    }
   
    public function show($uuidUsina)
    {
        $data = $this->projetoService->listProjeto($uuidUsina);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
   
    public function update(Request $request, $uuidUsina)
    {
        $this->projetoService->defineData($request->all());
        $data = $this->projetoService->atualizaProjeto($uuidUsina);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], 400);
    }
}
