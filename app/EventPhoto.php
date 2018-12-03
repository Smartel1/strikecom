<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventPhoto extends Model
{
    protected $fillable = [
        'event_id',
        'url',
    ];

    public function event ()
    {
        return $this->belongsTo(Event::class);
    }
}
