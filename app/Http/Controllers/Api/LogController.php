<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Log;

class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $logs = Log::paginate();
            return response()->json($logs, 200);
        } catch (\Throwable $th) {
            return response()->json(['msg' => 'Erro ao buscar logs'], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $log = Log::find($id);
            
            if ($log) {
                return response()->json($log, 200);
            }
            return response()->json(['msg' => 'Log não encontrado'], 406);
        } catch (\Throwable $th) {
            return response()->json(['msg' => 'Erro ao buscar logs'], 400);
        }
    }
}