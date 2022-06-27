<?php

namespace App\Http\Controllers\Api;

use App\InterestedSponsors;
use App\Helpers\ApiMailHelper;
use App\Traits\UploadTrait;
use Illuminate\Http\Request;

class InterestedSponsor
{
  public function create(Request $request)
  {
    $data = $request->all();
		if (empty($data['privacy_policy'])) {
			$data['privacy_policy'] = 0;
		}

		InterestedSponsors::create($data);

		$html = "
		<p>
			Alguém tem interesse em ser um patrocinador do <b>Evento HOJE 2021</b><br><br>
			<b>Dados do formulário:</b>
			<ul>
				<li><b>Nome: </b> $data[name]</li>
				<li><b>E-mail: </b> $data[email]</li>
				<li><b>Telefone: </b> $data[phone]</li>
				<li><b>Empresa: </b> $data[company]</li>
				<li><b>Aceita receber conteúdos da Política de Privacidade? </b>" . ($data['privacy_policy'] ? 'Sim' : 'Não') . "</li>
			</ul>
		</p>
		";

		//Começa o MKT
		$data['tags'] = 'interesse-patrocinio-2021';
		$data['firstname'] = $data['name'];
		unset($data['name']);
		$data_json = json_encode($data);

		$curl = curl_init();

		curl_setopt_array($curl, [
			CURLOPT_URL => "https://eventohoje.d3tmkt.com.br/api/contacts/new",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $data_json,
			CURLOPT_COOKIE => "be7d8e0fc4a483022cf6967a05a76bdb=9g7cjr4l7fgu8r2e0cmkcj5co6",
			CURLOPT_HTTPHEADER => [
				"Authorization: Basic YWRtaW46cHJvQDEzNQ==",
				"Content-Type: application/json"
			],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		// if ($err) {
		// 	echo "cURL Error #:" . $err;
		// } else {
		// 	echo $response;
		// }

		// Acaba o MKT

		//Começa o MKT do Alexandre

		$curl_ale = curl_init();

		curl_setopt_array($curl_ale, [
			CURLOPT_URL => "https://alexandreweimer.d3tmkt.com.br/api/contacts/new",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $data_json,
			CURLOPT_COOKIE => "be7d8e0fc4a483022cf6967a05a76bdb=9g7cjr4l7fgu8r2e0cmkcj5co6",
			CURLOPT_HTTPHEADER => [
				"Authorization: Basic YWRtaW46cHJvQDEzNQ==",
				"Content-Type: application/json"
			],
		]);

		$response_ale = curl_exec($curl_ale);
		$err_ale = curl_error($curl_ale);

		curl_close($curl_ale);

		// if ($err) {
		// 	echo "cURL Error #:" . $err;
		// } else {
		// 	echo $response;
		// }

		// Acaba o MKT do Alexandre

		//Começa Funil Pró CRM
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "https://api.funil.pro/api/v1/pessoas/novo");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		
		curl_setopt($ch, CURLOPT_POST, TRUE);

		$send = array(
			"cli_nome" => $data['firstname'],
			"cli_email" => $data['email'],
			"cli_telefone" => $data['phone'],
			"cli_observacao" => $data['company'],
			"tags" => array(array('fk_tag_id' => '866', "tagcli_origem" => 'site evento hoje'))
		);
		
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($send));
		
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Content-Type: application/json",
			"Accept: application/json",
			"TokenFunil: 463f70edf704e98e4aba33c66ea8c848"
		));
		
		$response = curl_exec($ch);

		curl_close($ch);
		//Acaba Funil Pró CRM
		
		ApiMailHelper::sendEmail('Evento HOJE 2021', $html, 'vinicius@a9p.com.br');
		ApiMailHelper::sendEmail('Evento HOJE 2021', $html, 'lucas.fornari@a9p.com.br');

		return redirect()->back();
  }
}
