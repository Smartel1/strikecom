<?php


namespace App\Criteria;


use Doctrine\Common\Collections\Criteria;

/**
 * Применяет фильтр по наличию локализации содержания
 * @package App\Criteria
 */
class HasLocalizedContent
{
    /**
     * @param $entityAlias string псевдоним сущности в DQL
     * @param $locale string локаль
     * @return Criteria
     */
    public static function make(string $entityAlias, string $locale)
    {
        if ($locale === 'all') return Criteria::create();

        return Criteria::create()->where(
            Criteria::expr()->neq($entityAlias . '.content_' . $locale, null)
        );
    }
}