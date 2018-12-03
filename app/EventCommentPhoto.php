<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventCommentPhoto extends Model
{
    protected $fillable = [
        'event_comment_id',
        'url',
    ];

    public function comment ()
    {
        return $this->belongsTo(EventComment::class);
    }
}
