<?php

namespace App\Models;

use App\Models\Usina;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UsinaIndicador extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'uin_id';

    protected $table = 'usinas_indicador';

    const UUID = 'uuid_uin_id';

    const CREATED_AT = 'uin_criado_em';

    const UPDATED_AT = 'uin_atualizado_em';

    const DELETED_AT = 'uin_deletado_em';

	protected $fillable = [
		'uuid_uin_id',
      	'uin_data',
        'uin_campo',
        'uin_valor',
        'fk_usi_id_usina'
	];

	protected static function boot()
    {
        parent::boot();

        static::creating(function (UsinaIndicador $usinaIndicador) {
            $usinaIndicador->uuid_uin_id = Str::uuid()->toString();
        });
    }

    public function usina()
    {
        return $this->belongsTo(Usina::class, 'fk_usi_id_usina')->select('uuid_usi_id', 'usi_id', 'usi_nome');
    }
}
