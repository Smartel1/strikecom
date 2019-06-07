<?php

namespace App\Entities\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Trait Timestamps
 * Добавляет в сущность поля createdAt и updatedAt,
 * привязывает хуки к этим полям для автоматического обновления
 * @package App\Entities\Traits
 */
trait Timestamps
{
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     * @var DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     * @var DateTime
     */
    protected $updatedAt;

    /**
     * @return int|null
     */
    public function getCreatedAt()
    {
        return $this->createdAt ? $this->createdAt->getTimestamp() : null;
    }

    /**
     * @return int|null
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt ? $this->updatedAt->getTimestamp() : null;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt(DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}
