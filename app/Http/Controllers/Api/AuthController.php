<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Usuario;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class AuthController extends Controller
{
	public function login(Request $request)
	{
		$credentials = $request->only('usu_email', 'usu_senha');
		
		$validacao = $this->validaCamposLogin($credentials);

		if ($validacao->fails()) {
            return response()->json($validacao->errors(), 406);
        }

        $token = auth('api')->attempt($credentials);
        dd($token);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        $user->update(['token' => $token]);
        
        return response()->json([
                'status' => 'success',
                'user' => $user,
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);
	}

	public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'usu_email' => 'required|string|email|max:255|unique:users',
            'usu_senha' => 'required|string|min:6',
            'fk_group_id' => 'required|integer'
        ]);

        $user = Usuario::create([
            'name' => $request->name,
            'username' => $request->username,
            'usu_email' => $request->email,
            'usu_senha' => Hash::make($request->password),
            'fk_group_id' => $request->fk_group_id,
        ]);

        $token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function logout()
    {
        Auth::logout(true);
        Auth::invalidate(true);
        // Auth::attempt($credentials);
        return response()->json([
            'status' => 'success',
            'message' => '',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

	private function validaCamposLogin($data)
    {
        $validacao = [
            'usu_email' => 'required|string',
            'usu_senha' => 'required|string',
        ];

        $mensagem = [
            'required' => 'O campo `:attribute` é obrigatório.',
        ];

        return Validator::make($data, $validacao, $mensagem);
    }
}
