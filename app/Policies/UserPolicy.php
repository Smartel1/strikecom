<?php

namespace App\Policies;

use App\Entities\Event;
use App\Entities\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function before (User $user, $action)
    {
        if (in_array(User::$ROLE_MODERATOR, $user->getRoles())
            or in_array(User::$ROLE_ADMIN, $user->getRoles())) return true;
    }

    /**
     * Пользователь может менять только свой объект
     * @param User $userToUpdate
     * @param User $userWhoUpdates
     * @return bool
     */
    public function update (User $userWhoUpdates, User $userToUpdate, $changesRoles)
    {
        //обычным пользователям запрещено изменять роли
        if ($changesRoles) return false;

        return $userToUpdate->getId() === $userWhoUpdates->getId();
    }
}
