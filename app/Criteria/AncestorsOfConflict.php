<?php


namespace App\Criteria;


use App\Entities\Conflict;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;

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
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public static function make(string $entityAlias, ?int $descendantId)
    {
        if (is_null($descendantId)) return Criteria::create();

        /** @var EntityManager $em */
        $em = app('em');

        /** @var Conflict $childConflict */
        $childConflict = $em->find(Conflict::class, $descendantId);

        $ancestorsIds = $em->createQueryBuilder()
            ->select('c.id')
            ->from(Conflict::class, 'c')
            ->where($em->getExpressionBuilder()->lt('c.lft', $childConflict->getLft()))
            ->andWhere($em->getExpressionBuilder()->gt('c.rgt', $childConflict->getRgt()))
            ->getQuery()
            ->getResult();

        $ancestorsIds = collect($ancestorsIds)->pluck('id')->toArray();

        return Criteria::create()->where(
            Criteria::expr()->in($entityAlias, $ancestorsIds)
        );
    }
}