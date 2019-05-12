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
            'id'                    => $this->getId(),
            'latitude'              => $this->getLatitude(),
            'longitude'             => $this->getLongitude(),
            'company_name'          => $this->getCompanyName(),
            'date_from'             => $this->getDateFrom(),
            'date_to'               => $this->getDateTo(),
            'conflict_reason_id'    => $this->getConflictReasonId(),
            'conflict_result_id'    => $this->getConflictResultId(),
            'industry_id'           => $this->getIndustryId(),
            'region_id'             => $this->getRegionId(),
        ];

        $locale = app('locale');

        if ($locale !== 'all') {
            $structure['title'] = $this->__call('getTitle' . $locale, []);
        } else {
            $structure['title_ru'] = $this->getTitleRu();
            $structure['title_en'] = $this->getTitleEn();
            $structure['title_es'] = $this->getTitleEs();
        }

        return $structure;
    }
}
