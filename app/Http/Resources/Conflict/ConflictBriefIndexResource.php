<?php

namespace App\Http\Resources\Conflict;

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
        $structure = [
            'id' => $this->id,
        ];

        $locale = app('locale');

        if ($locale !== 'all') {
            $structure['title'] = $this['title_'.$locale];
        } else {
            $structure['title_ru'] = $this['title_ru'];
            $structure['title_en'] = $this['title_en'];
            $structure['title_es'] = $this['title_es'];
        }

        return $structure;
    }
}
