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
        if ($user->isAdmin()) return true;
    }

    public function create (User $user)
    {
        //Любой аутентифицированный пользователь
        return true;
    }

    public function update (User $user, Comment $comment)
    {
        return $comment->getUserId() === $user->getId();
    }

    public function delete (User $user, Comment $comment)
    {
        return $comment->getUserId() === $user->getId();
    }

}
