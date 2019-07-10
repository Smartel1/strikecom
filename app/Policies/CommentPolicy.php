<?php

namespace App\Policies;

use App\Entities\Comment;
use App\Entities\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
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

    public function update (User $user, Comment $comment)
    {
        if (!$comment->getUser()) return false;

        return $comment->getUser()->getId() === $user->getId();
    }

    public function delete (User $user, Comment $comment)
    {
        if (!$comment->getUser()) return false;

        return $comment->getUser()->getId() === $user->getId();
    }

}
