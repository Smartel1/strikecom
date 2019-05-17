<?php

namespace App\Http\Resources\Conflict;

use App\Entities\Conflict;
use Illuminate\Http\Resources\Json\Resource;

class ConflictIndexResource extends Resource
{
    /**
     * Структура ответа на запрос списка конфликтов
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var $conflict Conflict */
        $conflict = $this;

        $structure = [
            'id'                    => $conflict->getId(),
            'latitude'              => $conflict->getLatitude(),
            'longitude'             => $conflict->getLongitude(),
            'company_name'          => $conflict->getCompanyName(),
            'date_from'             => $conflict->getDateFrom(),
            'date_to'               => $conflict->getDateTo(),
            'conflict_reason_id'    => $conflict->getConflictReason() ? $conflict->getConflictReason()->getId() : null,
            'conflict_result_id'    => $conflict->getConflictResult() ? $conflict->getConflictResult()->getId() : null,
            'industry_id'           => $conflict->getIndustry() ? $conflict->getIndustry()->getId() : null,
            'region_id'             => $conflict->getRegion() ? $conflict->getRegion()->getId() : null,
        ];

        $locale = app('locale');

        if ($locale !== 'all') {
            $structure['title'] = $conflict->getTitleByLocale($locale);
        } else {
            $structure['title_ru'] = $conflict->getTitleRu();
            $structure['title_en'] = $conflict->getTitleEn();
            $structure['title_es'] = $conflict->getTitleEs();
        }

        return $structure;
    }
}
