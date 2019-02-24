<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $fillable = [
        'url',
    ];

    protected $hidden = ['pivot'];

    protected $dateFormat = 'U';

    protected $casts = [
        'created_at'    => 'integer',
        'updated_at'    => 'integer',
    ];

    public function events()
    {
        return $this->belongsToMany(Event::class);
    }

    public function news()
    {
        return $this->belongsToMany(News::class);
    }

    public function comments()
    {
        return $this->belongsToMany(Comment::class);
    }
}
