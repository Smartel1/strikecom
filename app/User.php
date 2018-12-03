<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'uid', 'image_url', 'admin'
    ];

    public function conflicts ()
    {
        return $this->hasMany(Conflict::class);
    }

    public function comments ()
    {
        return $this->hasMany(EventComment::class);
    }

    public function events ()
    {
        return $this->hasMany(Event::class);
    }
}
