<?php


namespace App\Criteria;


use Doctrine\Common\Collections\Criteria;

/**
 * Применяет фильтр по дате начала не ранее определенной даты
 * @package App\Criteria
 */
class DateFromAfter
{
    /**
     * @param string $entityAlias псевдоним сущности в DQL
     * @param int $date дата
     * @return Criteria
     */
    public static function make(string $entityAlias, int $date)
    {
        if (is_null($date)) return Criteria::create();

        return Criteria::create()->where(
            Criteria::expr()->gte($entityAlias . '.date_from', $date)
        );
    }
}