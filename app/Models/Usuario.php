<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $primaryKey = 'usu_id';

    protected $table = 'usuarios';

    const UUID = 'uuid_usu_id';
    
    const CREATED_AT = 'usu_criado_em';

    const UPDATED_AT = 'usu_atualizado_em';
    
    const DELETED_AT = 'usu_deletado_em';

    protected $fillable = [
        'uuid_usu_id',
        'usu_nome',
        'usu_apelido',
        'usu_email',
        'password',
        'fk_gru_id_grupo',
        'fk_dis_id_distribuidor',
        'fk_int_id_integrador',
        'usu_imagem',
        'usu_token'
    ];

    protected $hidden = [
        'usu_id',
        'password'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Usuario $usuario) {
            $usuario->uuid_usu_id = Str::uuid()->toString();
        });
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /*
    * Get the group that the user belongs to
    */
    public function group()
    {
        return $this->belongsTo(Group::class, 'fk_group_id');
    }
}