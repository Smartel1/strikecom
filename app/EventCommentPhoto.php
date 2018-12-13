<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventCommentPhoto extends Model
{
    protected $fillable = [
        'event_comment_id',
        'url',
    ];

    protected $dateFormat = 'U';

    protected $casts = [
        'created_at' =>'integer',
        'updated_at' =>'integer',
    ];

    public function comment ()
    {
        return $this->belongsTo(EventComment::class);
    }
}
