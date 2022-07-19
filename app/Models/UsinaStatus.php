<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UsinaStatus extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $primaryKey = 'uss_id';

    protected $table = 'usinas_status';

    const UUID = 'uuid_uss_id';

    const CREATED_AT = 'uss_criado_em';

    const UPDATED_AT = 'uss_atualizado_em';
    
    const DELETED_AT = 'uss_deletado_em';

	protected $fillable = [
		'uuid_uss_id',
      	'uss_nome',
      	'uss_tipo',
        'uss_cor'
	];

	protected static function boot()
    {
        parent::boot();

        static::creating(function (UsinaStatus $usinaStatus) {
            $usinaStatus->uuid_uss_id = Str::uuid()->toString();
        });
    }
}