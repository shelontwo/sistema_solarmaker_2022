<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Group extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
      'name',
	];

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