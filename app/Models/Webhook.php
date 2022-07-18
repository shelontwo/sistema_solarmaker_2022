<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Webhook extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'web_id';

    protected $table = 'webhooks';

    const CREATED_AT = 'web_criado_em';

    const UPDATED_AT = 'web_atualizado_em';

	protected $fillable = [
      	'fk_cli_id_cliente',
      	'fk_usi_id_usina',
      	'fk_log_id_log',
        'web_status'
	];
}