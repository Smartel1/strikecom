<?php

namespace App\Http\Resources\News;

use App\Entities\Photo;
use App\Entities\Tag;
use App\Entities\Video;
use Illuminate\Http\Resources\Json\Resource;

class NewsDetailResource extends Resource
{
    /**
     * Структура ответа на запрос деталки новости
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $structure = [
            'id'          => $this->getId(),
            'date'        => $this->getDate(),
            'views'       => $this->getViews(),
            'source_link' => $this->getSourceLink(),
            'photos'      => $this->getPhotos()
                ->map(function (Photo $photo) {
                    return $photo->getUrl();
                })
                ->getValues(),
            'videos'      => $this->getVideos()
                ->map(function (Video $video) {
                    return [
                        'url'           => $video->getUrl(),
                        'preview_url'   => $video->getPreviewUrl(),
                        'video_type_id' => $video->getVideoType()->getId(),
                    ];
                })->getValues(),
            'tags'        => $this->getTags()
                ->map(function (Tag $tag) {
                    return $tag->getName();
                })->getValues(),
            'user'        => $this->getUser() ? [
                'id'    => $this->getUser()->getId(),
                'name'  => $this->getUser()->getName(),
                'email' => $this->getUser()->getEmail()
            ] : null,
        ];

        $locale = app('locale');
        /**
         * Если передана конкретная локаль, то возвращаем поля title и content на нужном языке
         * Иначе возвращаем title_ru, title_en, title_es и content_ru, content_es, content_es
         */
        if ($locale !== 'all') {
            $structure['title'] = $this->__call('getTitle' . $locale, []);
            $structure['content'] = $this->__call('getContent' . $locale, []);
        } else {
            $structure['title_ru'] = $this->getTitleRu();
            $structure['title_en'] = $this->getTitleEn();
            $structure['title_es'] = $this->getTitleEs();
            $structure['content_ru'] = $this->getContentRu();
            $structure['content_en'] = $this->getContentEn();
            $structure['content_es'] = $this->getContentEs();
        }

        return $structure;
    }
}
