<?php


namespace App\Entities;

use App\Entities\Traits\Timestamps;
use Doctrine\ORM\Mapping AS ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="news")
 */
class News
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
     * @var User|null
     */
    protected $user;

    /**
     * @ORM\ManyToMany(targetEntity="Comment")
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
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     */
    public function setUser(?User $user)
    {
        $this->user = $user;
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