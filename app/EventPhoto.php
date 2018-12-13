<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventPhoto extends Model
{
    protected $fillable = [
        'event_id',
        'url',
    ];

    protected $dateFormat = 'U';

    protected $casts = [
        'created_at' =>'integer',
        'updated_at' =>'integer',
    ];

    public function event ()
    {
        return $this->belongsTo(Event::class);
    }
}
