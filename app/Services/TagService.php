<?php

namespace App\Services;


use App\Models\Event;
use App\Models\News;
use App\Models\Tag;

class TagService
{
    public function updateEventTags (Event $event, array $tagNames)
    {
        $tags = collect();

        foreach ((array)$tagNames as $tagName) {

            $tags->push(Tag::firstOrCreate(['name'=>$tagName]));

        }

        $event->tags()->sync($tags->pluck('id'));
    }

    public function updateNewsTags (News $news, array $tagNames)
    {
        $tags = collect();

        foreach ((array)$tagNames as $tagName) {

            $tags->push(Tag::firstOrCreate(['name'=>$tagName]));

        }

        $news->tags()->sync($tags->pluck('id'));
    }
}