<?php


namespace App\Criteria;


use App\Entities\User;
use Doctrine\Common\Collections\Criteria;

/**
 * Фильтр работает для исключения тех записей, которые не должен видеть пользователь
 * @package App\Criteria
 */
class ByPermission
{
    /**
     * @param User|null $user
     * @return Criteria
     */
    public static function make(?User $user)
    {
        if (is_null($user)) return Criteria::create();

        if ($user->isAdmin()) return Criteria::create();

        return Criteria::create()->where(
            Criteria::expr()->eq('published', true)
        );
    }
}