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
		$credentials = $request->only('usu_email', 'password');
		
		$validacao = $this->validaCamposLogin($credentials);

		if ($validacao->fails()) {
            return response()->json($validacao->errors(), 406);
        }

        $token = auth('api')->attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        $user->update(['usu_token' => $token]);
        
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
            'password' => 'required|string|min:6',
            'fk_group_id' => 'required|integer'
        ]);

        $user = Usuario::create([
            'name' => $request->name,
            'username' => $request->username,
            'usu_email' => $request->email,
            'password' => Hash::make($request->password),
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
        auth('api')->logout();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
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
            'password' => 'required|string',
        ];

        $mensagem = [
            'required' => 'O campo `:attribute` é obrigatório.',
        ];

        return Validator::make($data, $validacao, $mensagem);
    }
}