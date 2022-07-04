<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Module extends Model
{
    public static function getModules(){
    	return (new static)::whereRaw('LENGTH(father_path) = 0 OR father_path IS NULL ')
    			->orderBy('father_order','ASC')
    			->get();
    }
    
	/**
     * The module's submodules
     */
    public function submodules()
    {
        return $this->hasMany(Module::class, 'father_path', 'path')->orderBy('order');
    }

    /**
     * The events that belong to the usergroup.
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_module');
    }
}
