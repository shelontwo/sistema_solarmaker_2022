<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Distribuidor extends Model
{
    use HasFactory;

    protected $primaryKey = 'dis_id';

    protected $table = 'distribuidores';

    const UUID = 'uuid_dis_id';

    const CREATED_AT = 'dis_criado_em';

    const UPDATED_AT = 'dis_atualizado_em';

    const DELETED_AT = 'dis_deletado_em';

	protected $fillable = [
		'uuid_dis_id',
      	'dis_nome'
	];

	protected $hidden = [
        'dis_id',
    ];

	protected static function boot()
    {
        parent::boot();

        static::creating(function (Distribuidor $distribuidor) {
            $distribuidor->uuid_dis_id = Str::uuid()->toString();
        });
    }
}
