<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\Auth\AuthService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class AuthController extends Controller
{
    protected $authService;

    public function __construct(
        AuthService $authService
    )
    {
        $this->authService = $authService;
    }

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
                'usuario' => $user,
                'authorization' => [
                    'token' => $token,
                ]
            ]);
	}

	public function novoUsuario(Request $request)
    {
        $validacao = $this->validaCamposCadastro($request->all());
		
        if ($validacao->fails()) {
            return response()->json(['msg' => $validacao->errors()], 406);
        }
        
        $this->authService->defineData($request->all());
        $data = $this->authService->novoUsuario();

        if ($data['status']) {
            return response()->json($data['data']);
        }
        return response()->json(['msg' => $data['msg']],400);
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

    private function validaCamposCadastro($data)
    {
        $validacao = [
            'usu_nome' => 'required|string|max:255',
            'usu_apelido' => 'required|string|max:255',
            'usu_email' => 'required|string|email|max:255|unique:usuarios',
            'password' => 'required|string|min:6',
            'uuid_gru_id' => 'required|string'
        ];

        $mensagem = [
            'required' => 'O campo `:attribute` é obrigatório.',
        ];

        return Validator::make($data, $validacao, $mensagem);
    }
}