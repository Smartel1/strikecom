<?php


namespace App\Criteria;


use App\Entities\Conflict;
use Doctrine\Common\Collections\Criteria;

/**
 * Применяет фильтр по принадлежности к предкам конфликта
 * @package App\Criteria
 */
class AncestorsOfConflict
{
    /**
     * @param string $entityAlias псевдоним сущности в DQL
     * @param int $descendantId
     * @return Criteria
     */
    public static function make(string $entityAlias, ?int $descendantId)
    {
        if (is_null($descendantId)) return Criteria::create();

        $ancestorsIds = [];

        /** @var Conflict $childConflict */
        $childConflict = app('em')->find(Conflict::class, $descendantId);

        //Перебираем в цикле всех предков переданного конфликта (через привязку к событию).
        //Чтобы не попасть в замкнутый цикл, проверяем, что конфликт ещё не в массиве $ancestorsIds
        while ($childConflict->getParentEvent() and !in_array($childConflict->getId(), $ancestorsIds)) {
            $parentEvent = $childConflict->getParentEvent();
            $ancestorsIds []= $parentEvent->getConflict()->getId();
            $childConflict = $parentEvent->getConflict();
        }

        return Criteria::create()->where(
            Criteria::expr()->in($entityAlias, $ancestorsIds)
        );
    }
}