<?php

namespace App\Http\Resources\Conflict;

use Illuminate\Http\Resources\Json\Resource;

class ConflictDetailResource extends Resource
{
    /**
     * Структура ответа на запрос деталки конфликта
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $structure = [
            'id'                    => $this->id,
            'latitude'              => $this->latitude,
            'longitude'             => $this->longitude,
            'company_name'          => $this->company_name,
            'date_from'             => $this->date_from,
            'date_to'               => $this->date_to,
            'conflict_reason_id'    => $this->conflict_reason_id,
            'conflict_result_id'    => $this->conflict_result_id,
            'industry_id'           => $this->industry_id,
            'region_id'             => $this->region_id,
        ];

        $locale = app('locale');

        if ($locale !== 'all') {
            $structure['title'] = $this['title_'.$locale];
        } else {
            $structure['title_ru'] = $this['title_ru'];
            $structure['title_en'] = $this['title_en'];
            $structure['title_es'] = $this['title_es'];
        }

        return $structure;
    }
}
