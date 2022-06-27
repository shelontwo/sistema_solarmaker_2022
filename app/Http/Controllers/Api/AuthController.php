<?php

namespace App\Http\Controllers\Api;

use App\Client;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\UserAuth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
	//
	public function login(Request $request)
	{
		$request->validate([
			'email' => 'required|string|email',
			'password' => 'required|string'
		]);
		$user_auth = UserAuth::where('email', $request['email'])->first();
		if (empty($user_auth)) {
			return response(['msg' => 'Não existe conta para este e-mail', 'email' => true], 403);
		} else if (!Hash::check($request['password'], $user_auth->password)) {
			return response(['msg' => 'Senha incorreta!', 'password' => true], 403);
		}
		Auth::login($user_auth);
		$token = Str::random(80);
		$hashed_token = bcrypt($token);
		$user_auth->update(['token' => $hashed_token]);
		return $hashed_token;
	}
	public function register(Request $request)
	{
		$data = $request->all();
		$merge = array_merge($data['address'], $data['client']);
		$merge['cpf'] = str_replace(['.', ',', '/', '-'], '', $merge['cpf']);
		$merge['cnpj'] = str_replace(['.', ',', '/', '-'], '', $merge['cnpj']);
		$merge['phone'] = str_replace(['(', ')', ' ', '-'], '', $merge['phone']);
		$merge['state_registration'] = str_replace(['(', ')', ' ', '-', '/'], '', $merge['state_registration']);
		Validator::make($request->client, [
			'password' => 'required|string|min:6|max:20',
			'password_confirmation' => 'required|string|min:6|max:20',
			'email' => ['email', 'unique:user_auths', 'required', 'string', 'regex:/^(([^<>()\\.,;:ç~\s@"]+(\.[^<>()\\.,;:ç~\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/'],
			'phone' => 'required',
			'person_type' => 'in:pf,pj',
			'name' => 'required|string',
			'birthday' => 'required|date_format:d/m/Y',

		])->validate();

		Validator::make($request->address, [
			'cep' => 'min:9|max:9',
			'street' => 'required|string',
			'city' => 'required|string',
			'state' => 'required|string',
			'neibourhood' => 'required|string',
			'number' => 'required|numeric'
		])->validate();

		if ($merge['number'] <= 0) {
			$merge['number'] = 1;
		}

		if (str_word_count($merge['name']) < 2) {
			return response()->json(['message' => 'Nome inválido'], 403);
		}

		if ($merge['person_type'] == 'pf') {
			if (!$merge['cpf'] || strlen($merge['cpf']) != 11) {
				return response()->json(['message' => 'cpf inválido'], 403);
			}
		} else if ($merge['person_type'] == 'pj') {
			if (!$merge['cnpj'] || strlen($merge['cnpj']) != 14) {
				return response()->json(['message' => 'cnpj inválido'], 403);
			} else if (!$merge['social_name']) {
				return response()->json(['message' => 'Razão Social inválida'], 403);
			} else if (!$merge['state_registration']) {
				return response()->json(['message' => 'Inscrição Estadual inválida'], 403);
			} else if (!$merge['responsible']) {
				return response()->json(['message' => 'Responsável inválido'], 403);
			} else if (!$merge['position']) {
				return response()->json(['message' => 'Cargo inválido'], 403);
			}
		}
		
		$client = Client::create($merge);
		$user = new UserAuth;
		$user->email = $request->client['email'];
		$user->password = bcrypt($request->client['password']);
		$user->save();
		return response()->json([
			'message' => 'Successfully created user!'
		], 201);
	}

	public function user(Request $request)
	{
		$auth_user = UserAuth::where('token', $request['token'])->first();
		if ($auth_user) {
			return response()->json($auth_user->email);
		} else {
			return response()->json(["msg" => "Token Inválido"], 401);
		}
	}

	public function checkEmail(Request $request)
	{
		$validation = $request->validate(['email' => ['email', 'unique:user_auths', 'required', 'string', 'regex:/^(([^<>()\\.,;:ç~\s@"]+(\.[^<>()\\.,;:ç~\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/']]);
	}
}
