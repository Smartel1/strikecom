<?php

namespace App\Policies;

use App\Event;
use App\News;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NewsPolicy
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

    public function update (User $user, News $news)
    {
        return $news->user_id === $user->id;
    }

    public function delete (User $user, News $news)
    {
        return $news->user_id === $user->id;
    }

}
