<?php

namespace App\Http\Resources\News;

use App\Entities\News;
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
        /** @var $news News*/
        $news = $this;

        $structure = [
            'id'          => $news->getId(),
            'date'        => $news->getDate(),
            'views'       => $news->getViews(),
            'source_link' => $news->getSourceLink(),
            'photos'      => $news->getPhotos()
                ->map(function (Photo $photo) {
                    return $photo->getUrl();
                })
                ->getValues(),
            'videos'      => $news->getVideos()
                ->map(function (Video $video) {
                    return [
                        'url'           => $video->getUrl(),
                        'preview_url'   => $video->getPreviewUrl(),
                        'video_type_id' => $video->getVideoType()->getId(),
                    ];
                })->getValues(),
            'tags'        => $news->getTags()
                ->map(function (Tag $tag) {
                    return $tag->getName();
                })->getValues(),
            'user'        => $news->getUser() ? [
                'id'    => $news->getUser()->getId(),
                'name'  => $news->getUser()->getName(),
                'email' => $news->getUser()->getEmail()
            ] : null,
        ];

        $locale = app('locale');
        /**
         * Если передана конкретная локаль, то возвращаем поля title и content на нужном языке
         * Иначе возвращаем title_ru, title_en, title_es и content_ru, content_es, content_es
         */
        if ($locale !== 'all') {
            $structure['title'] = $news->getTitleByLocale($locale);
            $structure['content'] = $news->getContentByLocale($locale);
        } else {
            $structure['title_ru'] = $news->getTitleRu();
            $structure['title_en'] = $news->getTitleEn();
            $structure['title_es'] = $news->getTitleEs();
            $structure['content_ru'] = $news->getContentRu();
            $structure['content_en'] = $news->getContentEn();
            $structure['content_es'] = $news->getContentEs();
        }

        return $structure;
    }
}
