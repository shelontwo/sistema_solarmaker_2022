<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Speakers;


class SpeakerController extends Controller
{
    public function list(Request $request)
    {
        $dados = $request->all();

        $palestrantes = Speakers::where('active', true)->get();

        if ($palestrantes->count() > 0) {

            $retorno['palestrantes'] = [];

            foreach ($palestrantes as $key => $value) {
                $retorno['palestrantes'][] = ['nome' => $value->name];
            }

            return response()->json($retorno, 200);
        } else {
            return response()->json('Nenhum palestrante encontrado!', 406);
        }
    }
}
