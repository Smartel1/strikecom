<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conflict extends Model
{
    protected $fillable = [
        'title',
        'latitude',
        'longitude',
        'company_name',
        'date_from',
        'date_to',
        'conflict_reason_id',
        'conflict_result_id',
        'industry_id',
        'region_id',
    ];

    protected $casts = [
        'latitude' => 'double',
        'longitude' => 'double',
    ];

    protected $dates = ['date_from', 'date_to'];

    public function events ()
    {
        return $this->hasMany(Event::class);
    }
}
