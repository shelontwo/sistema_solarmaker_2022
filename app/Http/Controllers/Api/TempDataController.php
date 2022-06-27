<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TempDataController extends Controller
{
    public function hashGenerator(Request $request){
        $token = Str::random(80);
        $hashed_token = bcrypt($token);
        return $hashed_token;
    } 
}
