<?php

namespace App\Models;

use App\Models\Usina;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UsinaProducao extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'upr_id';

    protected $table = 'usinas_producao';

    const UUID = 'uuid_upr_id';

    const CREATED_AT = 'upr_criado_em';

    const UPDATED_AT = 'upr_atualizado_em';

    const DELETED_AT = 'upr_deletado_em';

	protected $fillable = [
		'uuid_upr_id',
      	'upr_data',
        'upr_hora',
        'upr_producao',
        'upr_tipo',
        'fk_usi_id_usina'
	];

	protected static function boot()
    {
        parent::boot();

        static::creating(function (UsinaProducao $usinaProducao) {
            $usinaProducao->uuid_upr_id = Str::uuid()->toString();
        });
    }

    public function usina()
    {
        return $this->belongsTo(Usina::class, 'fk_usi_id_usina')->select('uuid_usi_id', 'usi_id', 'usi_nome');
    }
}
