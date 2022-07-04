<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\User;
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
		$request->validate([
			'email' => 'required|string|email',
			'password' => 'required|string'
		]);
		
		$credentials = $request->only('email', 'password');
		
		$validacao = $this->validaCamposLogin($credentials);

		if ($validacao->fails()) {
            return response()->json($validacao->errors(), 406);
        }

        $token = Auth::attempt($credentials);
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
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
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
        Auth::logout();
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
            'email' => 'required|string',
            'password' => 'required|string',
        ];

        $mensagem = [
            'required' => 'O campo `:attribute` é obrigatório.',
        ];

        return Validator::make($data, $validacao, $mensagem);
    }
}
