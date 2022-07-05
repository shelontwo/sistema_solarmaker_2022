<?php

namespace App;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
class User extends Authenticatable implements JWTSubject
{
    use Notifiable, SoftDeletes;


    protected $primaryKey = 'gru_id';

    protected $table = 'grupos';

    const CREATED_AT = 'usu_criado_em';

    const UPDATED_AT = 'usu_atualizado_em';
    
    const DELETED_AT = 'usu_deletado_em';

    protected $fillable = [
        'uuid_usu_id',
        'usu_nome',
        'usu_apelido',
        'usu_email',
        'usu_senha',
        'fk_gru_id_grupo',
        'usu_imagem',
        'usu_token'
    ];

    protected $hidden = [
        'usu_senha'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (User $USER) {
            $USER->uuid_usu_id = Str::uuid()->toString();
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
