<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConflictResult extends Model
{
    protected $fillable = [
        'name', 'code'
    ];
}
