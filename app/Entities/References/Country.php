<?php

namespace App\Entities\References;

use App\Entities\Interfaces\Reference;
use App\Entities\Traits\NamesTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 */
class Country implements Reference
{
    use NamesTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    public function __construct()
    {
        $this->regions = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\References\Region", mappedBy="country")
     * @var ArrayCollection|Region[]
     */
    protected $regions;

    /**
     * @return Region[]|ArrayCollection
     */
    public function getRegions()
    {
        return $this->regions;
    }

    /**
     * @param Region[]|ArrayCollection $regions
     */
    public function setRegions($regions): void
    {
        $this->regions = $regions;
    }
}