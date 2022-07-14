<?php

namespace App\Models;

use App\Models\Usina;
use App\Models\Inversor;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UsinaInversor extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'inu_id';

    protected $table = 'usinas_inversor';

    const UUID = 'uuid_inu_id';

    const CREATED_AT = 'inu_criado_em';

    const UPDATED_AT = 'inu_atualizado_em';

    const DELETED_AT = 'inu_deletado_em';

	protected $fillable = [
		'uuid_inu_id',
      	'inu_painel_quantidade',
        'inu_painel_potencia',
        'fk_usi_id_usina',
        'fk_inv_id_inversor'
	];

	protected static function boot()
    {
        parent::boot();

        static::creating(function (UsinaInversor $usinaInversor) {
            $usinaInversor->uuid_inu_id = Str::uuid()->toString();
        });
    }

    public function usina()
    {
        return $this->belongsTo(Usina::class, 'fk_usi_id_usina')->select('uuid_usi_id', 'usi_id', 'usi_nome');
    }

    public function inversor()
    {
        return $this->belongsTo(Inversor::class, 'fk_inv_id_inversor')->select('uuid_inv_id', 'inv_id', 'inv_marca', 'inv_modelo', 'inv_potencia', 'inv_serial');
    }
}
