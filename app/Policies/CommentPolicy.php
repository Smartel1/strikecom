<?php

namespace App\Policies;

use App\EventComment;
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

    public function update (User $user, EventComment $comment)
    {
        return $comment->user_id === $user->id;
    }

    public function delete (User $user, EventComment $comment)
    {
        return $comment->user_id === $user->id;
    }

}
