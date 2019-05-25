<?php


namespace App\Entities\Traits;

/**
 * Trait ContentsTrait
 * Добавляет в сущность поля "content" на всех языках приложения
 * @package App\Entities\Traits
 */
trait ContentsTrait
{
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content_ru;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content_en;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content_es;

    /**
     * @return mixed
     */
    public function getContentRu()
    {
        return $this->content_ru;
    }

    /**
     * @param mixed $content_ru
     */
    public function setContentRu($content_ru): void
    {
        $this->content_ru = $content_ru;
    }

    /**
     * @return mixed
     */
    public function getContentEn()
    {
        return $this->content_en;
    }

    /**
     * @param mixed $content_en
     */
    public function setContentEn($content_en): void
    {
        $this->content_en = $content_en;
    }

    /**
     * @return mixed
     */
    public function getContentEs()
    {
        return $this->content_es;
    }

    /**
     * @param mixed $content_es
     */
    public function setContentEs($content_es): void
    {
        $this->content_es = $content_es;
    }
}