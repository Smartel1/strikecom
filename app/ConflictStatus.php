<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConflictStatus extends Model
{
    protected $fillable = [
        'name', 'code'
    ];
}
