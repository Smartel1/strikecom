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
        if (in_array(User::$ROLE_MODERATOR, $user->getRoles())
            or in_array(User::$ROLE_ADMIN, $user->getRoles())) return true;
    }

    public function create (User $user)
    {
        //Любой аутентифицированный пользователь
        return true;
    }

    //Пользователи могут менять новости, которые создали (но не могут менять статус публикации)
    public function update (User $user, News $news)
    {
        return $news->getAuthor() and $news->getAuthor()->getId() === $user->getId();
    }

    public function setFavourite (User $user)
    {
        return true;
    }

    public function delete (User $user, News $news)
    {
        return false;
    }

}
