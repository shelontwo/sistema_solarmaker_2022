<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Configuracao extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $primaryKey = 'con_id';

    protected $table = 'configuracoes';

    const UUID = 'uuid_con_id';

    const CREATED_AT = 'con_criado_em';

    const UPDATED_AT = 'con_atualizado_em';
    
    const DELETED_AT = 'con_deletado_em';

	protected $fillable = [
		'uuid_con_id',
      	'con_canal_id',
      	'con_tipo',
        'con_ativo',
      	'fk_dis_id_distribuidor',
        'fk_int_id_integrador'
	];

	protected static function boot()
    {
        parent::boot();

        static::creating(function (Configuracao $configuracao) {
            $configuracao->uuid_con_id = Str::uuid()->toString();
        });
    }

    public function distribuidor()
    {
        return $this->belongsTo(Distribuidor::class, 'fk_dis_id_distribuidor')->select('uuid_dis_id', 'dis_id', 'dis_nome');
    }

    public function integrador()
    {
        return $this->belongsTo(Integrador::class, 'fk_int_id_integrador')->select('uuid_int_id', 'int_id', 'int_nome');
    }
}