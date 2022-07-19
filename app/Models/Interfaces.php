<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Interfaces extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'ite_id';

    protected $table = 'interfaces';

    const UUID = 'uuid_ite_id';

    const CREATED_AT = 'ite_criado_em';

    const UPDATED_AT = 'ite_atualizado_em';
    
    const DELETED_AT = 'ite_deletado_em';

	protected $fillable = [
		'uuid_ite_id',
      	'ite_data',
      	'ite_nsu',
      	'ite_usuario',
      	'ite_senha',
        'fk_usi_id_usina',
        'fk_uco_id_unidade',
        'fk_int_id_integrador',
	];

	protected static function boot()
    {
        parent::boot();

        static::creating(function (Interfaces $Interface) {
            $Interface->uuid_ite_id = Str::uuid()->toString();
        });
    }
}