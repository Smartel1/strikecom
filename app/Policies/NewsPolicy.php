<?php

namespace App\Policies;

use App\Entities\News;
use App\Entities\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NewsPolicy
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

    public function update (User $user, News $news)
    {
        return false;
    }

    public function delete (User $user, News $news)
    {
        return false;
    }

}
