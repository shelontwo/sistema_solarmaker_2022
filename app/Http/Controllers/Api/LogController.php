<?php

namespace App\Http\Controllers\Api;

use App\Models\Log;
use Illuminate\Http\Request;
use App\Services\Log\LogService;
use App\Http\Controllers\Controller;
class LogController extends Controller
{
    protected $logService;

    public function __construct(
        LogService $logService
    )
    {
        $this->logService = $logService;
    }

    public function index(Request $request)
    {
        $data = $this->logService->indice($request);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
    
    public function show($uuid)
    {
        $data = $this->logService->listaLog($uuid);

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
    }
}