<?php

namespace App\Http\Resources\ClientVersion;

use Illuminate\Http\Resources\Json\Resource;

class ClientVersionResource extends Resource
{
    /**
     * Структура ответа на запрос версии клиентского приложения
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $structure = [
            'id' => $this->id,
            'version' => $this->version,
            'required' => $this->required,
        ];

        $locale = app('locale');

        if ($locale !== 'all') {
            $structure['description'] = $this['description_'.$locale];
        } else {
            $structure['description_ru'] = $this['description_ru'];
            $structure['description_en'] = $this['description_en'];
            $structure['description_es'] = $this['description_es'];
        }

        return $structure;
    }
}
