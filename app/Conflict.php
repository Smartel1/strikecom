<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conflict extends Model
{
    protected $fillable = [
        'name',
        'description',
        'content',
        'latitude',
        'longitude',
        'date_from',
        'date_to',
        'views',
        'source_link',
        'conflict_status_id',
        'conflict_type_id',
        'conflict_reason_id',
        'conflict_result_id',
        'industry_id',
        'region_id',
    ];

    protected $dates = ['date_from', 'date_to'];
}
