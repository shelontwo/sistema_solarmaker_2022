<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = "banner";
    protected $fillable = [
        'image',
        'mobile_image',
        'active',
        'description',
        'link',
        'popup',
        'button',
        'turn',
        'title',
        'start_date',
        'end_date'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
