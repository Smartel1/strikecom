<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'content',
        'date',
        'views',
        'source_link',
        'conflict_id',
        'event_type_id',
        'event_status_id',
        'user_id',
    ];

    protected $dateFormat = 'U';

    protected $dates = ['date'];

    protected $casts = [
        'latitude' => 'double',
        'longitude' => 'double',
    ];

    public function conflict()
    {
        return $this->belongsTo(Conflict::class);
    }

    public function status()
    {
        return $this->belongsTo(EventStatus::class);
    }

    public function type ()
    {
        return $this->belongsTo(EventType::class);
    }

    public function user ()
    {
        return $this->belongsTo(User::class);
    }

    public function comments ()
    {
        return $this->hasMany(EventComment::class);
    }

    public function photos ()
    {
        return $this->hasMany(EventPhoto::class);
    }

    public function tags ()
    {
        return $this->belongsToMany(Tag::class);
    }
}
