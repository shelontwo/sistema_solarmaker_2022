<?php

namespace App\Models;

use App\Models\Usina;
use App\Models\Integrador;
use Illuminate\Support\Str;
use App\Models\UnidadeConsumidora;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Interfaces extends Model
{
    use HasFactory, SoftDeletes;
    
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

    public function usina()
    {
        return $this->belongsTo(Usina::class, 'fk_usi_id_usina')->select('uuid_usi_id', 'usi_id', 'usi_nome');
    }

    public function unidade()
    {
        return $this->belongsTo(UnidadeConsumidora::class, 'fk_uco_id_unidade')->select('uuid_uco_id', 'uco_id', 'uco_nome');
    }

    public function integrador()
    {
        return $this->belongsTo(Integrador::class, 'fk_int_id_integrador')->select('uuid_int_id', 'int_id', 'int_nome');
    }
}