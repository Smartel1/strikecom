<?php


namespace App\Entities;

use App\Entities\Interfaces\Commentable;
use App\Entities\Traits\ContentsTrait;
use App\Entities\Traits\Timestamps;
use App\Entities\Traits\TitlesTrait;
use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="news")
 */
class News implements Commentable
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
     * @ORM\Column(type="boolean")
     */
    protected $published = false;

    /**
     * @ORM\ManyToMany(targetEntity="Photo")
     * @ORM\JoinTable(name="news_photo",
     *      joinColumns={@ORM\JoinColumn(name="news_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="photo_id", referencedColumnName="id")}
     *      )
     * @var ArrayCollection|Photo[]
     */
    protected $photos;

    /**
     * @ORM\ManyToMany(targetEntity="Video")
     * @ORM\JoinTable(name="news_video",
     *      joinColumns={@ORM\JoinColumn(name="news_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="video_id", referencedColumnName="id")}
     *      )
     * @var ArrayCollection|Video[]
     */
    protected $videos;

    /**
     * @ORM\ManyToMany(targetEntity="Tag")
     * @ORM\JoinTable(name="news_tag",
     *      joinColumns={@ORM\JoinColumn(name="news_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     *      )
     * @var ArrayCollection|Tag[]
     */
    protected $tags;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="cascade")
     * @var User|null
     */
    protected $author;

    /**
     * Пользователи, которые отметили новость в избранное
     * @ORM\ManyToMany(targetEntity="User", mappedBy="favouriteNews")
     * @var ArrayCollection|User[]
     */
    protected $likedUsers;

    /**
     * @ORM\ManyToMany(targetEntity="Comment", inversedBy="news")
     * @var ArrayCollection|Comment[]
     */
    protected $comments;

    /**
     * News constructor.
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
     * @return bool
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * @param bool $published
     */
    public function setPublished(bool $published): void
    {
        $this->published = $published;
    }

    /**
     * Получить локализованный заголовок
     * @param string $locale
     * @return string|null
     */
    public function getTitleByLocale(string $locale) : ?string
    {
        $getterName = 'getTitle' . $locale;

        return $this->$getterName();
    }

    /**
     * Получить локализованное содержимое
     * @param string $locale
     * @return string|null
     */
    public function getContentByLocale(string $locale) : ?string
    {
        $getterName = 'getContent' . $locale;

        return $this->$getterName();
    }
}