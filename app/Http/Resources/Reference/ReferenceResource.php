<?php

namespace App\Http\Resources\Reference;

use App\Entities\Interfaces\Reference;
use Illuminate\Http\Resources\Json\Resource;

class ReferenceResource extends Resource
{
    /**
     * Структура ответа на запрос справочников
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var Reference $ref */
        $ref = $this;
        $structure = [
            'id' => $ref->getId(),
        ];

        /**
         * В зависимости от переданной локали выбираем состав полей
         */
        $locale = app('locale');

        if ($locale !== 'all') {
            $structure['name'] = $ref->getNameByLocale($locale);
        } else {
            $structure['name_ru'] = $ref->getNameRu();
            $structure['name_en'] = $ref->getNameEn();
            $structure['name_es'] = $ref->getNameEs();
        }

        return $structure;
    }
}
