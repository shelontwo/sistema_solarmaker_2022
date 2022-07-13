<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Modulo extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'mod_id';

    protected $table = 'modulos';

    const UUID = 'uuid_mod_id';

    const CREATED_AT = 'mod_criado_em';

    const UPDATED_AT = 'mod_atualizado_em';

	protected $fillable = [
		'uuid_mod_id',
      	'mod_nome',
      	'mod_ordem',
      	'mod_icone',
        'fk_mod_id_modulo'
	];

	protected static function boot()
    {
        parent::boot();

        static::creating(function (Modulo $modulo) {
            $modulo->uuid_mod_id = Str::uuid()->toString();
        });
    }
}