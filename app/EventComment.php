<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventComment extends Model
{
    protected $fillable = [
        'user_id',
        'event_id',
        'content',
    ];

    public function user ()
    {
        return $this->belongsTo(User::class);
    }

    public function event ()
    {
        return $this->belongsTo(Event::class);
    }

    public function commentPhotos ()
    {
        return $this->hasMany(EventCommentPhoto::class);
    }
}
