<?php


namespace App\Rules;


use App\Entities\Conflict;
use Doctrine\ORM\NonUniqueResultException;
use LaravelDoctrine\ORM\Facades\EntityManager;

class NotAParentConflict extends BusinessRule
{
    private $conflict;

    /**
     * NotAParentConflict constructor.
     * @param Conflict $conflict
     */
    public function __construct(Conflict $conflict)
    {
        $this->conflict = $conflict;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @return bool
     * @throws NonUniqueResultException
     */
    public function passes()
    {
        //Находим количество событий этого конфликта, которые являются parentEvent для других конфликтов
        $relatedConflictsCount = EntityManager::createQueryBuilder()
            ->from('App\Entities\Conflict', 'c')
            ->join('c.parentEvent', 'pe')
            ->andWhere('pe.conflict = :conflict')
            ->setParameter('conflict', $this->conflict)
            ->select('count(c)')
            ->getQuery()
            ->getSingleScalarResult();

        return $relatedConflictsCount === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Этот конфликт является родительским для других конфликтов';
    }
}