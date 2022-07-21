<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Configuracao\ConfiguracaoService;

class ConfiguracaoController extends Controller
{
    protected $configuracaoService;

    public function __construct(
        ConfiguracaoService $configuracaoService
    )
    {
        $this->configuracaoService = $configuracaoService;
    }

   
    public function show($uuid)
    {
        $data = $this->configuracaoService->listConfiguracao($uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
   
    public function update(Request $request)
    {
        $this->configuracaoService->defineData($request->all());
        $data = $this->configuracaoService->atualizaConfiguracao();

        if ($data['status']) {
            return response()->json(['msg' => $data['msg']]);
        }
        return response()->json(['msg' => $data['msg']], 400);
    }
}
