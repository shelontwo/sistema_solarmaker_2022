<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IntegradorApi extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'ina_id';

    protected $table = 'integradores_apis';

    const UUID = 'uuid_ina_id';

    const CREATED_AT = 'ina_criado_em';

    const UPDATED_AT = 'ina_atualizado_em';
    
    const DELETED_AT = 'ina_deletado_em';

	protected $fillable = [
		'uuid_ina_id',
      	'ina_usuario',
      	'ina_api',
      	'ina_senha',
        'ina_token',
        'fk_int_id_integrador'
	];

    protected $hidden = [
        'ina_id',
    ];

	protected static function boot()
    {
        parent::boot();

        static::creating(function (IntegradorApi $api) {
            $api->uuid_ina_id = Str::uuid()->toString();
        });
    }
}