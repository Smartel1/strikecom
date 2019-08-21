<?php


namespace App\Entities;

use App\Entities\Traits\Timestamps;
use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User implements Authenticatable
{
    use Timestamps;

    public static $ROLE_ADMIN = 'ADMIN';
    public static $ROLE_MODERATOR = 'MODERATOR';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $uuid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $email;

    /**
     * Роли хранятся как неассоциативный массив в json
     * @ORM\Column(type="json", options={"default"="[]"})
     */
    protected $roles = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $fcm;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    protected $imageUrl;

    /**
     * @ORM\ManyToMany(targetEntity="Event", inversedBy="likedUsers")
     * @ORM\JoinTable(name="favourite_events",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     * @var ArrayCollection|Event[]
     */
    protected $favouriteEvents;

    /**
     * @ORM\ManyToMany(targetEntity="News", inversedBy="likedUsers")
     * @ORM\JoinTable(name="favourite_news",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="news_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     * @var ArrayCollection|News[]
     */
    protected $favouriteNews;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->favouriteEvents = new ArrayCollection();
        $this->favouriteNews = new ArrayCollection();
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
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param mixed $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return array
     */
    public function getRoles() : array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @return mixed
     */
    public function getFcm()
    {
        return $this->fcm;
    }

    /**
     * @param mixed $fcm
     */
    public function setFcm($fcm)
    {
        $this->fcm = $fcm;
    }

    /**
     * @return mixed
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * @param mixed $imageUrl
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->id;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return null;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return null;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $value
     * @return void
     */
    public function setRememberToken($value){ }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return '';
    }

    /**
     * @return Event[]|ArrayCollection
     */
    public function getFavouriteEvents()
    {
        return $this->favouriteEvents;
    }

    /**
     * @param Event[]|ArrayCollection $favouriteEvents
     */
    public function setFavouriteEvents($favouriteEvents): void
    {
        $this->favouriteEvents = $favouriteEvents;
    }

    /**
     * @return News[]|ArrayCollection
     */
    public function getFavouriteNews()
    {
        return $this->favouriteNews;
    }

    /**
     * @param News[]|ArrayCollection $favouriteNews
     */
    public function setFavouriteNews($favouriteNews): void
    {
        $this->favouriteNews = $favouriteNews;
    }

}