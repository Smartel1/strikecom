<?php

namespace App\Http\Resources\Reference;

use App\Entities\References\Locality;
use Illuminate\Http\Resources\Json\Resource;

class LocalityResource extends Resource
{
    /**
     * Структура ответа на запрос регионов
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var Locality $region */
        $region = $this;

        $structure = [
            'id' => $region->getId(),
        ];

        /**
         * В зависимости от переданной локали выбираем состав полей
         */
        $locale = app('locale');

        if ($locale !== 'all') {
            $structure['name'] = $region->getNameByLocale($locale);
            $structure['region'] = $region->getRegion()->getNameByLocale($locale);
            $structure['country'] = $region->getRegion()->getCountry()->getNameByLocale($locale);
        } else {
            $structure['name_ru'] = $region->getNameRu();
            $structure['name_en'] = $region->getNameEn();
            $structure['name_es'] = $region->getNameEs();
            $structure['region_ru'] = $region->getRegion()->getNameRu();
            $structure['region_en'] = $region->getRegion()->getNameEn();
            $structure['region_es'] = $region->getRegion()->getNameEs();
            $structure['country_ru'] = $region->getRegion()->getCountry()->getNameRu();
            $structure['country_en'] = $region->getRegion()->getCountry()->getNameEn();
            $structure['country_es'] = $region->getRegion()->getCountry()->getNameEs();
        }

        return $structure;
    }
}
