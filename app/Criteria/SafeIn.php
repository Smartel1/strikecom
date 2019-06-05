<?php


namespace App\Criteria;


use Doctrine\Common\Collections\Criteria;

/**
 * Фильтр работает как in, но если передан null, то не выбрасывает исключение
 * @package App\Criteria
 */
class SafeIn
{
    /**
     * @param string $field название поля
     * @param $values
     * @return Criteria
     */
    public static function make(string $field, $values)
    {
        if (is_null($values)) return Criteria::create();

        return Criteria::create()->where(
            Criteria::expr()->in($field, $values)
        );
    }
}