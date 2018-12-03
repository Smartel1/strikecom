<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventStatus extends Model
{
    protected $fillable = [
        'name', 'code'
    ];
}
