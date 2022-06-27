<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Tickets;
use App\Client;
use App\Helpers\ApiMailHelper;
use App\Ingressos;
use App\PasswordRecovery;
use App\UserAuth;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;

class ClientController extends Controller
{
	public function profile(Request $request)
	{
		$data = $request->all();
		$client = DB::table('user_auths as u')
			->select(
				'c.person_type',
				'c.name',
				'c.email',
				'c.phone',
				'c.cpf',
				'c.birthday',
				'c.cep',
				'c.neibourhood',
				'c.street',
				'c.number',
				'c.complement',
				'c.state',
				'c.city',
				'c.social_name',
				'c.state_registration',
				'c.cnpj',
				'c.position',
				'c.responsible'
			)
			->where('u.token', $data['auth'])
			->join('clients as c', 'c.email', 'u.email')
			->first();

		$client->birthday = date('d/m/Y', strtotime($client->birthday));
		return response()->json($client);
	}

	public function changeProfile(Request $request)
	{
		// ENTRYS >>> {
		// 	"client_token": "$2y$10$4cJ8H26Y5DrUhf44lAR.DuXOy./Tuz.xjaqIBE8Y9AjKjfqh2SlLq",
		// 	"name": "arthur teste",
		// 	"password": "testando",
		// 	"confirm_password": "testando",
		// 	"phone":"(49) 991068375",
		// 	"cpf": "851.486.190-58",
		// 	"state_registration" : null,
		// 	"cnpj":null,
		// 	"birthday":"04/12/1999",
		// 	"cep":"89700-154",
		// 	"street":"Rua do teste gratis",
		// 	"neibourhood":"imigrantes",
		// 	"number":19,
		// 	"complement":"perto do teste",
		// 	"state":"Rio de Janeiro",
		// 	"city":"Rio de Janeiro",
		// 	"social_name":null,
		// 	"responsible":null,
		// 	"position":null
		// }

		$data = $request->all();
		if (isset($data['email'])) {
			unset($data['email']);
		}

		$data['cpf'] = str_replace(['.', ',', '/', '-'], '', $data['cpf']);
		$data['cnpj'] = str_replace(['.', ',', '/', '-'], '', $data['cnpj']);
		$data['phone'] = str_replace(['(', ')', ' ', '-'], '', $data['phone']);
		$data['state_registration'] = str_replace(['(', ')', ' ', '-', '/'], '', $data['state_registration']);
		$user_verify = UserAuth::where('token', $data['client_token'])
			->join('clients as c', 'c.email', 'user_auths.email')
			->select('user_auths.id as user_id', 'user_auths.email', 'c.id as client_id')
			->first();

		if (!empty($user_verify)) {
			if (!$data['name'] || str_word_count($data['name']) < 2) {
				return response()->json(['msg' => 'Preencha com nome e sobrenome!'], 403);
			}

			$request->validate([
				'name' => 'required|string',
				'phone' => 'required|string|min:13|max:15',
				'password' => 'nullable|string|min:6|max:20',
				'cpf' => 'nullable|string|min:14|max:14',
				'cep' => 'required|string|min:9|max:9',
				'cnpj' => 'nullable|string|min:18|max:18',
				'neibourhood' => 'required|string',
				'number' => 'required|numeric',
				'state' => 'required|string',
				'city' => 'required|string',
				'social_name' => 'nullable|string',
				'responsible' => 'nullable|string',
				'position' => 'nullable|string',
				'state_registration' => 'nullable|string',
				'birthday' => 'required|date_format:d/m/Y',
			]);

			$data['birthday'] = date('Y-m-d', strtotime($data['birthday']));

			if ($data['number'] <= 0) {
				$data['number'] = 1;
			}

			if ($data['person_type'] == 'pf') {
				if (!$data['cpf'] || strlen($data['cpf']) != 11) {
					return response()->json(['message' => 'cpf inválido'], 403);
				}
			} else if ($data['person_type'] == 'pj') {
				if (!$data['cnpj'] || strlen($data['cnpj']) != 14) {
					return response()->json(['message' => 'cnpj inválido'], 403);
				} else if (!$data['social_name']) {
					return response()->json(['message' => 'Razão Social inválida'], 403);
				} else if (!$data['state_registration']) {
					return response()->json(['message' => 'Inscrição Estadual inválida'], 403);
				} else if (!$data['responsible']) {
					return response()->json(['message' => 'Responsável inválido'], 403);
				} else if (!$data['position']) {
					return response()->json(['message' => 'Cargo inválido'], 403);
				}
			}

			if (isset($data['confirm_password']) && isset($data['password'])) {
				if ($data['confirm_password'] != $data['password']) {
					return response()->json(['msg' => 'Senhas não conferem!'], 403);
				} else if ($data['confirm_password'] === $data['password']) {
					$data['password'] = bcrypt($data['password']);
					$user = UserAuth::find($user_verify->user_id)->update(['password' => $data['password']]);
					unset($data['password']);
					unset($data['confirm_password']);
				}
			}

			$client = Client::find($user_verify->client_id)->update($data);
			return response()->json($client);
		} else {
			return response()->json(['msg' => 'Usuário inválido'], 404);
		}
	}

	public function retrieveOpenCart(Request $request)
	{
		$data = $request->all();

		if ($data['is_logged']) {
			$client = DB::table('user_auths as u')
				->selectRaw(
					'p.id as p_id,
					p.ticket_id,
					count(p.quantity) as quantity,
					p.total_price,
					t.quantity as t_quantity,
					t.max_sell,
					t.color,
					t.title,
					t.id,
					r.coupon_code,
					r.coupon_value'
				)
				->where('token', $data['client_token'])
				->whereNotIn('r.cart_step_id', [4, 5, 6, 7, 8, 9])
				->join('clients as c', 'c.email', 'u.email')
				->join('carts as r', 'r.client_id', 'c.id')
				->join('cart_products as p', 'p.cart_id', 'r.id')
				->join('tickets as t', 't.id', 'p.ticket_id')
				->groupBy('p.ticket_id')->get();
		} else {
			$client = DB::table('temp_data as d')
				->selectRaw(
					'p.id as p_id,
					p.ticket_id,
					count(p.quantity) as quantity,
					p.total_price,
					t.quantity as t_quantity,
					t.max_sell,
					t.color,
					t.title,
					t.id,
					d.cart_id'
				)
				->where('user_cod', $data['client_token'])
				->whereNotIn('r.cart_step_id', [4, 5, 6, 7, 8, 9])
				->join('carts as r', 'r.id', 'd.cart_id')
				->join('cart_products as p', 'p.cart_id', 'r.id')
				->join('tickets as t', 't.id', 'p.ticket_id')
				->groupBy('p.ticket_id')->get();
		}

		return response()->json($client);
	}

	public function retrieveClosedCart(Request $request)
	{
		$data = $request->all();

		$client = DB::table('user_auths as u')
			->selectRaw(
				'p.id as p_id,
				p.ticket_id,
				p.quantity as quantity,
				p.total_price,
				t.quantity as t_quantity,
				t.max_sell,
				t.title,
				t.color,
				t.id,
				r.cart_step_id,
				r.payment_method_id,
				i.ingresso_nome,
				i.ingresso_codigo,
				i.ingresso_id,
				r.id as carrinho,
				r.payment_link,
				r.pix_link,
				r.pix_qr_code'
			)
			->where('token', $data['client_token'])
			->whereNotIn('r.cart_step_id', [1, 2, 3])
			->join('clients as c', 'c.email', 'u.email')
			->join('carts as r', 'r.client_id', 'c.id')
			->join('cart_products as p', 'p.cart_id', 'r.id')
			->join('tickets as t', 't.id', 'p.ticket_id')
			->leftJoin('ingressos as i', 'i.fk_item_id', 'p.id')
			->get();

		$cart = DB::table('user_auths as u')
			->selectRaw(
				'r.id as carrinho,
			r.payment_link,
			r.pix_link,
			r.pix_qr_code,
			r.cart_step_id,
			r.payment_method_id'
			)
			->where('token', $data['client_token'])
			->whereNotIn('r.cart_step_id', [1, 2, 3])
			->join('clients as c', 'c.email', 'u.email')
			->join('carts as r', 'r.client_id', 'c.id')
			->orderBy('carrinho', 'desc')
			->get();

		return response()->json(["client" => $client, "cart" => $cart]);
	}

	public function retrieveMyCheckIns(Request $request)
	{
		$data = $request->all();
		$auth = json_decode($data['authentication']);
		$tickets = DB::table('ingressos as i')
			->selectRaw(
				't.title,
			t.id,
			t.color,
			i.ingresso_codigo,
			r.id as carrinho,
			c.name'
			)
			->where('i.ingresso_email', $data['email'])
			->where('u.token', $auth->value)
			->whereNotIn('r.cart_step_id', [1, 2, 3])
			->join('cart_products as p', 'p.id', 'i.fk_item_id')
			->join('carts as r', 'r.id', 'p.cart_id')
			->join('clients as c', 'c.id', 'r.client_id')
			->join('user_auths as u', 'u.email', 'c.email')
			->join('tickets as t', 't.id', 'p.ticket_id')
			->get();

		return response()->json($tickets);
	}

	public function searchEmail(Request $request)
	{
		$data = $request->all();
		$request->validate(['email' => ['email', 'required', 'string', 'regex:/^(([^<>()\\.,;:ç~\s@"]+(\.[^<>()\\.,;:ç~\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/']]);
		$client = Client::where('email', $data['email'])->select('name', 'social_name', 'person_type', 'cpf', 'cnpj')->first();
		if ($client) {
			return response()->json($client);
		} else {
			return response()->json(null);
		}
	}

	public function getTickets()
	{
		$tickets = Tickets::where('active', 1)->select('id', 'title', 'price', 'quantity', 'lot', 'quantity as quantity_old')->orderBy('price', 'asc')->get();
		return $tickets;
	}

	private function recoverCode()
	{
		$token = Str::random(60);
		$list_tokens = PasswordRecovery::where('recovery_code', $token)->get();

		if ($list_tokens != "[]") {
			$this->recoverCode();
		} else {
			return $token;
		}
	}

	private function checkRecover($email)
	{
		$verify = PasswordRecovery::where('email', $email)
			->whereDate('created_at', '=', Carbon::today())
			->first();

		if (!empty($verify)) {
			return $verify->recovery_code;
		} else {
			return false;
		}
	}

	public function verifyToken(Request $request)
	{
		$data = $request->all();
		$token_validation = PasswordRecovery::where('recovery_code', $data['token'])
			->whereDate('password_recovery.created_at', '=', Carbon::today())
			->join('user_auths as u', 'u.email', 'password_recovery.email')
			->select('u.id as authentication', 'u.email')
			->first();

		if (!empty($token_validation)) {
			return response()->json($token_validation);
		} else {
			return response()->json("token inválido", 404);
		}
	}

	public function changePassword(Request $request)
	{
		$data = $request->all();
		$account = UserAuth::where('user_auths.id', $data['authentication'])
			->where('user_auths.email', $data['email'])
			->where('p.recovery_code', $data['token'])
			->join('password_recovery as p', 'p.email', 'user_auths.email')
			->select('user_auths.id')
			->first();

		if (empty($account)) {
			return response()->json(['msg' => 'Dados inválidos!'], 405);
		}

		$client_data = Client::where('email', $data['email'])->first();
		$request->validate([
			'password' => 'required|string|min:6|max:20',
		]);

		$data['password'] = bcrypt($data['password']);
		// return response()->json($data['password']);
		$account->update(['password' => $data['password']]);
		$token = PasswordRecovery::where('recovery_code', $data['token'])->first();
		$token->delete();

		return response()->json("Senha alterada com sucesso!");
	}

	public function sendRecover(Request $request)
	{
		$data = $request->all();
		$email_receiver = $data['email'];
		$client_name = Client::where('email', $data['email'])->select('name')->first();
		$check = $this->checkRecover($data['email']);
		if ($check) {
			$code = $check;
		} else {
			$code = $this->recoverCode();
			PasswordRecovery::create(['email' => $data['email'], 'recovery_code' => $code]);
		}
		$recovery_link = route('client.forgot-password') . '?p=' . $code;

		$assunto = "Recuperação de Senha";

		$mensagem = '
        <div><img src="' . asset('/img/header-email.png') . '"></div>
        <div>&nbsp;</div>
        <div style="font-family: Verdana,Arial,Helvetica,sans-serif;color:#4d4d4d">		
		<div>Olá, <b>' . $client_name->name . '</b> esqueceu sua senha? Não tem problema! ;)</div>
		<div>Para redefini-la, você pode clicar <a href="' . $recovery_link . '">nesse link</a>.</div>
		<div>&nbsp;</div>
		<div>Ou, se preferir, copie e cole em seu navegador:</div>
		<div><a href="' . $recovery_link . '">' . $recovery_link . '</a></div>
		<div>&nbsp;</div>
		<div>Após a alteração, sua senha antiga será substituída pela nova. Este link é válido apenas no dia de hoje.</div>
        <div>&nbsp;</div>	
        <div><img style="width: 200px;height: 200px;" src="' . asset('/img/aline-email.png') . '"></div>		
        <div>&nbsp;</div>	
        <div style="font-weight: bold;color: #ec0e51;font-size: 18px;">Aline da Silva</div>
        <div>Gestora</div>
        <div>49 3425-5948 | 49 99802-6025</div>
        <div>Acesse:<a href="' . route('home') . '">@eventohoje</a></div>
        <div>Siga-nos no Instagram <a href="https://www.instagram.com/eventohoje/">@eventohoje</a></div>
        <div>&nbsp;</div>	
        <div>"nunca subestime o impacto que você pode ter na vida de outra pessoa!"</div>
    </div>';

		ApiMailHelper::sendEmail($assunto, $mensagem, $email_receiver);
		return response()->json("E-mail enviado!");
	}

	public function checkCode(Request $request)
	{
		$data = $request->all();
		$code = Ingressos::where('ingresso_codigo', $data['code'])->first();

		if (!empty($code->ingresso_nome)) {
			return response()->json(['error' => 'Este passaporte já tem dono :(']);
		}

		return response()->json(empty($code) ? false : true);
	}

	public function checkin(Request $request)
	{
		$data = $request->all();
		$data = $data['client'];

		$passport = Ingressos::where('ingresso_codigo', $data['code'])->first();
		if (empty($passport)) {
			return response()->json(['error' => 'Este código é inválido']);
		} else {
			$passport->update([
				"ingresso_nome" => $data['name'],
				"ingresso_email" => $data['email'],
				"ingresso_telefone" => $data['phone'],
				"ingresso_cpf" => $data['cpf'],
				"ingresso_estado" => $data['state'],
				"ingresso_cidade" => $data['city'],
				"ingresso_escolaridade" => $data['schooling'],
				"ingresso_cnpj" => $data['cnpj'],
				"ingresso_empresa" => $data['company'],
				"ingresso_cargo" => $data['position'],
				"ingresso_nascimento" => date('Y-m-d', strtotime($data['birthday'])),
				"ingresso_genero" => $data['gender'],
				"ingresso_checkin" => 1,
				"check1" => $data['check1'] ? 1 : 0,
				"check2" => $data['check2'] ? 1 : 0,
				"check3" => $data['check3'] ? 1 : 0
			]);

			$email_receiver = $data['email'];

			$assunto = "Check-in realizado com sucesso";

			$mensagem = '
        <div><img src="' . asset('/img/header-email.png') . '"></div>
        <div>&nbsp;</div>
        <div style="font-family: Verdana,Arial,Helvetica,sans-serif;color:#4d4d4d">
		<div>Olá, <b>' . $data['name'] . '</b>, seu check-in foi realizado com sucesso e você está oficialmente autorizado para o embarque!.</div>
        <div>&nbsp;</div>
        <div>Agora é só se preparar e tentar conter o entusiasmo, é claro!</div>
        <div>Mas precisamos admitir que, a cada novo check-in, a gente fica ainda mais animado.</div>
        <div>Estamos prontos para decolar, daqui a pouco, a viagem começa. Prepare-se!</div>
        <div>Não poupe energia nestes dias insanos de evento, aproveite ao máximo.</div>
        <div>Não se esqueça: no dia do evento, você ainda precisa fazer o seu credenciamento, mas vai ser coisa rápida, então nem se preocupe.</div>
		<div>&nbsp;</div>
		<div>Dados do seu Passaporte:</div>
		<div>Código do Passaporte: ' . $data['code'] . '</div>
		<div>Nome completo: ' . $data['name'] . '</div>
		<div>Gênero: ' . $data['gender'] . '</div>
		<div>Data de nascimento: ' . $data['birthday'] . '</div>
		<div>CPF: ' . $data['cpf'] . '</div>
		<div>E-mail: ' . $data['email'] . '</div>
		<div>Telefone: ' . $data['phone'] . '</div>
		<div>Cidade: ' . $data['city'] . '</div>
		<div>Estado: ' . $data['state'] . '</div>
		<div>Empresa: ' . $data['company'] . '</div>
		<div>Cargo: ' . $data['position'] . '</div>
		<div>CNPJ: ' . $data['cnpj'] . '</div>
		<div>Escolaridade: ' . $data['schooling'] . '</div>
        <div>&nbsp;</div>	
		<div>Um grande abraço e nos vemos na missão HJ21!</div>
        <div>&nbsp;</div>	
        <div><img style="width: 200px;height: 200px;" src="' . asset('/img/aline-email.png') . '"></div>		
        <div>&nbsp;</div>	
        <div style="font-weight: bold;color: #ec0e51;font-size: 18px;">Aline da Silva</div>
        <div>Gestora</div>
        <div>49 3425-5948 | 49 99802-6025</div>
        <div>Acesse:<a href="' . route('home') . '">@eventohoje</a></div>
        <div>Siga-nos no Instagram <a href="https://www.instagram.com/eventohoje/">@eventohoje</a></div>
        <div>&nbsp;</div>	
        <div>"nunca subestime o impacto que você pode ter na vida de outra pessoa!"</div>
    </div>';

			ApiMailHelper::sendEmail($assunto, $mensagem, $email_receiver);

			$tags = 'hj-realizou-checkin';
			$firstname = $data['name'];
			ApiMailHelper::sendFunil($tags, $firstname, $email_receiver);


			return response()->json("Chekin feito");
		}
	}

	public function getIngressos(Request $request)
	{
		// ENTRYS >>> 
		// {
		// 	"token":"$2y$10$4cJ8H26Y5DrUhf44lAR.DuXOy./Tuz.xjaqIBE8Y9AjKjfqh2SlLq",
		// 	"email":"arthur@teste.com"
		// }

		$data = $request->all();
		$ingressos = UserAuth::where('token', $data["token"])
			->where('email', $data['email'])
			->join('ingressos as i', 'i.ingresso_email', 'user_auths.email')
			->join('cart_products as c', 'c.id', 'i.fk_item_id')
			->join('tickets as t', 't.id', 'c.ticket_id')
			->select('t.title', 't.lot', 'i.ingresso_codigo')
			->get();
		return response()->json($ingressos);
	}

	public function removeCheckin(Request $request)
	{
		$data = $request->all();
		$temp = json_decode($data['authentication']);
		$ticket = Ingressos::where('ingresso_id', $data['id'])
			->where('u.token', $temp->value)
			->join('cart_products as c', 'c.id', 'ingressos.fk_item_id')
			->join('carts as cs', 'cs.id', 'c.cart_id')
			->join('clients as cl', 'cl.id', 'cs.client_id')
			->join('user_auths as u', 'u.email', 'cl.email')
			->select('ingressos.*')
			->first();

		if (empty($ticket)) {
			return response()->json(['msg' => 'Desculpe, mas este passaporte não existe.']);
		} else {
			$ticket->update([
				"ingresso_nome" => "",
				"ingresso_email" => "",
				"ingresso_telefone" => "",
				"ingresso_cpf" => "",
				"ingresso_estado" => "",
				"ingresso_cidade" => "",
				"ingresso_escolaridade" => "",
				"ingresso_vinculo_educacional" => "",
				"ingresso_cnpj" => "",
				"ingresso_empresa" => "",
				"ingresso_cargo" => ""
			]);
			return response()->json(['msg' => 'Removido o check-in deste passaporte.']);
		}
	}
}
