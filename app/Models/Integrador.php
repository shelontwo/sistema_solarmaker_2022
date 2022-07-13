<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\Distribuidor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Integrador extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'int_id';

    protected $table = 'integradores';

    const UUID = 'uuid_int_id';

    const CREATED_AT = 'int_criado_em';

    const UPDATED_AT = 'int_atualizado_em';

    const DELETED_AT = 'int_deletado_em';

	protected $fillable = [
		'uuid_int_id',
      	'int_nome',
        'int_nome_fantasia',
        'int_cnpj',
        'int_cep',
        'int_uf',
        'int_cidade',
        'int_bairro',
        'int_rua',
        'int_numero',
        'int_complemento',
        'int_telefone',
        'int_celular',
        'int_email',
        'int_imagem',
        'fk_dis_id_distribuidor'
	];

	protected static function boot()
    {
        parent::boot();

        static::creating(function (Integrador $integrador) {
            $integrador->uuid_int_id = Str::uuid()->toString();
        });
    }

    public function distribuidor()
    {
        return $this->belongsTo(Distribuidor::class, 'fk_dis_id_distribuidor')->select('uuid_dis_id', 'dis_id', 'dis_nome');
    }
}
