<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'conflict_id',
        'content',
    ];

    public function user ()
    {
        return $this->belongsTo(User::class);
    }

    public function conflict ()
    {
        return $this->belongsTo(Conflict::class);
    }

    public function commentPhotos ()
    {
        return $this->hasMany(CommentPhoto::class);
    }
}
