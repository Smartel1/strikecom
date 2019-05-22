<?php


namespace App\Rules;


use App\Entities\Event;
use Doctrine\ORM\NonUniqueResultException;
use LaravelDoctrine\ORM\Facades\EntityManager;

class NotAParentEvent extends BusinessRule
{
    private $event;

    /**
     * NotAParentEvent constructor.
     * @param Event $event
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @return bool
     * @throws NonUniqueResultException
     */
    public function passes()
    {
        $relatedConflictsCount = EntityManager::createQueryBuilder()
            ->from('App\Entities\Conflict', 'c')
            ->where('c.parentEvent = :event')
            ->setParameter('event', $this->event)
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
        return 'Это событие является родительским для конфликта';
    }
}