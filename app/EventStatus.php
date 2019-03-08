<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventStatus extends Model
{
    protected $fillable = [
        'name_ru', 'name_en', 'name_es',
    ];

    public $timestamps = false;
}
