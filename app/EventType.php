<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    protected $fillable = [
        'name_ru', 'name_en', 'name_es',
    ];

    public $timestamps = false;
}
