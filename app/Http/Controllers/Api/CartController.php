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
use App\DirectSell;
use App\Helpers\ApiMailHelper;
use App\PasswordRecovery;
use App\UserAuth;
use DateTime;

//ORDEM DAS API'S: CREATE >> (SUM/SUB) >> (ATTRIBUTION/CANCEL/TIMEOUT) >> ASAAS >> WEBHOOK

class CartController extends Controller
{
    public function create(Request $request)
    {
        // entrys >> {
        //     "temp":1,
        //     "ticket_id": 1
        //     "event_id"
        // }
        // Clicar em comprar fora do carrinho sem quantidade
        // Add quantidade dentro do carrrinho

        $data = $request->all();
        date_default_timezone_set('America/Sao_Paulo');
        $ticket = Tickets::where('id', $data['ticket_id'])->where('event_id', $data['event_id'])->first();

        if (empty($ticket)) {
            return response()->json(['error' => 'Dados inválidos!'], 202);
        }

        $date_from_user = new DateTime();
        $start_date = new DateTime($ticket->start_date);
        $end_date = new DateTime($ticket->end_date);
        if ($ticket->active != 1 || !$this->check_in_range($start_date, $end_date, $date_from_user)) {
            return response()->json(['error' => 'Passaporte não está liberado para venda!'], 202);
        }
        $temp_storage = json_decode($data['temp']);
        $temp_data = TempData::where('user_cod', $temp_storage->key)->orWhere('user_cod', $temp_storage->email)->get();
        if ($temp_storage->email) {
            $temp_storage->key = $temp_storage->email;
        }

        if ($temp_data == "[]") {
            if ($ticket->quantity > 0) {
                $ticket->update(array('quantity' => $ticket->quantity - 1));
                $cart = Cart::create(["event_id" => $data['event_id']]);
                $temp['user_cod'] = $temp_storage->key; //ver
                $temp['cart_id'] = $cart->id;
                $products['cart_id'] = $cart->id;
                $products['ticket_id'] = $data['ticket_id'];
                $products['total_price'] = $ticket->price;
                $log['cart_id'] = $cart->id;
                $log['message'] = "Criado carrinho e adicionado: 1 " . "<strong>" . $ticket->title . "</strong>";
                $cart_products = CartProducts::create($products);
                // $cart_products->update(array('quantity' => $cart_products->quantity + 1));
                $temp_user = TempData::create($temp);
                $log_create = CartLog::create($log);
                return response()->json(['valor_total' => $cart_products->total_price, 'Quantidade' => $cart_products->quantity]);
            } else {
                return response()->json(["error" => 'Estoque deste lote zerado!'], 202);
            }
        } else {
            $cart_products = CartProducts::where('ticket_id', $data['ticket_id'])
                ->where('cart_id', $temp_data[0]->cart_id)
                ->get();
            if ($cart_products == "[]" && $ticket->quantity > 0) {
                $products['cart_id'] = $temp_data[0]->cart_id;
                $products['ticket_id'] = $data['ticket_id'];
                $products['total_price'] = $ticket->price;
                $log['cart_id'] = $temp_data[0]->cart_id;
                $log['message'] = "Adicionado: 1 " . "<strong>" . $ticket->title . "</strong>" . " ao carrinho";
                $ticket->update(array('quantity' => $ticket->quantity - 1));
                $cart_products = CartProducts::create($products);
                // $cart_products->update(array('quantity' => $cart_products->quantity + 1));
                $get_coupon = Cart::where('id',  $temp_data[0]->cart_id)->first();
                $discount = 0;
                if ($get_coupon) {
                    if ($get_coupon->coupon_id) {
                        $discount = $this->get_discount($get_coupon->coupon_id, json_decode($data['temp']));
                        $get_coupon->update(['coupon_value' => $discount]);
                    }
                }

                $log_create = CartLog::create($log);
                return response()->json(['valor_total' => $cart_products->total_price, 'Quantidade' => $cart_products->quantity, 'Desconto' => round($discount, 2)]);
            } else if ($cart_products != "[]" && $ticket->quantity > 0) {
                $add = $this->add($data['ticket_id'], json_decode($data['temp']));
                return $add;
            } else {
                return response()->json(["error" => 'Estoque deste lote zerado!'], 202);
            }
        }
    }

    private function check_in_range($start_date, $end_date, $date_from_user)
    {
        $start = $start_date->getTimestamp();
        $end = $end_date->getTimestamp();
        $check = $date_from_user->getTimestamp();

        return (($start <= $check) && ($check <= $end));
    }

    public function sum(Request $request)
    {
        // entrys >> {
        //     "temp":1,
        //     "ticket_id": 1
        // }
        // Aumenta quantidade quando chamado dentro do carrinho.

        $data = $request->all();

        $temp = json_decode($data['temp']);
        // if ($temp->email) {
        $add = $this->add($data['ticket_id'], $temp);
        // } else {
        //     $add = $this->add($data['ticket_id'], $temp->key);
        // }
        return $add;
    }

    private function add($ticket_id, $temp)
    {
        $ticket = Tickets::find($ticket_id);
        $date_from_user = new DateTime();
        $start_date = new DateTime($ticket->start_date);
        $end_date = new DateTime($ticket->end_date);
        if ($ticket->active != 1 || !$this->check_in_range($start_date, $end_date, $date_from_user)) {
            return response()->json(['msg' => 'Passaporte não está liberado para venda!'], 403);
        }
        $cart_products = DB::table('cart_products')->join('temp_data', 'temp_data.cart_id', '=', 'cart_products.cart_id')
            ->where('cart_products.ticket_id', $ticket_id)
            ->where('temp_data.user_cod', $temp->email)
            ->orWhere('temp_data.user_cod', $temp->key)
            ->where('cart_products.ticket_id', $ticket_id)
            ->get();

        if (count($cart_products) < $ticket->max_sell && $ticket->quantity > 0) {
            CartProducts::create(['ticket_id' => $ticket->id, 'quantity' => 1, 'cart_id' => $cart_products->first()->cart_id, 'total_price' => $ticket->price]);
            $ticket->update(array('quantity' => $ticket->quantity - 1));
            $get_coupon = Cart::where('id', $cart_products->first()->cart_id)->first();
            $discount = 0;
            if ($get_coupon) {
                if ($get_coupon->coupon_id) {
                    $discount = $this->get_discount($get_coupon->coupon_id, $temp);
                    $get_coupon->update(['coupon_value' => $discount]);
                }
            }
            $log = CartLog::create(['cart_id' => $cart_products->first()->cart_id, 'message' => 'Adicionado 1 ' . "<strong>" . $ticket->title . "</strong>" . " ao carrinho"]);
            $cart_product_quantity = CartProducts::where('cart_id', $cart_products->first()->cart_id)->where('ticket_id', $ticket_id)->sum('quantity');
            $cart_product_value = CartProducts::where('cart_id', $cart_products->first()->cart_id)->where('ticket_id', $ticket_id)->sum('total_price');
            return response()->json(['valor_total' => $cart_product_value, 'Quantidade' => (int)$cart_product_quantity, 'Desconto' => round($discount, 2)]);
        } else if ($ticket->quantity == 0) {
            return response()->json(array("error" => 'Estoque insuficiente deste item!'));
        } else {
            return response()->json(array("max" => 'Quantidade máxima deste passaporte atingida!'));
        }
    }

    public function sub(Request $request)
    {
        // entrys >> {
        //     "temp":1,
        //     "ticket_id": 2
        // }
        // Subtrai dentro do carrinho
        // Quando chegar a zero na qtd, recarregar a tela para mostrar que não existe nada no carrinho

        $data = $request->all();
        $ticket = Tickets::find($data['ticket_id']);
        $temp = json_decode($data['temp']);
        $temp_data = TempData::where('user_cod', $temp->email)->orWhere('user_cod', $temp->key)->first();
        $cart_products = CartProducts::where('cart_id', $temp_data->cart_id)->where('ticket_id', $data['ticket_id'])->first();
        $product_to_delete = CartProducts::find($cart_products->id)->delete();
        $ticket->update(array('quantity' => $ticket->quantity + 1));
        $get_coupon = Cart::where('id', $temp_data->cart_id)->first();
        $discount = 0;
        $log = CartLog::create(['cart_id' => $temp_data->cart_id, 'message' => 'Removido 1 ' . "<strong>" . $ticket->title . "</strong>" . " do carrinho"]);

        if ($get_coupon) {
            if ($get_coupon->coupon_id) {
                $discount = $this->get_discount($get_coupon->coupon_id, $temp);
                if ($discount <= 0) {
                    $coupon = Coupon::find($get_coupon->coupon_id);

                    if ($coupon->unique_use == 0) {
                        $coupon->update(['quantity' => $coupon->quantity += 1]);
                    }

                    $get_coupon->update([
                        "coupon_id" => null,
                        "coupon_code" => null,
                        "coupon_value" => 0,
                        "coupon_type" => 0
                    ]);
                    return response()->json('Cupom removido por falta de elegibilidade');
                } else {
                    $get_coupon->update(['coupon_value' => round($discount, 2)]);
                }
            }
        }

        return response()->json(['Desconto' => round($discount, 2)]);
    }

    private function get_discount($coupon_id, $temp)
    {
        $cart_get = Cart::join('temp_data', 'temp_data.cart_id', '=', 'carts.id')
            ->join('cart_products as c', 'c.cart_id', 'carts.id')
            ->join('coupon_relations as cr', 'cr.ticket_id', 'c.ticket_id')
            ->join('coupons as cs', 'cs.id', 'cr.coupon_id')
            ->where('cr.coupon_id', $coupon_id)
            ->where('user_cod', $temp->email)->orWhere('user_cod', $temp->key)
            ->where('cr.coupon_id', $coupon_id)
            ->select('cr.price as discount', 'c.total_price as ticket_price')
            ->get();

        $total_discount = 0;
        if (!empty($cart_get)) {
            foreach ($cart_get as $discount) {
                $total_discount += $this->get_percentage($discount->ticket_price, $discount->discount);
            }
        }

        return $total_discount;
    }

    private function get_percentage($total, $percentage)
    {
        if ($total > 0) {
            return ($percentage / 100) * $total;
        } else {
            return 0;
        }
    }

    public function promotion(Request $request)
    {
        // entrys >> {
        //     promo_code: "TXF23EKX1",
        //     temp: 1
        // }

        // Se inserir novamente o mesmo promo_code e temp, o cupom é removido
        // Promoção feita dentro do carrinho

        $data = $request->all();
        $temp = json_decode($data['temp']);
        $coupon = Coupon::where('code', $data['promo_code'])->first();
        if ($coupon) {
            $date_from_user = new DateTime();
            $start_date = new DateTime($coupon->start_date);
            $end_date = new DateTime($coupon->end_date);

            $cart_check = Cart::join('temp_data', 'temp_data.cart_id', '=', 'carts.id')
                ->join('cart_products as c', 'c.cart_id', 'carts.id')
                ->join('coupon_relations as cr', 'cr.ticket_id', 'c.ticket_id')
                ->join('coupons as cs', 'cs.id', 'cr.coupon_id')
                ->where('cr.coupon_id', $coupon->id)
                ->where('user_cod', $temp->email)->orWhere('user_cod', $temp->key)
                ->where('cr.coupon_id', $coupon->id)
                ->select('cr.price as discount', 'c.total_price as ticket_price')
                ->groupBy('cr.ticket_id')
                ->get();

            $coupon->value = $this->get_discount($coupon->id, $temp);
        }

        if (!$coupon) {
            return "Código Inválido";
        } else if (!$this->check_in_range($start_date, $end_date, $date_from_user)) {
            return "Código Expirado";
        } else if ($cart_check == '[]') {
            return "Nenhum passaporte elegível para este código!";
        } else if ($coupon->unique_use == 0 && $coupon->quantity <= 0) {
            return "Cupom Esgotado :(";
        } else {
            $cart = Cart::join('temp_data', 'temp_data.cart_id', '=', 'carts.id')
                ->where('user_cod', $temp->email)->orWhere('user_cod', $temp->key)
                ->first();

            if ($cart->coupon_id != null && $cart->coupon_code != $data['promo_code']) {
                return "Apenas um cupom por vez";
            } else if ($cart->coupon_id != null && $cart->coupon_code == $data['promo_code']) {
                Cart::find($cart->cart_id)
                    ->update([
                        "coupon_id" => null,
                        "coupon_code" => null,
                        "coupon_value" => null,
                        "coupon_type" => 0
                    ]);
                $log = CartLog::create(['cart_id' => $cart->cart_id, 'message' => 'Removido cupom ' . "<strong>" . $coupon->code . "</strong>"]);
                if ($coupon->unique_use == 0) {
                    $coupon->quantity += 1;
                    $update = Coupon::find($coupon->id)->update(['quantity' => $coupon->quantity]);
                }
                return "Cupom Removido";
            }

            Cart::find($cart->cart_id)
                ->update([
                    "coupon_id" => $coupon->id,
                    "coupon_code" => $coupon->code,
                    "coupon_value" => $coupon->value,
                    "coupon_type" => $coupon->discount_type
                ]);
            $log = CartLog::create(['cart_id' => $cart->cart_id, 'message' => 'Utilizado cupom ' . "<strong>" . $coupon->code . "</strong>"]);

            if ($coupon->unique_use == 0) {
                $coupon->quantity -= 1;
                $update = Coupon::find($coupon->id)->update(['quantity' => $coupon->quantity]);
            }
            return response()->json(['Desconto' => round($coupon->value, 2)]);
        }
    }

    public function cancel(Request $request)
    {
        // entrys >> {
        //     "temp":1
        //      "ticket_id:numero"
        // }
        // Cancela um item inteiro do carrinho
        //FAZER UM LOG

        $data = $request->all();
        $temp = json_decode($data['temp']);
        $cancel = CartProducts::join('temp_data as t', 't.cart_id', '=', 'cart_products.cart_id')
            ->where('cart_products.ticket_id', $data['ticket_id'])
            ->where('user_cod', $temp->email)
            ->orWhere('user_cod', $temp->key)
            ->where('cart_products.ticket_id', $data['ticket_id'])
            ->select('cart_products.id as id', 'cart_products.ticket_id as ticket_id', 'cart_products.cart_id as cart')
            ->get();

        $ticket = Tickets::find($cancel->first()->ticket_id);
        $ticket->update(['quantity' => $ticket->quantity + count($cancel)]);
        foreach ($cancel as $ticket_to_cancel) {
            $ticket_to_delete = CartProducts::find($ticket_to_cancel->id)->delete();
        }
        $get_coupon = Cart::where('id', $cancel->first()->cart)->first();
        if ($get_coupon) {
            if ($get_coupon->coupon_id) {
                $discount = $this->get_discount($get_coupon->coupon_id, $temp);
                if ($discount <= 0) {
                    $coupon = Coupon::find($get_coupon->coupon_id);

                    if ($coupon->unique_use == 0) {
                        $coupon->update(['quantity' => $coupon->quantity += 1]);
                    }

                    $get_coupon->update([
                        "coupon_id" => null,
                        "coupon_code" => null,
                        "coupon_value" => 0,
                        "coupon_type" => 0
                    ]);
                    return response()->json('Cupom removido por falta de elegibilidade');
                } else {
                    $get_coupon->update(['coupon_value' => round($discount, 2)]);
                }
            }
        }
    }

    private function AsaasCancel($payment_id)
    {
        $cancel = Cart::where('payment_id', $payment_id)
            ->join('clients as c', 'c.id', 'carts.client_id')
            ->whereNotIn('carts.cart_step_id', [7, 8, 9])
            ->select('carts.coupon_id', 'c.name', 'c.email', 'carts.id as id')
            ->first();

        if (empty($cancel)){
            return response()->json('Carrinho já cancelado',200);
        }
        $products = CartProducts::where('cart_id', $cancel->id)->get();

        if ($cancel->coupon_id) {
            $coupon = Coupon::find($cancel->coupon_id);
            if ($coupon->unique_use == 0) {
                $coupon->update(['quantity' => $coupon->quantity += 1]);
            }
        }
        $carts = Cart::find($cancel->id)->update(array('cart_step_id' => 9));

        foreach ($products as $product) {
            $tickets = Tickets::find($product->ticket_id);
            $tickets->update(array('quantity' => $tickets->quantity + $product->quantity));
        }
        $log = CartLog::create(['cart_id' => $cancel->id, 'message' => 'Carrinho <strong>cancelado</strong> pela Asaas']);

        $email_receiver = $cancel->email;

        $assunto = "Seu pagamento foi cancelado";

        $mensagem = '
        <div><img src="' . asset('/img/header-email.png') . '"></div>
        <div>&nbsp;</div>
        <div style="font-family: Verdana,Arial,Helvetica,sans-serif;color:#4d4d4d">		
        <div>Olá, <b>' . $cancel->name . '</b>.</div>
        <div>&nbsp;</div>
        <div>O seu pedido de número <b>' . $cancel->id . '</b> para o HOJE 2021, teve o pagamento cancelado. :/</div>
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

        $ticket_list = CartProducts::where('cart_id', $cancel->id)
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

        $email_receiver = $cancel->email;
        $firstname = $cancel->name;

        ApiMailHelper::sendFunil($tags, $firstname, $email_receiver);
    }

    public function timeout()
    {
        $created = Cart::whereNotIn('cart_step_id', array(4, 5, 6, 7, 8, 9))
            ->where('updated_at', '<', Carbon::now()->subMinutes(15)->toDateTimeString())
            ->get();

        $pix_cart = Cart::where('cart_step_id', 4)
            ->where('pix_date', '<', Carbon::today())
            ->get();

        $verify_recovery = PasswordRecovery::whereDate('created_at', '<', Carbon::today())->get();
        $verify_direct_sell = DirectSell::where('updated_at', '<', Carbon::now()->subHours(1)->toDateTimeString())->where('sell_step_id', 1)->get();

        foreach ($verify_direct_sell as $sell) {
            $ticket_s = Tickets::find($sell->ticket_id);
            $ticket_s->update(['quantity' => $ticket_s->quantity + $sell->quantity]);
            $sell->delete();
        }

        foreach ($pix_cart as $pix) {
            $pix->update(['cart_step_id' => 8]);
            $product_p = CartProducts::where('cart_id', $pix->id)->get();
            foreach ($product_p as $p) {
                $ticket_p = Tickets::where('id', $p->ticket_id)->first();
                $ticket_p->update(['quantity' => $ticket_p->quantity + 1]);
            }
        }


        foreach ($created as $create) {
            $temp_data = TempData::where('cart_id', $create->id)->first();

            if ($create->coupon_id) {
                $coupon = Coupon::find($create->coupon_id);
                if ($coupon->unique_use == 0) {
                    $coupon->update(['quantity' => $coupon->quantity += 1]);
                }
            }
            if (!empty($temp_data)) {
                $temp_data->delete();
            }
            $products_data = CartProducts::where('cart_id', $create->id)->get();
            foreach ($products_data as $product) {
                $tickets = Tickets::find($product->ticket_id);
                $tickets->update(array('quantity' => $tickets->quantity + $product->quantity));
            }
            $create->update(array('cart_step_id' => 8));
            $log = CartLog::create(['cart_id' => $create->id, 'message' => 'Carrinho <strong>cancelado</strong> por ociosidade']);
        }

        foreach ($verify_recovery as $recovery) {
            $recovery->delete();
        }

        return response()->json('Timed Out!');
    }

    public function attribution(request $request)
    {
        //     entrys >> {
        //         "temp":1,
        //         "client_id":1
        //         "post_attribution: boolean" ==> PARA QUANDO LOGA NO MEIO DO CARRINHO
        // }
        //após criação de cms de clientes, colocar para log aparecer com nome do mesmo

        $data = $request->all();
        $temp = json_decode($data['temp']);
        $email_verify = TempData::where('user_cod', $temp->email)->first();
        if (!empty($email_verify) && $data['post_attribution']) {
            $cart_to_substitute = Cart::where('carts.id', $email_verify->cart_id)
                ->join('cart_products as c', 'c.cart_id', 'carts.id')
                ->select('c.ticket_id', 'c.id as cart_product_id')
                ->get();
            foreach ($cart_to_substitute as $ticket_to_delete) {
                Tickets::find($ticket_to_delete->ticket_id)->update(['quantity' => +1]);
                CartProducts::find($ticket_to_delete->cart_product_id)->delete();
            }
            Cart::find($email_verify->cart_id)->delete();
            $email_verify->delete();
        }
        $carts = DB::table('carts')->join('temp_data', 'temp_data.cart_id', '=', 'carts.id')
            ->where('user_cod', $temp->key)
            ->orwhere('user_cod', $temp->email)
            ->where('carts.client_id', '=', null)
            ->whereNotIn('carts.cart_step_id', [6, 7])
            ->first();
        if ($carts) {
            $temp_data = TempData::where('user_cod', $temp->key)->orWhere('user_cod', $temp->email)->first();
            $temp_data->update(['user_cod' => $temp->email]);
            $client = UserAuth::join('clients', 'clients.email', 'user_auths.email')->where('token', $data['client_id'])->first();
            //$log = CartLog::create(['cart_id' => $carts->cart_id, 'message' => 'Carrinho <strong>atribuído</strong> ao cliente: ' . '<strong>' . $client->name . '</strong>']);
            $cart_data = Cart::find($carts->cart_id);
            $cart_data->update(array('client_id' => $client->id));
        }
    }

    public function asaas(request $request)
    {
        // entrys >> {
        //     "name":"Paulo",
        //     "parcels" : 10, 
        //     "cpfCnpj": "12345678",
        //     "number": "5184019740373151",
        //     "expiryMonth": "07",
        //     "expiryYear": "2021",
        //     "ccv" : "318",
        //     "temp": apenas valor,
        //     "client_id"
        //      payment_method: 1 para credito, 2 para boleto e 3 para pix
        // }

        // Mandar isso pra api, esse formulário é preenchido na hora

        $data = $request->all();
        // return response()->json(json_decode(Asaas::getQrCode('pay_4314522418714063')));
        $client_details = Client::where('email', $data['temp'])->select('name')->first();
        // return response()->json(asset('/img/header-email.png'));

        $client_data = TempData::where('user_cod', $data['temp'])
            ->join('carts', 'carts.id', '=', 'temp_data.cart_id')
            ->join('clients', 'clients.id', '=', 'carts.client_id')
            ->join('user_auths', 'user_auths.email', '=', 'clients.email')
            ->where('user_auths.token', '=', $data['client_id'])
            ->first();

        $carts = Cart::whereIn('cart_step_id', [4, 5, 6, 7, 8, 9])->where('client_id', $client_data->client_id)->get();
        // if ($client_data->coupon_id) {
        $coupon = Coupon::where('id', $client_data->coupon_id)->first();
        foreach ($carts as $cart) {
            if ($cart->coupon_id == $client_data->coupon_id) {
                if (isset($coupon->unique_use)) {
                    if ($coupon->unique_use == 1) {
                        Cart::find($client_data->cart_id)
                            ->update([
                                "coupon_id" => null,
                                "coupon_code" => null,
                                "coupon_value" => 0,
                                "coupon_type" => 0
                            ]);
                        return response()->json(["msg" => "CUPOM JÁ UTILIZADO EM COMPRAS ANTERIORES!"], 400);
                    }
                }
            }
        }
        // }

        $client = Client::where('id', $client_data->client_id)->first();
        $tempdata = TempData::where('user_cod', $data['temp'])->select('id')->first();

        $ticket_list_desc = CartProducts::where('cart_id', $client_data->cart_id)
            ->join('tickets as t', 't.id', 'cart_products.ticket_id')
            ->join('event_years as e', 'e.id', 't.event_id')
            ->selectRaw('count(cart_products.quantity) as quantity,t.title,e.year,total_price')
            ->groupBy('cart_products.ticket_id')
            ->get();

        $description = "";
        for ($i = 0; $i < count($ticket_list_desc); $i++) {
            $description = $description . '
            ' . '-item: ' . $ticket_list_desc[$i]->title . ' ' . $ticket_list_desc[$i]->year . ' -Qtd: ' . $ticket_list_desc[$i]->quantity . ' -R$: ' . number_format(($ticket_list_desc[$i]->quantity * $ticket_list_desc[$i]->total_price), 2, ',', '.');
        }

        // return $description;
        $products_value = CartProducts::where('cart_id', $client_data->cart_id)->sum('total_price');
        if ($data["payment_method"] == 1) {
            $data["payment_method"] = "CREDIT_CARD";
            $data["payment_method_id"] = 1;
        } else if ($data['payment_method'] == 2) {
            $data["payment_method"] = "BOLETO";
            $data["payment_method_id"] = 2;
        } else if ($data['payment_method'] == 3) {
            $data["payment_method"] = "UNDEFINED";
            $data["payment_method_id"] = 3;
        }

        $email_receiver = $data['temp'];
        unset($data['temp']);

        if ($client_data->coupon_id) {
            if ($client_data->coupon_type == 1) {
                $products_value -= $client_data->coupon_value;
            } else {
                $percent = $products_value * ($client_data->coupon_value / 100);
                $products_value -= $percent;
            }
        }
        $dueDate = Carbon::now()->addDay(Configurations::first()['due_date'])->format('Y-m-d');
        $account = ["name" => $client_data->name, "email" => $client_data->email, "cpfCnpj" => $data['cpfCnpj']];

        if ($data['payment_method'] == "CREDIT_CARD") {
            $credit_card = ["holderName" => $data['name'], "number" => $data["number"], "expiryMonth" => $data["expiryMonth"], "expiryYear" => $data["expiryYear"], "ccv" => $data["ccv"]];
            $card_holder = [
                "name" => $client_data->name,
                "email" => $client_data->email,
                "cpfCnpj" => $data['cpfCnpj'],
                "postalCode" => $client_data->cep,
                "addressNumber" => $client_data->number,
                "addressComplement" => $client_data->complement,
                "phone" => $client_data->phone,
                "mobilePhone" => $client_data->cell
            ];
        }
        if (isset($data['parcels'])) {
            $parcels = $data['parcels'];
            $parcel_price = $products_value / $parcels;
        } else {
            $parcels = null;
            $parcel_price = null;
        }
        if (!$client->asaas_id) {
            $create_client = Asaas::ClientCreate($account);
            $client_update = Client::find($client->id)->update(['asaas_id' => json_decode($create_client)->id]);
            $client->asaas_id = json_decode($create_client)->id;
        }
        $payment = [
            "customer" => $client->asaas_id,
            "billingType" => $data['payment_method'],
            "description" => $description,
            "dueDate" => $dueDate,
            "value" => $products_value,
            // "creditCard" => $credit_card,
            // "creditCardHolderInfo" => $card_holder
        ];
        if ($data['payment_method'] == "CREDIT_CARD") {
            $payment["installmentCount"] = $parcels;
            $payment["installmentValue"] = $parcel_price;
            $payment["creditCard"] = $credit_card;
            $payment["creditCardHolderInfo"] = $card_holder;
        }

        $charge = Asaas::SendToCharge(json_encode($payment));

        if ($data['payment_method_id'] == 3) {
            $pix = Asaas::getQrCode(json_decode($charge)->id);
        }

        if (isset(json_decode($charge)->errors)) {
            return response()->json(["error" => json_decode($charge)->errors[0]->description]);
        } else {
            if ($data['payment_method_id'] == 1) {
                Cart::find($client_data->cart_id)->update(["payment_id" => json_decode($charge)->id, "payment_method_id" => $data['payment_method_id'], "payment_method" => $data['payment_method'], "cart_step_id" => 4, "total_parcels" => $parcels]);
            } else if ($data['payment_method_id'] == 2) {
                Cart::find($client_data->cart_id)->update(["payment_id" => json_decode($charge)->id, "payment_method_id" => $data['payment_method_id'], "payment_link" => json_decode($charge)->bankSlipUrl, "payment_method" => $data['payment_method'], "cart_step_id" => 4, "total_parcels" => $parcels]);
            } else if ($data['payment_method_id'] == 3) {
                Cart::find($client_data->cart_id)->update(["payment_id" => json_decode($charge)->id, "payment_method_id" => $data['payment_method_id'], "pix_link" => json_decode($pix)->payload, 'pix_qr_code' => json_decode($pix)->encodedImage, 'pix_date' => json_decode($pix)->expirationDate, "payment_method" => $data['payment_method'], "cart_step_id" => 4, "total_parcels" => $parcels]);
            }
            $log = CartLog::create(['cart_id' => $client_data->cart_id, 'message' => 'Pagamento gerado, alterado status para <strong>Aguardando Pagamento</strong>']);
            TempData::find($tempdata->id)->delete();

            $assunto = "Sua compra foi concluída com sucesso (Aguardando Pagamento)";
            $mensagem = '<div><img src="' . asset('/img/header-email.png') . '"></div>
            <div>&nbsp;</div>
            <div style="font-family: Verdana,Arial,Helvetica,sans-serif;color:#4d4d4d">        
            <div>Olá, <b>' . $client_details->name . '</b>,</div>
            <div>Espero que esteja bem! Por aqui estamos empolgadíssimos \o/</div>
            <div>Como você está?</div>
            <div>&nbsp;</div>
            <div>Recebemos sua compra, estamos apenas aguardando a confirmação do pagamento, código <b>' . $client_data->cart_id . '</b>!</div>
            <div>&nbsp;</div>
            <div>Estamos muito felizes por você comprar o seu passaporte para o ' . 'HOJE 2021' . '. õ/</div>
            <div>
            Para acompanhar o status da compra é só <a href="' . route('client.login') . '">clicar aqui</a> e realizar o login com o seu e-mail e senha cadastrados no momento da compra. 
            <div>&nbsp;</div>	
            <div><img style="width: 200px;height: 200px;" src="' . asset('/img/aline-email.png') . '"></div>		
            <div>&nbsp;</div>
            <div style="font-weight: bold;color: #ec0e51;font-size: 18px;">Aline da Silva</div>
            <div>Gestora</div>
            <div>49 3090-9907 | 49 99802-6025</div>
            <a href="https://www.instagram.com/eventohoje/">@eventohoje</a>
            <a href="https://www.facebook.com/alinefsilvaw">@alinefsilvaw</a>
            <a href="https://www.linkedin.com/in/alinefsilvaw/">@alinefsilvaw</a>
            <div>&nbsp;</div>	
            <div>"nunca subestime o impacto que você pode ter na vida de outra pessoa!"</div>
        </div>';

            ApiMailHelper::sendEmail($assunto, $mensagem, $email_receiver);

            $ticket_list = CartProducts::where('cart_id', $client_data->cart_id)
                ->join('tickets as t', 't.id', 'cart_products.ticket_id')
                ->join('event_years as e', 'e.id', 't.event_id')
                ->select('cart_products.ticket_id', 't.title', 'e.year')
                ->groupBy('cart_products.ticket_id')
                ->get();

            $tags = "";
            for ($i = 0; $i < count($ticket_list); $i++) {
                if ($i == 0) {
                    $tags = 'hj-comprou-' . $ticket_list[$i]->ticket_id . 'ID' . '-' . $ticket_list[$i]->title . '-' . $ticket_list[$i]->year;
                } else {
                    $tags = $tags . ',hj-comprou-' . $ticket_list[$i]->ticket_id . 'ID' . '-' . $ticket_list[$i]->title . '-' . $ticket_list[$i]->year;
                }
            }

            $firstname = $client_details->name;
            ApiMailHelper::sendFunil($tags, $firstname, $email_receiver);

            if ($data['payment_method_id'] == 1 || $data['payment_method_id'] == 2) {
                return response()->json(json_decode($charge));
            } else if ($data['payment_method_id'] == 3) {
                return response()->json(json_decode($pix));
            }
        }
    }

    public function webhook(request $request)
    {
        $data = $request->all();

        $client_products = Client::where('asaas_id', $data['payment']['customer'])
            ->join('carts', 'carts.client_id', '=', 'clients.id')
            ->join('cart_products', 'cart_products.cart_id', '=', 'carts.id')
            ->where('payment_id', $data['payment']['id'])
            ->select(
                'cart_products.id',
                'carts.client_id',
                'cart_products.cart_id',
                'clients.name as name',
                'clients.email as email'
            )
            ->get();

        if ($client_products == "[]") {
            return response()->json('Usuario não encontrado', 200);
        }

        if ($data['event'] == "PAYMENT_CONFIRMED" || $data['event'] == "PAYMENT_RECEIVED") {
            foreach ($client_products as $item) {
                $ingresso = Ingressos::create(["fk_item_id" => $item->id, "ingresso_codigo" => TicketsGenerator::generateTicketCode()]);
                Cart::find($item->cart_id)->update(["cart_step_id" => 5]);
            }
            $log = CartLog::create(['cart_id' => $client_products->first()->cart_id, 'message' => 'Pagamento Recebido, alterado status para <strong>Pagamento Confirmado</strong>']);

            $email_receiver = $client_products[0]->email;

            $assunto = "Recebemos o seu pagamento";

            $mensagem = '
        <div><img src="' . asset('/img/header-email.png') . '"></div>
        <div>&nbsp;</div>
        <div style="font-family: Verdana,Arial,Helvetica,sans-serif;color:#4d4d4d">
        <div><b>' . $client_products[0]->name . '</b></div>
        <div>&nbsp;</div>
        <div>O seu pedido <b>' . $client_products[0]->cart_id . '</b> teve o pagamento confirmado com sucesso.</div>
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
        <div>49 3090-9907 | 49 99802-6025</div>
        <a href="https://www.instagram.com/eventohoje/">@eventohoje</a>
        <a href="https://www.facebook.com/alinefsilvaw">@alinefsilvaw</a>
        <a href="https://www.linkedin.com/in/alinefsilvaw/">@alinefsilvaw</a>
        <div>&nbsp;</div>	
        <div>"nunca subestime o impacto que você pode ter na vida de outra pessoa!"</div>
    </div>';

            ApiMailHelper::sendEmail($assunto, $mensagem, $email_receiver);

            $ticket_list = CartProducts::where('cart_id', $client_products[0]->cart_id)
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

            $firstname = $client_products[0]->name;
            ApiMailHelper::sendFunil($tags, $firstname, $email_receiver);
        } else if ($data['event'] == "PAYMENT_OVERDUE" || $data['event'] == "PAYMENT_DELETED" || $data['event'] == "PAYMENT_REFUNDED") {
            // return response()->json($this->AsaasCancel($data['payment']['id']));
            $this->AsaasCancel($data['payment']['id']);
        }
    }
}
