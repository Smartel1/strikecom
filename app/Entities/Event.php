<?php


namespace App\Entities;

use App\Entities\Interfaces\Commentable;
use App\Entities\Interfaces\Post;
use App\Entities\References\Locality;
use App\Entities\References\EventStatus;
use App\Entities\References\EventType;
use App\Entities\Traits\ContentsTrait;
use App\Entities\Traits\CoordinatesTrait;
use App\Entities\Traits\Timestamps;
use App\Entities\Traits\TitlesTrait;
use DateTime;
use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="events")
 */
class Event implements Commentable, Post
{
    use TitlesTrait;
    use ContentsTrait;
    use Timestamps;
    use CoordinatesTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    protected $date;

    /**
     * @ORM\Column(type="integer")
     */
    protected $views = 0;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    protected $source_link;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $published = false;

    /**
     * @ORM\ManyToMany(targetEntity="Photo", cascade={"remove"})
     * @ORM\JoinTable(name="event_photo",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="photo_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     * @var ArrayCollection|Photo[]
     */
    protected $photos;

    /**
     * @ORM\ManyToMany(targetEntity="Video", cascade={"remove"})
     * @ORM\JoinTable(name="event_video",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="video_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     * @var ArrayCollection|Video[]
     */
    protected $videos;

    /**
     * @ORM\ManyToMany(targetEntity="Tag")
     * @ORM\JoinTable(name="event_tag",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     * @var ArrayCollection|Tag[]
     */
    protected $tags;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="set null")
     * @var User|null
     */
    protected $author;

    /**
     * Пользователи, которые отметили событие в избранное
     * @ORM\ManyToMany(targetEntity="User", mappedBy="favouriteEvents")
     * @var ArrayCollection|User[]
     */
    protected $likedUsers;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Conflict", inversedBy="events")
     * @ORM\JoinColumn(name="conflict_id", referencedColumnName="id", onDelete="cascade")
     * @var Conflict|null
     */
    protected $conflict;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\References\EventStatus")
     * @var EventStatus|null
     */
    protected $eventStatus;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\References\EventType")
     * @var EventType|null
     */
    protected $eventType;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\References\Locality")
     * @ORM\JoinColumn(name="locality_id", referencedColumnName="id", onDelete="set null")
     * @var Locality|null
     */
    protected $locality;

    /**
     * @ORM\ManyToMany(targetEntity="Comment", inversedBy="events", cascade={"remove"})
     * @var ArrayCollection|Comment[]
     */
    protected $comments;

    /**
     * Event constructor.
     */
    public function __construct()
    {
        $this->videos = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->likedUsers = new ArrayCollection();
    }

    /**
     * @return Video[]|ArrayCollection
     */
    public function getVideos()
    {
        return $this->videos;
    }

    /**
     * @param Video[]|ArrayCollection $videos
     */
    public function setVideos($videos)
    {
        $this->videos = $videos;
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
    public function setPhotos($photos)
    {
        $this->photos = $photos;
    }

    /**
     * @return Tag[]|ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param Tag[]|ArrayCollection $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return User|null
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User|null $author
     */
    public function setAuthor(?User $author)
    {
        $this->author = $author;
    }

    /**
     * @return User[]|ArrayCollection
     */
    public function getLikedUsers()
    {
        return $this->likedUsers;
    }

    /**
     * @param User[]|ArrayCollection $likedUsers
     */
    public function setLikedUsers($likedUsers): void
    {
        $this->likedUsers = $likedUsers;
    }

    /**
     * @return Conflict|null
     */
    public function getConflict(): ?Conflict
    {
        return $this->conflict;
    }

    /**
     * @param Conflict|null $conflict
     */
    public function setConflict(?Conflict $conflict): void
    {
        $this->conflict = $conflict;
    }

    /**
     * @return EventStatus|null
     */
    public function getEventStatus(): ?EventStatus
    {
        return $this->eventStatus;
    }

    /**
     * @param EventStatus|null $eventStatus
     */
    public function setEventStatus(?EventStatus $eventStatus): void
    {
        $this->eventStatus = $eventStatus;
    }

    /**
     * @return EventType|null
     */
    public function getEventType(): ?EventType
    {
        return $this->eventType;
    }

    /**
     * @param EventType|null $eventType
     */
    public function setEventType(?EventType $eventType): void
    {
        $this->eventType = $eventType;
    }

    /**
     * @return Comment[]|ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param Comment[]|ArrayCollection $comments
     */
    public function setComments($comments): void
    {
        $this->comments = $comments;
    }

    /**
     * @return Locality|null
     */
    public function getLocality(): ?Locality
    {
        return $this->locality;
    }

    /**
     * @param Locality|null $locality
     */
    public function setLocality(?Locality $locality): void
    {
        $this->locality = $locality;
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
     * @return integer|null
     */
    public function getDate()
    {
        return $this->date ? $this->date->getTimestamp() : null;
    }

    /**
     * @param int $date
     */
    public function setDate(int $date)
    {
        $this->date = DateTime::createFromFormat('U', $date);
    }

    /**
     * @return mixed
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @param mixed $views
     */
    public function setViews($views)
    {
        $this->views = $views;
    }

    /**
     * @return mixed
     */
    public function getSourceLink()
    {
        return $this->source_link;
    }

    /**
     * @param mixed $source_link
     */
    public function setSourceLink($source_link)
    {
        $this->source_link = $source_link;
    }

    /**
     * @return boolean
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * @param boolean $published
     */
    public function setPublished(bool $published): void
    {
        $this->published = $published;
    }
}