<?php

namespace App\Http\Resources\Event;

use App\Http\Resources\Comment\CommentResource;
use App\Http\Resources\Conflict\ConflictDetailResource;
use Illuminate\Http\Resources\Json\Resource;

class EventDetailResource extends Resource
{
    /**
     * Структура ответа на запрос деталки события
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $structure = [
            'id'               => $this->id,
            'date'             => $this->date,
            'views'            => $this->views,
            'source_link'      => $this->source_link,
            'conflict_id'      => $this->conflict_id,
            'event_status_id'  => $this->event_status_id,
            'event_type_id'    => $this->event_type_id,
            'photos'           => $this->photos->pluck('url'),
            'videos'           => $this->videos->makeHidden(['id', 'created_at', 'updated_at']),
            'tags'             => $this->tags->pluck('name'),
            'user'    => $this->user_id ? [
                'id'     => $this->user->id,
                'name'   => $this->user->name,
                'email'  => $this->user->email
            ] : null,
            'conflict'         => ConflictDetailResource::make($this->conflict),
            'comments'         => CommentResource::collection($this->comments),
        ];

        $locale = app('locale');

        if ($locale !== 'all') {
            $structure['title'] = $this['title_'.$locale];
            $structure['content'] = $this['content_'.$locale];
        } else {
            $structure['title_ru'] = $this['title_ru'];
            $structure['title_en'] = $this['title_en'];
            $structure['title_es'] = $this['title_es'];
            $structure['content_ru'] = $this['content_ru'];
            $structure['content_en'] = $this['content_en'];
            $structure['content_es'] = $this['content_es'];
        }

        return $structure;
    }
}
