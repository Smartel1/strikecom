<?php


namespace App\Criteria;


use Doctrine\Common\Collections\Criteria;

/**
 * Применяет фильтр по наличию тега
 * @package App\Criteria
 */
class HasTag
{
    /**
     * @param string $entityAlias псевдоним сущности в DQL
     * @param int|null $tagId ид. тега
     * @return Criteria
     */
    public static function make(string $entityAlias, ?int $tagId)
    {
        if (is_null($tagId)) return Criteria::create();

        return Criteria::create()->where(
            Criteria::expr()->eq($entityAlias . '.id', $tagId)
        );
    }
}