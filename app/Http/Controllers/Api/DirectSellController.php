<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Helpers\Asaas;
use App\Helpers\TicketsGenerator;
use App\Tickets;
use App\TempData;
use App\Cart;
use App\CartLog;
use App\CartProducts;
use App\Configurations;
use App\Ingressos;
use App\Client;
use App\Coupon;
use App\UserAuth;
use App\DirectSell;
use App\Helpers\ApiMailHelper;

class DirectSellController extends Controller
{
	public function save(Request $request)
	{
		$data = $request->all();
		$check_ticket = Tickets::where('id', $data['item']['id'])->first();
		if (isset($data['item']['sell'])) {
			$sell_verify = DirectSell::where('ticket_id', $data['item']['id'])->where('seller_id', (int)$data['item']['seller_id'])->where('sell_step_id', 1)->first();
			if ((int)$data['item']['quantity_to_sell'] > $sell_verify['quantity']) {
				$extra = (int)$data['item']['quantity_to_sell'] - $sell_verify['quantity'];
				if ($extra > $check_ticket->quantity) {
					return response()->json(['error' => 1], 500);
				} else {
					$sell_verify->update(['quantity' => $sell_verify['quantity'] + $extra]);
					$check_ticket->update(['quantity' => $check_ticket->quantity - $extra]);
					return response(200);
				}
			} else {
				$extra = $sell_verify['quantity'] - (int)$data['item']['quantity_to_sell'];
				$sell_verify->update(['quantity' => $sell_verify['quantity'] - $extra]);
				$check_ticket->update(['quantity' => $check_ticket->quantity + $extra]);
				return response(200);
			}
		} else {
			if ($data['item']['quantity_to_sell'] > $check_ticket->quantity) {
				return response()->json(['error' => 1], 500);
			} else {
				$direct_sell_second_check = DirectSell::where('seller_id', $data['item']['seller_id'])->where('sell_step_id', 1)->where('ticket_id', $data['item']['id'])->first();
				if (!empty($direct_sell_second_check)) {
					$extra = (int)$data['item']['quantity_to_sell'] - $direct_sell_second_check['quantity'];
					$check_ticket->update(['quantity' => $check_ticket->quantity - $extra]);
					$direct_sell_second_check->update(['quantity' => $direct_sell_second_check->quantity + $extra]);
				} else {
					$create_sell = DirectSell::create(['ticket_id' => $check_ticket->id, 'seller_id' => (int)$data['item']['seller_id'], 'sell_step_id' => 1, 'quantity' => $data['item']['quantity_to_sell'], 'price' => $check_ticket->price]);
					$check_ticket->update(['quantity' => $check_ticket->quantity - $data['item']['quantity_to_sell']]);
				}
				return response()->json(200);
			}
		}
	}

	public function ongoingSell(Request $request)
	{
		$data = $request->all();
		$sell_atm = DirectSell::where('seller_id', $data['user'])->where('sell_step_id', 1)->get();
		return response()->json($sell_atm);
	}

	public function getIngressos(Request $request)
	{
		$data = $request->all();
		$ingressos = CartProducts::join('ingressos as i', 'i.fk_item_id', 'cart_products.id')
			->join('tickets as t', 't.id', 'cart_products.ticket_id')
			->join('carts as c', 'c.id', 'cart_products.cart_id')
			->where('cart_products.cart_id', $data['cart_id'])
			->select(
				'i.ingresso_id as id',
				'i.ingresso_codigo as codigo',
				'i.ingresso_email as email',
				'i.ingresso_nome as name',
				't.title as ticket_name',
				'cart_products.total_price as price',
				'c.description as observations',
				'c.cart_step_id as step'
			)
			->get();
		return response()->json($ingressos);
	}

	public function getTickets(Request $request)
	{
		$data = $request->all();
		$tickets = Tickets::where('active', 1)->where('event_id', $data['year'])->select('id', 'title', 'price', 'quantity', 'lot', 'quantity as quantity_old')->orderBy('price', 'asc')->get();
		return $tickets;
	}

	public function getSells(Request $request)
	{
		$data = $request->all();
		$sells_count = DirectSell::join('carts as c', 'c.id', 'direct_sells.cart_id')
			->join('clients as s', 's.id', 'c.client_id')
			->join('users as u', 'u.id', 'seller_id')
			->join('tickets as t', 't.id', 'direct_sells.ticket_id')
			->leftJoin('event_years as e', 'e.id', 'c.event_id')
			->where(function ($query) use ($data) {
				if (!empty($data['seller'])) {
					$query->where('seller_id', $data['seller']);
				}
			})
			->where(function ($query) use ($data) {
				if (!empty($data['search'])) {
					$query->where('s.email', 'LIKE', "%" . $data['search'] . "%");
					$query->orWhere('e.year', 'LIKE', "%" . $data['search'] . "%");
					$query->orWhere('c.id', 'LIKE', "%" . $data['search'] . "%");
					$query->orWhere('s.name', 'LIKE', "%" . $data['search'] . "%");
				}
			})

			->select(
				'c.id as order_id',
				's.email as client_email',
				's.name as client_name',
				'direct_sells.created_at as created',
				'direct_sells.price',
				'direct_sells.quantity',
				'direct_sells.seller_id',
				'direct_sells.sell_step_id',
				'direct_sells.cart_id as cart_id',
				'u.name as seller_name',
				't.price as ticket_price',
				'c.cart_step_id as step'
			)
			->where('sell_step_id', '!=', 1)
			->orderBy('step','asc')->orderBy('order_id','desc')
			->get()
			->groupBy('cart_id');


		$sells_count = count($sells_count);
		return response()->json($sells_count);
	}

	public function pagination(Request $request)
	{
		$data = $request->all();
		$endPage = $data["page"] * 10;
		$firstPage = $endPage - 10;
		$direct_sells = DirectSell::join('carts as c', 'c.id', 'direct_sells.cart_id')
			->join('clients as s', 's.id', 'c.client_id')
			->join('users as u', 'u.id', 'seller_id')
			->join('tickets as t', 't.id', 'direct_sells.ticket_id')
			->leftJoin('event_years as e', 'e.id', 'c.event_id')
			->where(function ($query) use ($data) {
				if (!empty($data['seller'])) {
					$query->where('seller_id', $data['seller']);
				}
			})
			->where(function ($query) use ($data) {
				if (!empty($data['search'])) {
					$query->where('s.email', 'LIKE', "%" . $data['search'] . "%");
					$query->orWhere('e.year', 'LIKE', "%" . $data['search'] . "%");
					$query->orWhere('c.id', 'LIKE', "%" . $data['search'] . "%");
					$query->orWhere('s.name', 'LIKE', "%" . $data['search'] . "%");
				}
			})
			->select(
				'c.id as order_id',
				's.email as client_email',
				's.name as client_name',
				'direct_sells.created_at as created',
				'direct_sells.price',
				'direct_sells.quantity',
				'direct_sells.seller_id',
				'direct_sells.sell_step_id',
				'direct_sells.cart_id as cart_id',
				'u.name as seller_name',
				't.price as ticket_price',
				'c.cart_step_id as step'
			)
			->where('sell_step_id', '!=', 1)
			->orderBy('step','asc')->orderBy('order_id','desc')
			->get()
			->groupBy('cart_id')
			->skip($firstPage)
			->take(10);


		foreach ($direct_sells as $sell) {
			for ($i = 0; $i < count($sell); $i++) {
				$sell[0]->total_quantity += $sell[$i]->quantity;
				$sell[0]->total_price += $sell[$i]->ticket_price * $sell[$i]->quantity;
			}
		}
		return response()->json($direct_sells);
	}

	public function sellConfirm(Request $request)
	{
		$data = $request->all();
		$data_to_send = Cart::where('carts.id', $data['cart_id'])->join('clients as c', 'c.id', 'carts.client_id')->select('email', 'name')->first();
		for ($i = 0; $i < count($data['itens']); $i++) {
			$ingressos = Ingressos::find($data['itens'][$i]['id']);
			if (!$ingressos->ingresso_codigo) {
				$ingressos->update(['ingresso_codigo' => TicketsGenerator::generateTicketCode()]);
			}
		}
		$cart = Cart::find($data['cart_id']);
		$cart->update(['cart_step_id' => 5, 'payment_method' => 'DIRECT_SELL', 'payment_method_id' => 4]);
		$log = CartLog::create(['cart_id' => $data['cart_id'], 'message' => 'Pagamento Recebido pelo venda direta, alterado status para <strong>Pagamento Confirmado</strong>']);

		$email_receiver = $data_to_send->email;

		$assunto = "Recebemos o seu pagamento";

		$mensagem = '
	<div><img src="' . asset('/img/header-email.png') . '"></div>
	<div>&nbsp;</div>
	<div style="font-family: Verdana,Arial,Helvetica,sans-serif;color:#4d4d4d">
	<div><b>' . $data_to_send->name . '</b></div>
	<div>&nbsp;</div>
	<div>O seu pedido <b>' . $cart->id . '</b> teve o pagamento confirmado com sucesso.</div>
	<div>&nbsp;</div>
	<div><b>Seu próximo passo:</b>
	<div>Para melhorar ainda mais a sua experiência, você deve fazer o seu check-in no evento.</div>
	<div>O Check-in confirma as suas informações (ou da pessoa que usará o passaporte) e facilita o seu acesso ao evento.<div>
	<div>&nbsp;</div>
	<div><b>Para fazer, é rapidinho:</b></div>
	<div>1º <a href="' . route('client.checkin') . '">Clique aqui</a> e insira o código do passaporte;</div>
	<div>2º Preencha os dados <b> da pessoa que usará o passaporte;</b></div>
	<div>3º Clique em “FAZER CHECK-IN”;</div>
	<div>&nbsp;</div>
	<div>Pronto, check-in feito e economia de tempo no Evento. <3</div>
	<div>&nbsp;</div>
	<div>Entre em contato caso tiver quaisquer dúvidas sobre o evento. </div>
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

		$ticket_list = CartProducts::where('cart_id', $cart->id)
			->join('tickets as t', 't.id', 'cart_products.ticket_id')
			->join('event_years as e', 'e.id', 't.event_id')
			->select('cart_products.ticket_id', 't.title', 'e.year')
			->groupBy('cart_products.ticket_id')
			->get();

		$tags = "";
		for ($i = 0; $i < count($ticket_list); $i++) {
			if ($i == 0) {
				$tags = 'hj-pagou-' . $ticket_list[$i]->ticket_id . 'ID' . '-' . $ticket_list[$i]->title . '-' . $ticket_list[$i]->year;
			} else {
				$tags = $tags . ',hj-pagou-' . $ticket_list[$i]->ticket_id . 'ID' . '-' . $ticket_list[$i]->title . '-' . $ticket_list[$i]->year;
			}
		}

		$firstname = $data_to_send->name;
		ApiMailHelper::sendFunil($tags, $firstname, $email_receiver);
	}

	public function sellCancel(Request $request)
	{
		$data = $request->all();
		$data_to_send = Cart::where('carts.id', $data['cart_id'])->join('clients as c', 'c.id', 'carts.client_id')->select('email', 'name')->first();
		for ($i = 0; $i < count($data['itens']); $i++) {
			$ingressos = Ingressos::find($data['itens'][$i]['id']);

			$ticket_to_change = CartProducts::join('ingressos as i', 'i.fk_item_id', 'cart_products.id')
				->where('i.ingresso_id', $data['itens'][$i]['id'])
				->select('ticket_id as id')
				->first();
			$ticket = Tickets::find($ticket_to_change->id);

			$ticket->update(['quantity' => $ticket->quantity + 1]);

			$ingressos->update(['ingresso_codigo' => null, 'ingresso_nome' => null, 'ingresso_email' => null,'ingresso_checkin' => 0]);
		}
		$cart = Cart::find($data['cart_id']);
		$cart->update(['cart_step_id' => 7]);
		$log = CartLog::create(['cart_id' => $data['cart_id'], 'message' => 'Carrinho <strong>cancelado</strong> manualmente']);


		$email_receiver = $data_to_send->email;

		$assunto = "Seu pagamento foi cancelado";

		$mensagem = '
        <div><img src="' . asset('/img/header-email.png') . '"></div>
        <div>&nbsp;</div>
        <div style="font-family: Verdana,Arial,Helvetica,sans-serif;color:#4d4d4d">		
        <div>Olá, <b>' . $data_to_send->name . '</b>.</div>
        <div>&nbsp;</div>
        <div>O seu pedido de número <b>' . $data['cart_id'] . '</b> para o HOJE 2021, teve o pagamento cancelado. :/</div>
        <div>&nbsp;</div>
        <div>Você pode efetuar a compra de novos passaportes através  <a href="' . route('client.cart') . '">desse link</a>.</div>
        <div>&nbsp;</div>
        <div>Caso tenha dúvidas ou não sabe porque estou lhe enviando este e-mail, me responda e vamos conversar. <3</div>
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

		$ticket_list = CartProducts::where('cart_id', $data['cart_id'])
			->join('tickets as t', 't.id', 'cart_products.ticket_id')
			->join('event_years as e', 'e.id', 't.event_id')
			->select('cart_products.ticket_id', 't.title', 'e.year')
			->groupBy('cart_products.ticket_id')
			->get();

		$tags = "";
		for ($i = 0; $i < count($ticket_list); $i++) {
			if ($i == 0) {
				$tags = 'hj-cancelou-' . $ticket_list[$i]->ticket_id . 'ID' . '-' . $ticket_list[$i]->title . '-' . $ticket_list[$i]->year;
			} else {
				$tags = $tags . ',hj-cancelou-' . $ticket_list[$i]->ticket_id . 'ID' . '-' . $ticket_list[$i]->title . '-' . $ticket_list[$i]->year;
			}
		}

		$firstname = $data_to_send->name;
		ApiMailHelper::sendFunil($tags, $firstname, $email_receiver);
	}
}
