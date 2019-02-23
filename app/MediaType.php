<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MediaType extends Model
{
    protected $fillable = [
        'name',
    ];

    public $timestamps = false;
}
