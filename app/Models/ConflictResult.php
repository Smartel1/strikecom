<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConflictResult extends Model
{
    protected $fillable = [
        'name_ru', 'name_en', 'name_es',
    ];

    public $timestamps = false;
}
