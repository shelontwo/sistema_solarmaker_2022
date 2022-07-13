<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chamado extends Model
{
	use HasFactory, SoftDeletes;
	
	protected $primaryKey = 'cha_id';

    protected $table = 'chamados';

    const UUID = 'uuid_cha_id';

    const CREATED_AT = 'cha_criado_em';

    const UPDATED_AT = 'cha_atualizado_em';
	
    const DELETED_AT = 'cha_deletado_em';

	protected $fillable = [
		'uuid_cha_id',
      	'cha_status',
        'cha_descricao',
        'cha_solucao',
        'cha_finalizado_em',
        'fk_cli_id_cliente'
	];

	protected static function boot()
    {
        parent::boot();

        static::creating(function (Chamado $chamado) {
            $chamado->uuid_cha_id = Str::uuid()->toString();
        });
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'fk_cli_id_cliente')->select('uuid_cli_id', 'cli_id', 'cli_nome', 'fk_int_id_integrador');
    }
}