<?php


namespace App\Entities;

use App\Entities\References\EventStatus;
use App\Entities\References\EventType;
use App\Entities\Traits\Timestamps;
use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="events")
 */
class Event
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
    protected $title_ru;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $title_en;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $title_es;

    /**
     * @ORM\Column(type="string")
     */
    protected $content_ru;

    /**
     * @ORM\Column(type="string")
     */
    protected $content_en;

    /**
     * @ORM\Column(type="string")
     */
    protected $content_es;

    /**
     * @ORM\Column(type="integer")
     */
    protected $date;

    /**
     * @ORM\Column(type="integer")
     */
    protected $views = 0;

    /**
     * @ORM\Column(type="string", length=500)
     */
    protected $source_link;

    /**
     * @ORM\Column(type="integer")
     */
    protected $conflict_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $event_status_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $event_type_id;

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
     * @ORM\ManyToOne(targetEntity="User")
     * @var User|null
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Conflict")
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
     * @ORM\ManyToMany(targetEntity="Comment")
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
     * @return mixed
     */
    public function getConflictId()
    {
        if ($this->conflict !== null) return $this->conflict->getId();

        return $this->conflict_id;
    }

    /**
     * @param mixed $conflict_id
     */
    public function setConflictId($conflict_id): void
    {
        $this->conflict_id = $conflict_id;
    }

    /**
     * @return mixed
     */
    public function getEventStatusId()
    {
        if ($this->eventStatus !== null) return $this->eventStatus->getId();

        return $this->event_status_id;
    }

    /**
     * @param mixed $event_status_id
     */
    public function setEventStatusId($event_status_id): void
    {
        $this->event_status_id = $event_status_id;
    }

    /**
     * @return mixed
     */
    public function getEventTypeId()
    {
        if ($this->eventType !== null) return $this->eventType->getId();

        return $this->event_type_id;
    }

    /**
     * @param mixed $event_type_id
     */
    public function setEventTypeId($event_type_id): void
    {
        $this->event_type_id = $event_type_id;
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
     * @return mixed
     */
    public function getTitleRu()
    {
        return $this->title_ru;
    }

    /**
     * @param mixed $title_ru
     */
    public function setTitleRu($title_ru)
    {
        $this->title_ru = $title_ru;
    }

    /**
     * @return mixed
     */
    public function getTitleEn()
    {
        return $this->title_en;
    }

    /**
     * @param mixed $title_en
     */
    public function setTitleEn($title_en)
    {
        $this->title_en = $title_en;
    }

    /**
     * @return mixed
     */
    public function getTitleEs()
    {
        return $this->title_es;
    }

    /**
     * @param mixed $title_es
     */
    public function setTitleEs($title_es)
    {
        $this->title_es = $title_es;
    }

    /**
     * @return mixed
     */
    public function getContentRu()
    {
        return $this->content_ru;
    }

    /**
     * @param mixed $content_ru
     */
    public function setContentRu($content_ru)
    {
        $this->content_ru = $content_ru;
    }

    /**
     * @return mixed
     */
    public function getContentEn()
    {
        return $this->content_en;
    }

    /**
     * @param mixed $content_en
     */
    public function setContentEn($content_en)
    {
        $this->content_en = $content_en;
    }

    /**
     * @return mixed
     */
    public function getContentEs()
    {
        return $this->content_es;
    }

    /**
     * @param mixed $content_es
     */
    public function setContentEs($content_es)
    {
        $this->content_es = $content_es;
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
}