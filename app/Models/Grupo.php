<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grupo extends Model
{
	use HasFactory, SoftDeletes;
	
	protected $primaryKey = 'gru_id';

    protected $table = 'grupos';

    const UUID = 'uuid_gru_id';

    const CREATED_AT = 'gru_criado_em';

    const UPDATED_AT = 'gru_atualizado_em';
	
    const DELETED_AT = 'gru_deletado_em';

	protected $fillable = [
		'uuid_gru_id',
      	'gru_nome'
	];

	protected $hidden = [
        'gru_id',
    ];

	protected static function boot()
    {
        parent::boot();

        static::creating(function (Grupo $grupo) {
            $grupo->uuid_gru_id = Str::uuid()->toString();
        });
    }

	/**
	 * The events that belong to the usergroup.
	 */
	public function modules()
	{
		return $this->belongsToMany(Module::class, 'group_module');
	}

	/**
	 * The users that belong to the usergroup
	 */
	public function users()
	{
		return $this->hasMany(User::class);
	}
}