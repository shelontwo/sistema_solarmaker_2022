<?php

namespace App\Http\Controllers\Api;

use App\Models\Grupo;
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
        $user->permissoes = $this->permissoes($user->fk_gru_id_grupo);
        return response()->json([
            'status' => 'success',
            'usuario' => $user,
            'authorization' => [
                'token' => $token,
            ]
        ]);
	}

    private function permissoes($grupoId)
    {
        $permissoes = [];
        $grupo = Grupo::find($grupoId);

        foreach ($grupo->modulos as $modulo) {
           $nome = $this->createSlug($modulo->mod_nome);
           $permissoes[] = $nome . ':visualizar';
        }
        return $permissoes;
    }

    public static function createSlug($string) {

        $table = array(
                'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
                'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
                'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
                'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
                'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
                'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
                'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r', '/' => '-', ' ' => '-'
        );
    
        // -- Remove duplicated spaces
        $stripped = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $string);
    
        // -- Returns the slug
        return strtolower(strtr($string, $table));
    
    
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
            'usu_data_referencia' => 'date|nullable',
            'usu_dias_expiracao' => 'integer|nullable',
            'uuid_gru_id' => 'required|string|uuid',
            'uuid_int_id' => 'string|uuid',
            'uuid_dis_id' => 'string|uuid',
        ];

        return Validator::make($data, $validacao);
    }
}