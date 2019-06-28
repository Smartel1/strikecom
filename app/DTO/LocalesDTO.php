<?php


namespace App\DTO;


class LocalesDTO
{
    /** @var bool */
    private $ru;
    /** @var bool */
    private $en;
    /** @var bool */
    private $es;

    public function __construct(bool $ru, bool $en, bool $es)
    {
        $this->ru = $ru;
        $this->en = $en;
        $this->es = $es;
    }

    /**
     * @return bool
     */
    public function isRu(): bool
    {
        return $this->ru;
    }

    /**
     * @param bool $ru
     */
    public function setRu(bool $ru): void
    {
        $this->ru = $ru;
    }

    /**
     * @return bool
     */
    public function isEn(): bool
    {
        return $this->en;
    }

    /**
     * @param bool $en
     */
    public function setEn(bool $en): void
    {
        $this->en = $en;
    }

    /**
     * @return bool
     */
    public function isEs(): bool
    {
        return $this->es;
    }

    /**
     * @param bool $es
     */
    public function setEs(bool $es): void
    {
        $this->es = $es;
    }

}