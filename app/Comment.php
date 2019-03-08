<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'event_id',
        'content',
    ];

    protected $dateFormat = 'U';

    protected $hidden = ['pivot'];

    protected $casts = [
        'created_at' => 'integer',
        'updated_at' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function events()
    {
        return $this->belongsToMany(Event::class);
    }

    public function news()
    {
        return $this->belongsToMany(Event::class);
    }

    public function photos()
    {
        return $this->belongsToMany(Photo::class);
    }
}
