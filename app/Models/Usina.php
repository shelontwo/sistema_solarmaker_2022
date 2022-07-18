<?php

namespace App\Models;

use App\Models\Cliente;
use App\Models\Integrador;
use Illuminate\Support\Str;
use App\Models\UsinaSistemaCredito;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usina extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'usi_id';

    protected $table = 'usinas';

    const UUID = 'uuid_usi_id';

    const CREATED_AT = 'usi_criado_em';

    const UPDATED_AT = 'usi_atualizado_em';

    const DELETED_AT = 'usi_deletado_em';

	protected $fillable = [
		'uuid_usi_id',
      	'usi_nome',
        'usi_cep',
        'usi_uf',
        'usi_cidade',
        'usi_bairro',
        'usi_rua',
        'usi_numero',
        'usi_latitude',
        'usi_longitude',
        'usi_status',
        'usi_webhook_url',
        'usi_desativado_em',
        'fk_int_id_integrador',
        'fk_cli_id_cliente'
	];

	protected static function boot()
    {
        parent::boot();

        static::creating(function (Usina $usina) {
            $usina->uuid_usi_id = Str::uuid()->toString();
        });
    }

    public function integrador()
    {
        return $this->belongsTo(Integrador::class, 'fk_int_id_integrador')->select('uuid_int_id', 'int_id', 'int_nome');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'fk_cli_id_cliente')->select('uuid_cli_id', 'cli_id', 'cli_nome');
    }

    public function sistema_credito()
    {
        return $this->hasMany(UsinaSistemaCredito::class, 'fk_usi_id_usina', 'usi_id');
    }
}
