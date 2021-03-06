<?php


namespace App\Entities;

use App\Entities\References\VideoType;
use App\Entities\Traits\Timestamps;
use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="videos")
 */
class Video
{
    use Timestamps;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=500)
     */
    protected $url;

    /**
     * @ORM\Column(type="integer")
     */
    protected $video_type_id; //todo выпилить?

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    protected $preview_url;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\References\VideoType")
     * @ORM\JoinColumn(name="video_type_id", referencedColumnName="id")
     */
    protected $videoType;

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
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getPreviewUrl()
    {
        return $this->preview_url;
    }

    /**
     * @param mixed $preview_url
     */
    public function setPreviewUrl($preview_url)
    {
        $this->preview_url = $preview_url;
    }

    /**
     * @return VideoType
     */
    public function getVideoType()
    {
        return $this->videoType;
    }

    /**
     * @param mixed VideoType
     */
    public function setVideoType($videoType)
    {
        $this->videoType = $videoType;
    }

}
