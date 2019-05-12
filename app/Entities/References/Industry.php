<?php


namespace App\Entities\References;

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="industries")
 */
class Industry
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name_ru;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name_en;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name_es;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

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
}