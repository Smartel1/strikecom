<?php


namespace App\Criteria;


use Doctrine\Common\Collections\Criteria;

/**
 * Применяет фильтр по дате конца не позже определенной даты
 * @package App\Criteria
 */
class DateToBefore
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
            Criteria::expr()->lte($entityAlias . '.date_to', $date)
        );
    }
}