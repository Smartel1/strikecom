<?php

namespace App\Services;


use App\Event;
use App\Tag;

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
}