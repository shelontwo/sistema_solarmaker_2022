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
		$credentials = $request->only('usu_apelido', 'usu_email', 'password');
		
		$validacao = $this->validaCamposLogin($credentials);

		if ($validacao->fails()) {
            return response()->json($validacao->errors(), 406);
        }

        $token = auth('api')->attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dados de login incorretos',
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

	public function store(Request $request)
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
            'message' => 'Deslogado com sucesso',
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
            'usu_apelido' => 'required_if:usu_email,""|string',
            'usu_email' => 'required_if:usu_apelido,""|string',
            'password' => 'required|string',
        ];

        return Validator::make($data, $validacao);
    }

    private function validaCamposCadastro($data)
    {
        $validacao = [
            'usu_apelido' => 'required|string|max:255|unique:usuarios',
            'usu_nome' => 'required|string|max:255',
            'usu_email' => 'string|email|max:255|unique:usuarios',
            'password' => 'required|string|min:6',
            'usu_data_referencia' => 'date',
            'usu_dias_expiracao' => 'integer',
            'uuid_gru_id' => 'required|string',
            'uuid_int_id' => 'required|string'
        ];

        return Validator::make($data, $validacao);
    }
}