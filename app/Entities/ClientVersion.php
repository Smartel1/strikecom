<?php


namespace App\Entities;

use App\Entities\Traits\Timestamps;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="client_versions")
 */
class ClientVersion
{
    use Timestamps;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $version;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $client_id;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $required;

    /**
     * @ORM\Column(type="string", length=500)
     */
    protected $description_ru;

    /**
     * @ORM\Column(type="string", length=500)
     */
    protected $description_en;

    /**
     * @ORM\Column(type="string", length=500)
     */
    protected $description_es;

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
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version): void
    {
        $this->version = $version;
    }

    /**
     * @return mixed
     */
    public function getClientId()
    {
        return $this->client_id;
    }

    /**
     * @param mixed $client_id
     */
    public function setClientId($client_id): void
    {
        $this->client_id = $client_id;
    }

    /**
     * @return mixed
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param mixed $required
     */
    public function setRequired($required): void
    {
        $this->required = $required;
    }

    /**
     * @return mixed
     */
    public function getDescriptionRu()
    {
        return $this->description_ru;
    }

    /**
     * @param mixed $description_ru
     */
    public function setDescriptionRu($description_ru): void
    {
        $this->description_ru = $description_ru;
    }

    /**
     * @return mixed
     */
    public function getDescriptionEn()
    {
        return $this->description_en;
    }

    /**
     * @param mixed $description_en
     */
    public function setDescriptionEn($description_en): void
    {
        $this->description_en = $description_en;
    }

    /**
     * @return mixed
     */
    public function getDescriptionEs()
    {
        return $this->description_es;
    }

    /**
     * @param mixed $description_es
     */
    public function setDescriptionEs($description_es): void
    {
        $this->description_es = $description_es;
    }

    /**
     * Вернуть локализованное описание
     * @param string $locale
     * @return string|null
     */
    public function getDescriptionByLocale(string $locale) : ?string
    {
        $getterName = 'getDescription' . $locale;

        return $this->$getterName();
    }

}