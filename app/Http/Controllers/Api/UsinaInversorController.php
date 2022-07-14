<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Usina\InversorService;

class UsinaInversorController extends Controller
{
    protected $inversorService;

    public function __construct(
        InversorService $inversorService
    )
    {
        $this->inversorService = $inversorService;
    }

    public function index(Request $request, $uuidUsina)
    {
        $data = $this->inversorService->indice($request, $uuidUsina);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }

    public function store(Request $request, $uuidUsina)
    {
        $this->inversorService->defineData($request->all());
        $data = $this->inversorService->novoInversor($uuidUsina);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']], isset($data['http_status']) ? $data['http_status'] : 400);
    }
   
    public function destroy($uuidUsina, $uuid)
    {
        $data = $this->inversorService->removeInversor($uuidUsina, $uuid);

        if ($data['status']) {
            return response()->json(['msg' => $data['msg']]);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
}
