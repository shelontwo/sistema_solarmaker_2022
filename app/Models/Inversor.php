<?php

namespace App\Models;

use App\Models\Integrador;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inversor extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'inv_id';

    protected $table = 'inversores';

    const UUID = 'uuid_inv_id';

    const CREATED_AT = 'inv_criado_em';

    const UPDATED_AT = 'inv_atualizado_em';

    const DELETED_AT = 'inv_deletado_em';

	protected $fillable = [
		'uuid_inv_id',
      	'inv_marca',
        'inv_modelo',
        'inv_status',
        'inv_potencia',
        'inv_serial',
        'inv_garantia',
        'fk_int_id_integrador'
	];

	protected static function boot()
    {
        parent::boot();

        static::creating(function (Inversor $inversor) {
            $inversor->uuid_inv_id = Str::uuid()->toString();
        });
    }

    public function integrador()
    {
        return $this->belongsTo(Integrador::class, 'fk_int_id_integrador')->select('uuid_int_id', 'int_id', 'int_nome');
    }
}
