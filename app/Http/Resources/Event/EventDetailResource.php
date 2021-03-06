<?php

namespace App\Http\Resources\Event;

use App\Entities\Event;
use App\Entities\Photo;
use App\Entities\Tag;
use App\Entities\Video;
use App\Http\Resources\Conflict\ConflictDetailResource;
use App\Http\Resources\Reference\LocalityResourceExtended;
use App\Services\EventService;
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
        $event = $this->resource;
        $eventLocality = $event->getLocality();

        $structure = [
            'id'              => $event->getId(),
            'published'       => $event->isPublished(),
            'date'            => $event->getDate(),
            'views'           => $event->getViews(),
            'latitude'        => $event->getLatitude(),
            'longitude'       => $event->getLongitude(),
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
                    'video_type_id' => $video->getVideoType()->getId(),
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
            'locality'        => $eventLocality ? new LocalityResourceExtended($eventLocality) : null,
        ];

        $locale = app('locale');
        /**
         * Если передана конкретная локаль, то возвращаем поля title, content и country на нужном языке
         * Иначе возвращаем title_ru, title_en, title_es и content_ru, content_es, content_es
         */
        if ($locale !== 'all') {
            $structure['title'] = $event->getTitleByLocale($locale);
            $structure['content'] = $event->getContentByLocale($locale);

        } else {
            $structure['title_ru'] = $event->getTitleRu();
            $structure['title_en'] = $event->getTitleEn();
            $structure['title_es'] = $event->getTitleEs();
            $structure['content_ru'] = $event->getContentRu();
            $structure['content_en'] = $event->getContentEn();
            $structure['content_es'] = $event->getContentEs();
        }

        if ($request->get('withRelatives')) {
            /** @var EventService $eventService */
            $eventService = app(EventService::class);

            $structure['relatives'] = $eventService->getRelatives($event, $locale);
        }
        return $structure;
    }
}
