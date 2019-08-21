<?php


namespace App\Entities\References;

use App\Entities\Interfaces\Reference;
use App\Entities\Traits\NamesTrait;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="industries")
 */
class Industry implements Reference
{
    use NamesTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
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
}