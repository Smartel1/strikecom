<?php


namespace App\Criteria;


use Doctrine\Common\Collections\Criteria;

/**
 * Фильтрует по полю, значение которого должно входить в интервал
 * Если не переданы границы, то интервал открытый
 * @package App\Criteria
 */
class SafeBetween
{

    /**
     * @param string $field название поля
     * @param $min
     * @param $max
     * @return Criteria
     */
    public static function make(string $field, $min, $max)
    {
        //возможны 4 случая:
        //не переданы обе границы
        if (is_null($min) and is_null($max)) return Criteria::create();

        //переданы обе границы
        if (!is_null($min) and !is_null($max)) {
            return Criteria::create()->where(
                Criteria::expr()->andX(
                    Criteria::expr()->gte($field, $min),
                    Criteria::expr()->lte($field, $max)
                )
            );
        }

        //передана только нижняя граница
        if (!is_null($min)) {
            return Criteria::create()->where(
                Criteria::expr()->gte($field, $min)
            );
        }

        //передана только верхняя граница
        if (!is_null($max)) {
            return Criteria::create()->where(
                Criteria::expr()->lte($field, $max)
            );
        }
    }
}