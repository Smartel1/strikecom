<?php

namespace App\Http\Resources\Reference;

use App\Entities\References\Locality;
use Illuminate\Http\Resources\Json\Resource;

class LocalityResourceExtended extends Resource
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
            'region' => [
                'id'      => $locality->getRegion()->getId(),
                'name'    => $locality->getRegion()->getName(),
                'country' => [
                    'id' => $locality->getRegion()->getCountry()->getId()
                ]
            ]
        ];

        /**
         * В зависимости от переданной локали выбираем название страны
         */
        $locale = app('locale');

        if ($locale !== 'all') {
            $structure['region']['country']['name'] = $locality->getRegion()->getCountry()->getNameByLocale($locale);
        } else {
            $structure['region']['country']['name_ru'] = $locality->getRegion()->getCountry()->getNameRu();
            $structure['region']['country']['name_en'] = $locality->getRegion()->getCountry()->getNameEn();
            $structure['region']['country']['name_es'] = $locality->getRegion()->getCountry()->getNameEs();
        }

        return $structure;
    }
}
