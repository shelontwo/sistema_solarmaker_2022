<?php

namespace App\Models;

use App\Models\Usina;
use Illuminate\Support\Str;
use App\Models\UnidadeConsumidora;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UsinaSistemaCredito extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'usc_id';

    protected $table = 'usinas_sitema_credito';

    const UUID = 'uuid_usc_id';

    const CREATED_AT = 'usc_criado_em';

    const UPDATED_AT = 'usc_atualizado_em';

    const DELETED_AT = 'usc_deletado_em';

	protected $fillable = [
		'uuid_usc_id',
      	'usc_vigencia',
        'usc_percentual',
        'fk_usi_id_usina',
        'fk_uco_id_unidade',
	];

	protected static function boot()
    {
        parent::boot();

        static::creating(function (UsinaSistemaCredito $usinaSistemaCredito) {
            $usinaSistemaCredito->uuid_usc_id = Str::uuid()->toString();
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
