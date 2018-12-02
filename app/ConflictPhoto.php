<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConflictPhoto extends Model
{
    protected $fillable = [
        'comment_id',
        'url',
    ];

    public function comment ()
    {
        return $this->belongsTo(Comment::class);
    }
}
