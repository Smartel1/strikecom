<?php


namespace App\Rules;


use App\Entities\User;

class UserCanModerate extends BusinessRule
{
    private $user;

    /**
     * UserCanModerate constructor.
     * @param $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @return bool
     */
    public function passes()
    {
        return in_array('MODERATOR', $this->user->getRoles())
            or in_array('ADMIN', $this->user->getRoles());
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Пользователь должен обладать правами модератора';
    }
}