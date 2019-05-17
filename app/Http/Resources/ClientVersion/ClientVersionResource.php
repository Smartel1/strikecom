<?php

namespace App\Http\Resources\ClientVersion;

use App\Entities\ClientVersion;
use Illuminate\Http\Resources\Json\Resource;

class ClientVersionResource extends Resource
{
    /**
     * Структура ответа на запрос версии клиентского приложения
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var $version ClientVersion */
        $version = $this;

        $structure = [
            'id'       => $version->getId(),
            'version'  => $version->getVersion(),
            'required' => $version->isRequired(),
        ];

        $locale = app('locale');

        if ($locale !== 'all') {
            $structure['description'] = $version->getDescriptionByLocale($locale);
        } else {
            $structure['description_ru'] = $version->getDescriptionRu();
            $structure['description_en'] = $version->getDescriptionEn();
            $structure['description_es'] = $version->getDescriptionEs();
        }

        return $structure;
    }
}
