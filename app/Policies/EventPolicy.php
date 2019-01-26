<?php

namespace App\Policies;

use App\Event;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    public function before (User $user, $action)
    {
        if ($user->admin) return true;
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
