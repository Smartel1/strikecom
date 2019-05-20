<?php

namespace App\Http\Resources\Conflict;

use App\Entities\Conflict;
use Illuminate\Http\Resources\Json\Resource;

class ConflictBriefIndexResource extends Resource
{
    /**
     * Структура ответа на краткий запрос списка конфликтов
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var $conflict Conflict */
        $conflict = $this;

        $structure = [
            'id' => $conflict->getId(),
            'latitude' => $conflict->getLatitude(),
            'longitude' => $conflict->getLongitude(),
        ];

        $locale = app('locale');

        if ($locale !== 'all') {
            $structure['title'] = $conflict->getTitleByLocale($locale);
        } else {
            $structure['title_ru'] = $conflict->getTitleRu();
            $structure['title_en'] = $conflict->getTitleEn();
            $structure['title_es'] = $conflict->getTitleEs();
        }

        return $structure;
    }
}
