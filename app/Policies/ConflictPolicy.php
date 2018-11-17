<?php

namespace App\Policies;

use App\Conflict;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConflictPolicy
{
    use HandlesAuthorization;

    public function before (User $user, $action)
    {

    }

    public function create (User $user)
    {
        //Любой аутентифицированный пользователь
        return true;
    }

    public function update (User $user, Conflict $conflict)
    {
        return $conflict->user_id === $user->id;
    }

    public function delete (User $user, Conflict $conflict)
    {
        return $conflict->user_id === $user->id;
    }

}
