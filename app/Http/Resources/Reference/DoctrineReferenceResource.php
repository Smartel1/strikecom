<?php

namespace App\Http\Resources\Reference;

use Illuminate\Http\Resources\Json\Resource;

class DoctrineReferenceResource extends Resource
{
    /**
     * Структура ответа на запрос справочников
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $structure = [
            'id' => $this->getId(),
        ];

        /**
         * В зависимости от переданной локали выбираем состав полей
         */
        $locale = app('locale');

        if ($locale !== 'all') {
            $structure['name'] = $this->__call('getName' . $locale, []);
        } else {
            $structure['name_ru'] = $this->getNameRu();
            $structure['name_en'] = $this->getNameEn();
            $structure['name_es'] = $this->getNameEs();
        }

        return $structure;
    }
}
