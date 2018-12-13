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
        return $this->hasMany(EventComment::class);
    }

    public function events ()
    {
        return $this->hasMany(Event::class);
    }
}
