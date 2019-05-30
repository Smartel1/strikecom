<?php


namespace App\Entities;

use App\Entities\Interfaces\Commentable;
use App\Entities\References\EventStatus;
use App\Entities\References\EventType;
use App\Entities\Traits\ContentsTrait;
use App\Entities\Traits\Timestamps;
use App\Entities\Traits\TitlesTrait;
use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="events")
 */
class Event implements Commentable
{
    use TitlesTrait;
    use ContentsTrait;
    use Timestamps;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
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
     * @ORM\ManyToMany(targetEntity="Photo")
     * @ORM\JoinTable(name="event_photo",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="photo_id", referencedColumnName="id")}
     *      )
     * @var ArrayCollection|Photo[]
     */
    protected $photos;

    /**
     * @ORM\ManyToMany(targetEntity="Video")
     * @ORM\JoinTable(name="event_video",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="video_id", referencedColumnName="id")}
     *      )
     * @var ArrayCollection|Video[]
     */
    protected $videos;

    /**
     * @ORM\ManyToMany(targetEntity="Tag")
     * @ORM\JoinTable(name="event_tag",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     *      )
     * @var ArrayCollection|Tag[]
     */
    protected $tags;

    /**
     * @todo переименовать в author
     * @ORM\ManyToOne(targetEntity="User")
     * @var User|null
     */
    protected $user;

    /**
     * Пользователи, которые отметили событие в избранное
     * @ORM\ManyToMany(targetEntity="User", inversedBy="favouriteEvents")
     * @ORM\JoinTable(name="favourite_events",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     *      )
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
     * @ORM\ManyToMany(targetEntity="Comment", inversedBy="events")
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
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
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
        return is_null($this->date) ? null : (integer) $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
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
     * Получить локализованный заголовок
     * @param string $locale
     * @return string
     */
    public function getTitleByLocale(string $locale) : ?string
    {
        $getterName = 'getTitle' . $locale;

        return $this->$getterName();
    }

    /**
     * Получить локализованное содержимое
     * @param string $locale
     * @return string
     */
    public function getContentByLocale(string $locale) : ?string
    {
        $getterName = 'getContent' . $locale;

        return $this->$getterName();
    }
}