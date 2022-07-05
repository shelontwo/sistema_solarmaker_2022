<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Log extends Model
{
    use HasFactory;

    protected $primaryKey = 'log_id';

    protected $table = 'logs';

    const CREATED_AT = 'log_criado_em';

    const UPDATED_AT = 'log_atualizado_em';

	protected $fillable = [
        'uuid_log_id',
        'fk_usu_id_usuario',
        'log_url',
        'log_method',
        'log_request_json',
        'log_response_json',
        'log_status',
        'log_ip_address'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Log $log) {
            $log->uuid_log_id = Str::uuid()->toString();
        });
    }

    public function user()
	{
		return $this->hasOne(User::class, 'fk_user_id');
	}
}
