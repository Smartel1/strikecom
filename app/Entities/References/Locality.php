<?php

namespace App\Entities\References;

use App\Entities\Traits\NamesTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 */
class Locality
{
    use NamesTrait;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\References\Region", inversedBy="localities")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id", onDelete="cascade", nullable=false)
     * @var Region
     */
    protected $region;

    /**
     * @return Region
     */
    public function getRegion(): Region
    {
        return $this->region;
    }

    /**
     * @param Region $region
     */
    public function setRegion(Region $region): void
    {
        $this->region = $region;
    }
}