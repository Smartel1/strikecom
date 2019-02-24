<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'title',
        'content',
        'date',
        'views',
        'source_link',
        'event_type_id',
        'event_status_id',
        'user_id',
    ];

    protected $dateFormat = 'U';

    protected $dates = ['date'];

    protected $casts = [
        'latitude'   => 'double',
        'longitude'  => 'double',
        'date'       => 'integer',
        'user_id'    => 'integer',
        'created_at' => 'integer',
        'updated_at' => 'integer'
    ];

    public function status()
    {
        return $this->belongsTo(EventStatus::class);
    }

    public function type()
    {
        return $this->belongsTo(EventType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->belongsToMany(Comment::class);
    }

    public function photos()
    {
        return $this->belongsToMany(Photo::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function videos()
    {
        return $this->belongsToMany(Video::class);
    }
}
