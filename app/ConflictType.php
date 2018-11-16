<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConflictType extends Model
{
    protected $fillable = [
        'name', 'code'
    ];
}
