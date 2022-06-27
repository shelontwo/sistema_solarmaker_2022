<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiMailHelper;
use App\Traits\UploadTrait;
use Illuminate\Http\Request;

class LP
{
  public function contact(Request $request)
  {
    $data = $request->all();
		$html = "
		<p>
			Contato da LP do <b>Evento HOJE 2021</b><br><br>
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

		ApiMailHelper::sendEmail('LP Evento HOJE 2021', $html, 'vinicius@a9p.com.br');
		ApiMailHelper::sendEmail('LP Evento HOJE 2021', $html, 'lucas.fornari@a9p.com.br');

		return redirect()->back();
  }
}
