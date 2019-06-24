<?php

namespace App\Entities\References;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 */
class Region
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\References\Country", inversedBy="regions")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id", onDelete="cascade", nullable=false)
     * @var Country
     */
    protected $country;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\References\Locality", mappedBy="region")
     * @var ArrayCollection|Locality[]
     */
    protected $localities;

    public function __construct()
    {
        $this->localities = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Country
     */
    public function getCountry(): Country
    {
        return $this->country;
    }

    /**
     * @param Country $country
     */
    public function setCountry(Country $country): void
    {
        $this->country = $country;
    }

    /**
     * @return Locality[]|ArrayCollection
     */
    public function getLocalities()
    {
        return $this->localities;
    }

    /**
     * @param Locality[]|ArrayCollection $localities
     */
    public function setLocalities($localities): void
    {
        $this->localities = $localities;
    }
}