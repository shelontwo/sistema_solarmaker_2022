<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Concessionaria extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $primaryKey = 'con_id';

    protected $table = 'concessionarias';

    const UUID = 'uuid_con_id';

    const CREATED_AT = 'con_criado_em';

    const UPDATED_AT = 'con_atualizado_em';

    const DELETED_AT = 'con_deletado_em';

	protected $fillable = [
		'uuid_con_id',
      	'con_nome',
      	'con_cnpj',
      	'con_uf',
	];

	protected static function boot()
    {
        parent::boot();

        static::creating(function (Concessionaria $concessionaria) {
            $concessionaria->uuid_con_id = Str::uuid()->toString();
        });
    }
}