<?php


namespace App\Criteria;


use Doctrine\Common\Collections\Criteria;

/**
 * Фильтр работает как eq, но если передан null, то не выбрасывает исключение
 * @package App\Criteria
 */
class SafeEq
{
    /**
     * @param string $field название поля
     * @param $value
     * @return Criteria
     */
    public static function make(string $field, $value)
    {
        if (is_null($value)) return Criteria::create();

        return Criteria::create()->where(
            Criteria::expr()->eq($field, $value)
        );
    }
}