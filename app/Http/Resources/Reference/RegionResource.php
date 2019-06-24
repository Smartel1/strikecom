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
            'id'   => $region->getId(),
            'name' => $region->getName(),
        ];

        /**
         * В зависимости от переданной локали выбираем состав полей
         */
        $locale = app('locale');

        if ($locale !== 'all') {
            $structure['country'] = $region->getCountry()->getNameByLocale($locale);
        } else {
            $structure['country_ru'] = $region->getCountry()->getNameRu();
            $structure['country_en'] = $region->getCountry()->getNameEn();
            $structure['country_es'] = $region->getCountry()->getNameEs();
        }

        return $structure;
    }
}
