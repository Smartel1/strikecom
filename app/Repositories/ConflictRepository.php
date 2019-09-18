<?php


namespace App\Repositories;


use App\Entities\Conflict;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class ConflictRepository extends NestedTreeRepository
{
    /**
     * Получить конфликт, который является коренным в ветке конфликта $conflict
     * @param Conflict $conflict
     * @return Conflict
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getParentOfConflictBranch(Conflict $conflict) {
        return $this->getPathQueryBuilder($conflict)
            ->orderBy('node.lft')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }
}