<?php

namespace App\Policies;

use App\Models\ClientVersion;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientVersionPolicy
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

    public function delete (User $user, ClientVersion $clientVersion)
    {
        return false;
    }

}
