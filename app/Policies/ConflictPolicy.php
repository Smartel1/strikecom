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
        if ($user->admin) return true;
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
