<?php


namespace App\Entities\Traits;

/**
 * Trait NamesTrait
 * Добавляет в сущность поля "name" на всех языках приложения
 * @package App\Entities\Traits
 */
trait NamesTrait
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name_ru;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name_en;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name_es;

    /**
     * @return mixed
     */
    public function getNameRu()
    {
        return $this->name_ru;
    }

    /**
     * @param mixed $name_ru
     */
    public function setNameRu($name_ru): void
    {
        $this->name_ru = $name_ru;
    }

    /**
     * @return mixed
     */
    public function getNameEn()
    {
        return $this->name_en;
    }

    /**
     * @param mixed $name_en
     */
    public function setNameEn($name_en): void
    {
        $this->name_en = $name_en;
    }

    /**
     * @return mixed
     */
    public function getNameEs()
    {
        return $this->name_es;
    }

    /**
     * @param mixed $name_es
     */
    public function setNameEs($name_es): void
    {
        $this->name_es = $name_es;
    }

    /**
     * Получить локализованное имя
     * @param string $locale
     * @return string
     */
    public function getNameByLocale(string $locale) : ?string
    {
        $getterName = 'getName' . $locale;

        return $this->$getterName();
    }
}