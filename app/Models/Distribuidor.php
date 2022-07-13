<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Distribuidor extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'dis_id';

    protected $table = 'distribuidores';

    const UUID = 'uuid_dis_id';

    const CREATED_AT = 'dis_criado_em';

    const UPDATED_AT = 'dis_atualizado_em';

    const DELETED_AT = 'dis_deletado_em';

	protected $fillable = [
		'uuid_dis_id',
      	'dis_nome',
        'dis_nome_fantasia',
        'dis_cnpj',
        'dis_cep',
        'dis_uf',
        'dis_cidade',
        'dis_bairro',
        'dis_rua',
        'dis_numero',
        'dis_complemento',
        'dis_telefone',
        'dis_celular',
        'dis_email',
        'dis_imagem'
	];

	protected static function boot()
    {
        parent::boot();

        static::creating(function (Distribuidor $distribuidor) {
            $distribuidor->uuid_dis_id = Str::uuid()->toString();
        });
    }

    public function integradores()
    {
        return $this->hasMany(Integrador::class, 'fk_dis_id_distribuidor', 'dis_id')
            ->select('uuid_int_id', 'int_nome', 'int_nome_fantasia', 'int_telefone', 'int_celular');
    }
}
