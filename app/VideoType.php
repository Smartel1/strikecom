<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VideoType extends Model
{
    protected $fillable = [
        'code'
    ];

    public $timestamps = false;
}
