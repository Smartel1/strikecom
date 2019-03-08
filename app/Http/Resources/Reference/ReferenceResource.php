<?php

namespace App\Http\Resources\Reference;

use Illuminate\Http\Resources\Json\Resource;

class ReferenceResource extends Resource
{
    /**
     * Структура ответа на запрос справочников
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $structure = [
            'id'               => $this->id,
        ];

        $locale = app('locale');

        if ($locale !== 'all') {
            $structure['name'] = $this['name_'.$locale];
        } else {
            $structure['name_ru'] = $this['name_ru'];
            $structure['name_en'] = $this['name_en'];
            $structure['name_es'] = $this['name_es'];
        }

        return $structure;
    }
}
