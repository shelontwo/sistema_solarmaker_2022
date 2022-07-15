<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\Concessionaria;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnidadeConsumidora extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'uco_id';

    protected $table = 'unidades_consumidoras';

    const UUID = 'uuid_uco_id';

    const CREATED_AT = 'uco_criado_em';

    const UPDATED_AT = 'uco_atualizado_em';

	protected $fillable = [
		'uuid_uco_id',
      	'uco_codigo',
      	'uco_nome',
      	'uco_classificacao',
      	'uco_tipo',
      	'uco_modalidade',
        'fk_con_id_concessionaria'
	];

	protected static function boot()
    {
        parent::boot();

        static::creating(function (UnidadeConsumidora $unidadeConsumidora) {
            $unidadeConsumidora->uuid_uco_id = Str::uuid()->toString();
        });
    }

    public function concessionaria()
    {
        return $this->belongsTo(Concessionaria::class, 'fk_con_id_concessionaria')->select('uuid_con_id', 'con_id', 'con_nome');
    }
}