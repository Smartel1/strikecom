<?php

namespace App\Policies;

use App\Entities\ClientVersion;
use App\Entities\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientVersionPolicy
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

    public function delete (User $user, ClientVersion $clientVersion)
    {
        return false;
    }

}
