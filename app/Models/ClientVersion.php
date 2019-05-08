<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientVersion extends Model
{
    protected $fillable = [
        'version', 'client_id', 'required',
        'description_ru', 'description_en', 'description_es',
    ];

    protected $dateFormat = 'U';
}
