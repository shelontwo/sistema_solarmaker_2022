<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\Integrador;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'cli_id';

    protected $table = 'clientes';

    const UUID = 'uuid_cli_id';

    const CREATED_AT = 'cli_criado_em';

    const UPDATED_AT = 'cli_atualizado_em';

    const DELETED_AT = 'cli_deletado_em';

	protected $fillable = [
		'uuid_cli_id',
      	'cli_nome',
        'cli_cep',
        'cli_uf',
        'cli_cidade',
        'cli_bairro',
        'cli_rua',
        'cli_numero',
        'cli_complemento',
        'cli_telefone',
        'cli_celular',
        'cli_email',
        'cli_usuario',
        'cli_senha',
        'cli_alterar_senha',
        'fk_int_id_integrador'
	];

	protected static function boot()
    {
        parent::boot();

        static::creating(function (Cliente $cliente) {
            $cliente->uuid_cli_id = Str::uuid()->toString();
        });
    }
}
