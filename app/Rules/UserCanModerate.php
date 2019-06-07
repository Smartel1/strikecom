<?php


namespace App\Rules;


use App\Entities\User;

class UserCanModerate extends BusinessRule
{
    private $user;

    /**
     * UniqueVersion constructor.
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
        return $this->user->isAdmin();
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