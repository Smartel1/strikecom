<?php


namespace App\Criteria;


use App\Entities\Conflict;
use Doctrine\Common\Collections\Criteria;

/**
 * Применяет фильтр по принадлежности к прямым потомкам конфликта
 * (то есть в выборку попадут те конфликты, чьи parent_event_id указывают на одно из событий конфликта $parentId)
 * @package App\Criteria
 */
class ChildrenOfConflict
{
    /**
     * @param string $entityAlias псевдоним сущности в DQL
     * @param int $parentId
     * @return Criteria
     */
    public static function make(string $entityAlias, ?int $parentId)
    {
        if (is_null($parentId)) return Criteria::create();

        //массив id дочерних конфликтов
        $childrenConflictsIds = array_flatten(
            app('em')->createQueryBuilder()
                ->select('c.id')
                ->from(Conflict::class, 'c')
                ->leftJoin('c.parentEvent', 'pe')
                ->where('pe.conflict = :parentConflict')
                ->setParameter('parentConflict', $parentId)
                ->getQuery()
                ->getResult()
        );

        //Важно! Здесь используется <$entityAlias . '.id'>
        //а в критерии AncestorsOfConflict используется <$entityAlias>
        //Оба выражения делают одно и то же, но от способа записи зависит то, как доктрина назовёт параметр.
        //Если назвать одинаково, то доктрина создаст два одноименных параметра, что приведёт к исключению.
        //todo Это необходимо пересмотреть (возможно, объединить эти две критерия в один; или отказаться от критериев)
        return Criteria::create()->where(
            Criteria::expr()->in($entityAlias . '.id', $childrenConflictsIds)
        );
    }
}