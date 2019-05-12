<?php

namespace App\Http\Resources\News;

use App\Entities\Photo;
use App\Entities\Tag;
use App\Entities\Video;
use Illuminate\Http\Resources\Json\Resource;

class NewsIndexResourceDoctrine extends Resource
{
    /**
     * Структура ответа на запрос списка новостей
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /**
         * Сервис выполняет dirty запрос, поэтому новость с индексом 0
         */
        $structure = [
            'id'          => $this[0]->getId(),
            'date'        => $this[0]->getDate(),
            'views'       => $this[0]->getViews(),
            'source_link' => $this[0]->getSourceLink(),
            'photos'      => $this[0]->getPhotos()->map(function (Photo $photo) {
                return $photo->getUrl();
            })->getValues(),
            'videos'      => $this[0]->getVideos()->map(function (Video $video) {
                return [
                    'url'           => $video->getUrl(),
                    'preview_url'   => $video->getPreviewUrl(),
                    'video_type_id' => $video->getVideoTypeId(),
                ];
            })->getValues(),
            'tags'        => $this[0]->getTags()->map(function (Tag $tag) {
                return $tag->getName();
            })->getValues(),
            'comments_count'   => $this['comments_count'],
        ];

        $locale = app('locale');
        /**
         * Если передана конкретная локаль, то возвращаем поля title и content на нужном языке
         * Иначе возвращаем title_ru, title_en, title_es и content_ru, content_es, content_es
         */
        if ($locale !== 'all') {
            $structure['title'] = $this[0]->__call('getTitle' . $locale, []);
            $structure['content'] = $this[0]->__call('getContent' . $locale, []);
        } else {
            $structure['title_ru'] = $this[0]->getTitleRu();
            $structure['title_en'] = $this[0]->getTitleEn();
            $structure['title_es'] = $this[0]->getTitleEs();
            $structure['content_ru'] = $this[0]->getContentRu();
            $structure['content_en'] = $this[0]->getContentEn();
            $structure['content_es'] = $this[0]->getContentEs();
        }

        return $structure;
    }
}
