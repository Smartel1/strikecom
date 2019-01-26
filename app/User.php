<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'uid', 'image_url', 'admin'
    ];

    protected $dateFormat = 'U';

    protected $casts = [
        'created_at' =>'integer',
        'updated_at' =>'integer',
    ];

    public function conflicts ()
    {
        return $this->hasMany(Conflict::class);
    }

    public function comments ()
    {
        return $this->hasMany(Comment::class);
    }

    public function events ()
    {
        return $this->hasMany(Event::class);
    }

    public function news ()
    {
        return $this->hasMany(News::class);
    }
}
