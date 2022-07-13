<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GrupoModulo extends Model
{
	use HasFactory;
	
	protected $primaryKey = 'gmo_id';

    protected $table = 'grupos_modulos';

    const CREATED_AT = 'gmo_criado_em';

    const UPDATED_AT = 'gmo_atualizado_em';
	

	protected $fillable = [
      	'fk_mod_id_modulo',
      	'fk_gru_id_grupo',
	];

    protected $hidden = [
        'gmo_id'
    ];
}