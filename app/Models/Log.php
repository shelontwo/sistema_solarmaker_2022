<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
        'fk_user_id',
        'url',
        'method',
        'request_json',
        'response_json',
        'status',
        'ip_address'
    ];

    public function user()
	{
		return $this->hasOne(User::class, 'fk_user_id');
	}
}
