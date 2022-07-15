<?php

namespace App\Models;

use App\Models\Usina;
use Illuminate\Support\Str;
use App\Models\UnidadeConsumidora;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnidadeConsumidoraCredito extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'ucc_id';

    protected $table = 'unidades_consumidoras_credito';

    const UUID = 'uuid_ucc_id';

    const CREATED_AT = 'ucc_criado_em';

    const UPDATED_AT = 'ucc_atualizado_em';

    const DELETED_AT = 'ucc_deletado_em';

	protected $fillable = [
		'uuid_ucc_id',
        'ucc_quantidade',
      	'ucc_vigencia',
      	'ucc_posto_tarifario',
      	'ucc_observacao',
        'fk_usi_id_usina',
        'fk_uco_id_unidade',
	];

	protected static function boot()
    {
        parent::boot();

        static::creating(function (UnidadeConsumidoraCredito $unidadeConsumidoraCredito) {
            $unidadeConsumidoraCredito->uuid_ucc_id = Str::uuid()->toString();
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
}
