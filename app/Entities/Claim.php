<?php


namespace App\Entities;

use App\Entities\References\ClaimType;
use App\Entities\Traits\Timestamps;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="claims")
 */
class Claim
{
    use Timestamps;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="cascade", nullable=false)
     * @var User
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Comment", inversedBy="claims")
     * @ORM\JoinColumn(onDelete="cascade", nullable=false)
     * @var Comment
     */
    protected $comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\References\ClaimType")
     * @ORM\JoinColumn(onDelete="cascade", nullable=false)
     * @var ClaimType
     */
    protected $claimType;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User|null
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Comment
     */
    public function getComment(): Comment
    {
        return $this->comment;
    }

    /**
     * @param Comment $comment
     */
    public function setComment(Comment $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return ClaimType
     */
    public function getClaimType(): ClaimType
    {
        return $this->claimType;
    }

    /**
     * @param ClaimType $claimType
     */
    public function setClaimType(ClaimType $claimType): void
    {
        $this->claimType = $claimType;
    }
}