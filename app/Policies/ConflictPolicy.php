<?php

namespace App\Policies;

use App\Entities\Conflict;
use App\Entities\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConflictPolicy
{
    use HandlesAuthorization;

    public function before (User $user, $action)
    {
        if ($user->isAdmin()) return true;
    }

    public function create (User $user)
    {
        return false;
    }

    public function update (User $user, Conflict $conflict)
    {
        return false;
    }

    public function delete (User $user, Conflict $conflict)
    {
        return false;
    }

}
