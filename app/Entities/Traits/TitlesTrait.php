<?php


namespace App\Entities\Traits;

/**
 * Trait TitlesTrait
 * Добавляет в сущность поля "title" на всех языках приложения
 * @package App\Entities\Traits
 */
trait TitlesTrait
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $title_ru;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $title_en;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $title_es;

    /**
     * @return mixed
     */
    public function getTitleRu()
    {
        return $this->title_ru;
    }

    /**
     * @param mixed $title_ru
     */
    public function setTitleRu($title_ru): void
    {
        $this->title_ru = $title_ru;
    }

    /**
     * @return mixed
     */
    public function getTitleEn()
    {
        return $this->title_en;
    }

    /**
     * @param mixed $title_en
     */
    public function setTitleEn($title_en): void
    {
        $this->title_en = $title_en;
    }

    /**
     * @return mixed
     */
    public function getTitleEs()
    {
        return $this->title_es;
    }

    /**
     * @param mixed $title_es
     */
    public function setTitleEs($title_es): void
    {
        $this->title_es = $title_es;
    }
}