<?php

namespace App\Models;

use App\Models\Usina;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UsinaProjeto extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'usp_id';

    protected $table = 'usinas_projeto';

    const UUID = 'uuid_usp_id';

    const CREATED_AT = 'usp_criado_em';

    const UPDATED_AT = 'usp_atualizado_em';

    const DELETED_AT = 'usp_deletado_em';

	protected $fillable = [
		'uuid_usp_id',
      	'usp_potencia',
        'usp_eficiencia',
        'usp_data_instalacao',
        'usp_total_investido',
        'usp_tarifa_referencia',
        'usp_rsi_jan',
        'usp_rsi_fev',
        'usp_rsi_mar',
        'usp_rsi_abr',
        'usp_rsi_mai',
        'usp_rsi_jun',
        'usp_rsi_jul',
        'usp_rsi_ago',
        'usp_rsi_set',
        'usp_rsi_out',
        'usp_rsi_nov',
        'usp_rsi_dez',
        'fk_usi_id_usina'
	];

	protected static function boot()
    {
        parent::boot();

        static::creating(function (UsinaProjeto $sinaProjeto) {
            $sinaProjeto->uuid_usp_id = Str::uuid()->toString();
        });
    }

    public function usina()
    {
        return $this->belongsTo(Usina::class, 'fk_usi_id_usina')->select('uuid_usi_id', 'usi_id', 'usi_nome', 'fk_uss_id_status');
    }
}
