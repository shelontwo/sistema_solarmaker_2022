<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Usuario\UsuarioService;

class UsuarioController extends Controller
{
    protected $usuarioService;

    public function __construct(
        UsuarioService $usuarioService
    )
    {
        $this->usuarioService = $usuarioService;
    }

    public function index(Request $request)
    {
        $data = $this->usuarioService->indice($request);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $logout['msg']],400);
    }

    public function master(Request $request)
    {
        $data = $this->usuarioService->listUsuariosMaster($request);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $logout['msg']],400);
    }

    public function distribuidor(Request $request, $uuid)
    {
        $data = $this->usuarioService->listUsuariosDistribuidor($request, $uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $logout['msg']],400);
    }

    public function integrador(Request $request, $uuid)
    {
        $data = $this->usuarioService->listUsuariosIntegrador($request, $uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $logout['msg']],400);
    }

    public function store(Request $request)
    {
        $this->usuarioService->defineData($request->all());
        $data = $this->usuarioService->novoUsuario();

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], isset($data['http_status']) ? $data['http_status'] : 400);
    }
   
    public function show($uuid)
    {
        $data = $this->usuarioService->listUsuario($uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $logout['msg']],400);
    }
   
    public function update(Request $request)
    {
        $this->usuarioService->defineData($request->all());
        $data = $this->usuarioService->atualizaUsuario();

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], 400);
    }
   
    public function destroy($uuid)
    {
        $data = $this->usuarioService->removeUsuario($uuid);

        if ($data['status']) {
            return response()->json(['msg' => $data['msg']]);
        }
        return response()->json(['msg' => $logout['msg']],400);
    }
}