<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\UnidadeConsumidora;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnidadeConsumidoraFatura extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'ucf_id';

    protected $table = 'unidades_consumidoras_fatura';

    const UUID = 'uuid_ucf_id';

    const CREATED_AT = 'ucf_criado_em';

    const UPDATED_AT = 'ucf_atualizado_em';

    const DELETED_AT = 'ucf_deletado_em';

	protected $fillable = [
		'uuid_ucf_id',
        'ucf_valor_faturado',
      	'ucf_inicio_ciclo',
      	'ucf_fim_ciclo',
      	'ucf_valor_tarifa',
      	'ucf_consumida',
      	'ucf_faturada',
      	'ucf_tarifa',
      	'ucf_energia',
      	'ucf_energia_injetada',
      	'ucf_situacao',
      	'ucf_nome_arquivo',
      	'ucf_arquivo',
        'fk_uco_id_unidade',
	];

	protected static function boot()
    {
        parent::boot();

        static::creating(function (UnidadeConsumidoraFatura $unidadeConsumidoraFatura) {
            $unidadeConsumidoraFatura->uuid_ucf_id = Str::uuid()->toString();
        });
    }

    public function unidade()
    {
        return $this->belongsTo(UnidadeConsumidora::class, 'fk_uco_id_unidade')->select('uuid_uco_id', 'uco_id', 'uco_nome');
    }
}
