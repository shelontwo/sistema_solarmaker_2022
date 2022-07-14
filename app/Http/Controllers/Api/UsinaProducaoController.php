<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Usina\ProducaoService;

class UsinaProducaoController extends Controller
{
    protected $producaoService;

    public function __construct(
        ProducaoService $producaoService
    )
    {
        $this->producaoService = $producaoService;
    }

    public function index(Request $request, $uuidUsina)
    {
        $data = $this->producaoService->indice($request, $uuidUsina);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function diaria(Request $request, $uuidUsina)
    {
        $data = $this->producaoService->listProducoes($request, $uuidUsina, 1);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function instantanea(Request $request, $uuidUsina)
    {
        $data = $this->producaoService->listProducoes($request, $uuidUsina, 2);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function store(Request $request, $uuidUsina)
    {
        $this->producaoService->defineData($request->all());
        $data = $this->producaoService->novaProducao($uuidUsina);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], isset($data['http_status']) ? $data['http_status'] : 400);
    }
   
    public function show($uuidUsina, $uuidProd)
    {
        $data = $this->producaoService->listProducao($uuidUsina, $uuidProd);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
   
    public function update(Request $request, $uuidUsina)
    {
        $this->producaoService->defineData($request->all());
        $data = $this->producaoService->atualizaProducao($uuidUsina);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], 400);
    }
   
    public function destroy($uuidUsina, $uuidProd)
    {
        $data = $this->producaoService->removeProducao($uuidUsina, $uuidProd);

        if ($data['status']) {
            return response()->json(['msg' => $data['msg']]);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
}
