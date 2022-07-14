<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Chamado\ComentarioService;

class ChamadoComentarioController extends Controller
{
    protected $comentarioService;

    public function __construct(
        ComentarioService $comentarioService
    )
    {
        $this->comentarioService = $comentarioService;
    }

    public function index(Request $request, $uuidUsina)
    {
        $data = $this->comentarioService->indice($request, $uuidUsina);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function store(Request $request, $uuidUsina)
    {
        $this->comentarioService->defineData($request->all());
        $data = $this->comentarioService->novoComentario($uuidUsina);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], isset($data['http_status']) ? $data['http_status'] : 400);
    }
   
    public function show($uuidUsina, $uuid)
    {
        $data = $this->comentarioService->listComentario($uuidUsina, $uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
   
    public function update(Request $request, $uuidUsina)
    {
        $this->comentarioService->defineData($request->all());
        $data = $this->comentarioService->atualizaComentario($uuidUsina);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], 400);
    }
   
    public function destroy($uuidUsina, $uuid)
    {
        $data = $this->comentarioService->removeComentario($uuidUsina, $uuid);

        if ($data['status']) {
            return response()->json(['msg' => $data['msg']]);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
}
