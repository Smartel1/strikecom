<?php

namespace App\Http\Resources\Reference;

use App\Entities\References\Locality;
use Illuminate\Http\Resources\Json\Resource;

class LocalityResource extends Resource
{
    /**
     * Структура ответа на запрос населенных пунктов
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var Locality $locality */
        $locality = $this;

        $structure = [
            'id'     => $locality->getId(),
            'name'   => $locality->getName(),
            'region' => $locality->getRegion()->getName()
        ];

        /**
         * В зависимости от переданной локали выбираем состав полей
         */
        $locale = app('locale');

        if ($locale !== 'all') {
            $structure['country'] = $locality->getRegion()->getCountry()->getNameByLocale($locale);
        } else {
            $structure['country_ru'] = $locality->getRegion()->getCountry()->getNameRu();
            $structure['country_en'] = $locality->getRegion()->getCountry()->getNameEn();
            $structure['country_es'] = $locality->getRegion()->getCountry()->getNameEs();
        }

        return $structure;
    }
}
