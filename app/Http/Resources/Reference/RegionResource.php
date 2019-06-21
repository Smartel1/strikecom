<?php

namespace App\Http\Resources\Reference;

use App\Entities\References\Region;
use Illuminate\Http\Resources\Json\Resource;

class RegionResource extends Resource
{
    /**
     * Структура ответа на запрос регионов
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var Region $region */
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
            $structure['country'] = $region->getCountry()->getNameByLocale($locale);
        } else {
            $structure['name_ru'] = $region->getNameRu();
            $structure['name_en'] = $region->getNameEn();
            $structure['name_es'] = $region->getNameEs();
            $structure['country_ru'] = $region->getCountry()->getNameRu();
            $structure['country_en'] = $region->getCountry()->getNameEn();
            $structure['country_es'] = $region->getCountry()->getNameEs();
        }

        return $structure;
    }
}
