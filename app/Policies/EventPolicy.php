<?php

namespace App\Policies;

use App\Entities\Event;
use App\Entities\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    public function before (User $user, $action)
    {
        if ($user->isAdmin()) return true;
    }

    public function create (User $user)
    {
        //Любой аутентифицированный пользователь
        return true;
    }

    public function update (User $user, Event $event)
    {
        return false;
    }

    public function delete (User $user, Event $event)
    {
        return false;
    }

}
