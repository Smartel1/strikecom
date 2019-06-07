<?php


namespace App\Entities;

use App\Entities\Traits\Timestamps;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="comments")
 */
class Comment
{
    use Timestamps;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $content;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="cascade")
     * @var User|null
     */
    protected $user;

    /**
     * @ORM\ManyToMany(targetEntity="Photo")
     * @ORM\JoinTable(name="comment_photo",
     *      joinColumns={@ORM\JoinColumn(name="comment_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="photo_id", referencedColumnName="id", unique=true, onDelete="cascade")}
     *      )
     * @var Photo[]|ArrayCollection
     */
    protected $photos;

    /**
     * @ORM\ManyToMany(targetEntity="Event", mappedBy="comments", cascade={"remove"})
     * @var Event[]|ArrayCollection
     */
    protected $events;

    /**
     * @ORM\ManyToMany(targetEntity="News", mappedBy="comments", cascade={"remove"})
     * @var News[]|ArrayCollection
     */
    protected $news;

    /**
     * @ORM\OneToMany(targetEntity="Claim", mappedBy="comment")
     * @var Claim[]|ArrayCollection
     */
    protected $claims;

    /**
     * Comment constructor.
     */
    public function __construct()
    {
        $this->photos = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->news = new ArrayCollection();
        $this->claims = new ArrayCollection();
    }

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
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

    /**
     * @return Photo[]|ArrayCollection
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * @param Photo[]|ArrayCollection $photos
     */
    public function setPhotos($photos): void
    {
        $this->photos = $photos;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
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
     * @return Event[]|ArrayCollection
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param Event[]|ArrayCollection $events
     */
    public function setEvents($events): void
    {
        $this->events = $events;
    }

    /**
     * @return News[]|ArrayCollection
     */
    public function getNews()
    {
        return $this->news;
    }

    /**
     * @param News[]|ArrayCollection $news
     */
    public function setNews($news): void
    {
        $this->news = $news;
    }

    /**
     * @return Claim[]|ArrayCollection
     */
    public function getClaims()
    {
        return $this->claims;
    }

    /**
     * @param Claim[]|ArrayCollection $claims
     */
    public function setClaims($claims): void
    {
        $this->claims = $claims;
    }
}