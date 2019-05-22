<?php


namespace App\Criteria;


use App\Entities\Conflict;
use Doctrine\Common\Collections\Criteria;
use LaravelDoctrine\ORM\Facades\EntityManager;

/**
 * Применяет фильтр по принадлежности к конфликту
 * @package App\Criteria
 */
class BelongsToConflicts
{
    /**
     * @param $conflictIds
     * @return Criteria
     */
    public static function make($conflictIds)
    {
        if (is_null($conflictIds)) return Criteria::create();

        //Получаем все события, которые являются родительскими для конфликтов из фильтра
        $conflictParentEventsIds = EntityManager::createQueryBuilder()
            ->select('identity(c.parentEvent)')
            ->from(Conflict::class,'c')
            ->where(EntityManager::getExpressionBuilder()->in('c.id',':conflictIds'))
            ->setParameter('conflictIds', $conflictIds)
            ->getQuery()
            ->getResult();

        //Преобразовываем результат в плоский массив
        $conflictParentEventsIds = array_flatten($conflictParentEventsIds);

        if (count($conflictParentEventsIds) === 0) {
            //Находим события, которые принадлежат конфликтам напрямую
            return Criteria::create()->where(
                Criteria::expr()->in('c.id', $conflictIds)
            );
        } else {
            //Находим события, которые принадлежат конфликтам напрямую, либо являются родительскими
            return Criteria::create()->where(
                Criteria::expr()->orX(
                    Criteria::expr()->in('c.id', $conflictIds),
                    Criteria::expr()->in('e.id', $conflictParentEventsIds)
                )
            );
        }
    }
}