<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Integrador extends Model
{
    use HasFactory;

    protected $primaryKey = 'int_id';

    protected $table = 'integradores';

    const UUID = 'uuid_int_id';

    const CREATED_AT = 'int_criado_em';

    const UPDATED_AT = 'int_atualizado_em';

    const DELETED_AT = 'int_deletado_em';

	protected $fillable = [
		'uuid_int_id',
      	'int_nome',
        'fk_dis_id_distribuidor'
	];

	protected $hidden = [
        'int_id',
    ];

	protected static function boot()
    {
        parent::boot();

        static::creating(function (Integrador $integrador) {
            $integrador->uuid_int_id = Str::uuid()->toString();
        });
    }
}
