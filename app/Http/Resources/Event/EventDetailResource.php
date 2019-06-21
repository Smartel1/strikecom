<?php

namespace App\Http\Resources\Event;

use App\Entities\Event;
use App\Entities\Photo;
use App\Entities\Tag;
use App\Entities\Video;
use App\Http\Resources\Conflict\ConflictDetailResource;
use Illuminate\Http\Resources\Json\Resource;

class EventDetailResource extends Resource
{
    /**
     * Структура ответа на запрос деталки события
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var $event Event */
        $event = $this;

        $structure = [
            'id'              => $event->getId(),
            'published'       => $event->isPublished(),
            'date'            => $event->getDate(),
            'views'           => $event->getViews(),
            'source_link'     => $event->getSourceLink(),
            'conflict_id'     => $event->getConflict()->getId(),
            'event_status_id' => $event->getEventStatus() ? $event->getEventStatus()->getId() : null,
            'event_type_id'   => $event->getEventType() ? $event->getEventType()->getId() : null,
            'photos'          => $event->getPhotos()->map(function (Photo $photo) {
                return $photo->getUrl();
            })->getValues(),
            'videos'          => $event->getVideos()->map(function (Video $video) {
                return [
                    'url'           => $video->getUrl(),
                    'preview_url'   => $video->getPreviewUrl(),
                    'video_type_id' => $video->getVideoTypeId(),
                ];
            })->getValues(),
            'tags'            => $event->getTags()->map(function (Tag $tag) {
                return $tag->getName();
            })->getValues(),
            'author'          => $event->getAuthor() ? [
                'id'    => $event->getAuthor()->getId(),
                'name'  => $event->getAuthor()->getName(),
                'email' => $event->getAuthor()->getEmail()
            ] : null,
            'conflict'        => ConflictDetailResource::make($event->getConflict()),
            'comments_count'  => $event->getComments()->count(),
        ];

        $locale = app('locale');
        $eventLocality = $event->getLocality();
        /**
         * Если передана конкретная локаль, то возвращаем поля title и content на нужном языке
         * Иначе возвращаем title_ru, title_en, title_es и content_ru, content_es, content_es
         */
        if ($locale !== 'all') {
            $structure['title'] = $event->getTitleByLocale($locale);
            $structure['content'] = $event->getContentByLocale($locale);

            /*
             * Если событие привязано к месту, то отображаем локализованные названия места/региона/страны, иначе null
             */
            $structure['locality'] = $eventLocality ? $eventLocality->getNameByLocale($locale) : null;
            $structure['region'] = $eventLocality ? $eventLocality->getRegion()->getNameByLocale($locale) : null;
            $structure['country'] = $eventLocality ? $eventLocality->getRegion()->getCountry()->getNameByLocale($locale) : null;

        } else {
            $structure['title_ru'] = $event->getTitleRu();
            $structure['title_en'] = $event->getTitleEn();
            $structure['title_es'] = $event->getTitleEs();
            $structure['content_ru'] = $event->getContentRu();
            $structure['content_en'] = $event->getContentEn();
            $structure['content_es'] = $event->getContentEs();

            /*
             * Если событие привязано к месту, то отображаем названия места/региона/страны, иначе null
             */
            $structure['locality_ru'] = $eventLocality ? $eventLocality->getNameRu() : null;
            $structure['locality_en'] = $eventLocality ? $eventLocality->getNameEn() : null;
            $structure['locality_es'] = $eventLocality ? $eventLocality->getNameEs() : null;
            $structure['region_ru'] = $eventLocality ? $eventLocality->getRegion()->getNameRu() : null;
            $structure['region_en'] = $eventLocality ? $eventLocality->getRegion()->getNameEn() : null;
            $structure['region_es'] = $eventLocality ? $eventLocality->getRegion()->getNameEs() : null;
            $structure['country_ru'] = $eventLocality ? $eventLocality->getRegion()->getCountry()->getNameRu() : null;
            $structure['country_en'] = $eventLocality ? $eventLocality->getRegion()->getCountry()->getNameEn() : null;
            $structure['country_es'] = $eventLocality ? $eventLocality->getRegion()->getCountry()->getNameEs() : null;
        }

        return $structure;
    }
}
