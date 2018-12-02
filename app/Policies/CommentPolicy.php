<?php

namespace App\Policies;

use App\Comment;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
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

    public function update (User $user, Comment $comment)
    {
        return $comment->user_id === $user->id;
    }

    public function delete (User $user, Comment $comment)
    {
        return $comment->user_id === $user->id;
    }

}
