<?php

namespace App\Rules;

abstract class BusinessRule
{

    public $when = true;

    /**
     * Determine if the validation rule passes.
     *
     * @return bool
     */
    abstract public function passes();

    /**
     * Get the validation error message.
     *
     * @return string
     */
    abstract public function message();

    /**
     * @param $condition
     * @return mixed
     */
    public function when($condition)
    {
        $this->when = $condition;
        return $this;
    }
}
