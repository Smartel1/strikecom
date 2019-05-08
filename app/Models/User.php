<?php

namespace App\Models;

use App\Models\Comment;
use App\Models\Conflict;
use App\Models\Event;
use App\Models\News;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'uuid', 'image_url', 'admin', 'push', 'reward'
    ];

    protected $dateFormat = 'U';

    protected $casts = [
        'created_at' => 'integer',
        'updated_at' => 'integer',
        'reward'     => 'integer',
        'admin'      => 'boolean',
        'push'       => 'boolean',
    ];

    public function conflicts()
    {
        return $this->hasMany(Conflict::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function news()
    {
        return $this->hasMany(News::class);
    }

    public function favouriteEvents()
    {
        return $this->belongsToMany(Event::class, 'favourite_events');
    }

    public function favouriteNews()
    {
        return $this->belongsToMany(News::class, 'favourite_news');
    }
}
