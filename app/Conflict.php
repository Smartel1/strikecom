<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conflict extends Model
{
    protected $fillable = [
        'title',
        'description',
        'content',
        'latitude',
        'longitude',
        'date_from',
        'date_to',
        'views',
        'source_link',
        'conflict_status_id',
        'conflict_type_id',
        'conflict_reason_id',
        'conflict_result_id',
        'industry_id',
        'region_id',
        'user_id',
    ];

    protected $casts = [
        'latitude' => 'double',
        'longitude' => 'double',
    ];

    protected $dates = ['date_from', 'date_to'];

    public function user ()
    {
        return $this->belongsTo(User::class);
    }

    public function comments ()
    {
        return $this->hasMany(Comment::class);
    }

    public function tags ()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function conflictPhotos ()
    {
        return $this->hasMany(ConflictPhoto::class);
    }

    public function syncTagsFromArray (array $tagNames = [])
    {
        $tags = collect();

        foreach ($tagNames as $tagName) {

            $tagName = strtolower($tagName);

            $tagName = str_replace(' ', '_', $tagName);

            $tags->push(Tag::firstOrCreate(['name' => $tagName]));
        }

        $this->tags()->sync($tags->pluck('id'));
    }

    public function syncImageUrlsFromArray (array $urls = [])
    {
        $this->conflictPhotos()->delete();

        foreach ($urls as $url) {
            $this->conflictPhotos()->create([
                'url' => $url,
            ]);
        }
    }
}
