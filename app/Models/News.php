<?php

namespace App\Models;

use App\Models\Comment;
use App\Models\EventStatus;
use App\Models\EventType;
use App\Models\Photo;
use App\Models\Tag;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected static function boot()
    {
        parent::boot();

        //При сохранении модели мы поле title перезаписываем в поле title_ru [en/es]
        self::creating(function($model){

            $locale = app('locale');

            if (array_has($model, 'title') and $locale !== 'all') {
                $model["title_$locale"] = $model['title'];
            }

            unset($model['title']);

            if (array_has($model, 'content') and $locale !== 'all') {
                $model["content_$locale"] = $model['content'];
            }

            unset($model['content']);
        });

        self::updating(function($model){

            $locale = app('locale');

            if (array_has($model, 'title') and $locale !== 'all') {
                $model["title_$locale"] = $model['title'];
            }

            unset($model['title']);

            if (array_has($model, 'content') and $locale !== 'all') {
                $model["content_$locale"] = $model['content'];
            }

            unset($model['content']);
        });
    }

    protected $fillable = [
        'title',
        'title_ru',
        'title_en',
        'title_es',
        'content',
        'content_ru',
        'content_en',
        'content_es',
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
