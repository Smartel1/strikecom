<?php


namespace App\Entities;

use App\Entities\References\ConflictReason;
use App\Entities\References\ConflictResult;
use App\Entities\References\Industry;
use App\Entities\References\Region;
use App\Entities\Traits\Timestamps;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="conflicts")
 * @Gedmo\Mapping\Annotation\Tree(type="nested")
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\NestedTreeRepository")
 */
class Conflict
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
     * @ORM\Column(type="float")
     */
    protected $latitude;

    /**
     * @ORM\Column(type="float")
     */
    protected $longitude;

    /**
     * @ORM\Column(type="string", length=500)
     */
    protected $company_name;

    /**
     * @ORM\Column(type="integer")
     */
    protected $date_from;

    /**
     * @ORM\Column(type="integer")
     */
    protected $date_to;

    /**
     * @Gedmo\Mapping\Annotation\TreeLeft
     * @ORM\Column(type="integer")
     */
    protected $_lft;

    /**
     * @Gedmo\Mapping\Annotation\TreeRight
     * @ORM\Column(type="integer")
     */
    protected $_rgt;

    /**
     * @Gedmo\Mapping\Annotation\TreeParent
     * @ORM\ManyToOne(targetEntity="Conflict", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Conflict", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    protected $children;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\References\ConflictReason")
     * @var ConflictReason|null
     */
    protected $conflictReason;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\References\ConflictResult")
     * @var ConflictResult|null
     */
    protected $conflictResult;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\References\Industry")
     * @var Industry|null
     */
    protected $industry;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\References\Region")
     * @var Region|null
     */
    protected $region;

    /**
     * @ORM\OneToMany(targetEntity="Event", mappedBy="conflict")
     * @var Event[]|ArrayCollection
     */
    protected $events;

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
    public function setId($id): void
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
    public function setTitleRu($title_ru): void
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
    public function setTitleEn($title_en): void
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
    public function setTitleEs($title_es): void
    {
        $this->title_es = $title_es;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @return mixed
     */
    public function getCompanyName()
    {
        return $this->company_name;
    }

    /**
     * @param mixed $company_name
     */
    public function setCompanyName($company_name): void
    {
        $this->company_name = $company_name;
    }

    /**
     * @return integer|null
     */
    public function getDateFrom()
    {
        return is_null($this->date_from) ? null : (integer) $this->date_from;
    }

    /**
     * @param mixed $date_from
     */
    public function setDateFrom($date_from): void
    {
        $this->date_from = $date_from;
    }

    /**
     * @return integer|null
     */
    public function getDateTo()
    {
        return is_null($this->date_to) ? null : (integer) $this->date_to;
    }

    /**
     * @param mixed $date_to
     */
    public function setDateTo($date_to): void
    {
        $this->date_to = $date_to;
    }

    /**
     * @return mixed
     */
    public function getLft()
    {
        return $this->_lft;
    }

    /**
     * @param mixed $lft
     */
    public function setLft($lft): void
    {
        $this->_lft = $lft;
    }

    /**
     * @return mixed
     */
    public function getRgt()
    {
        return $this->_rgt;
    }

    /**
     * @param int|null $rgt
     */
    public function setRgt($rgt): void
    {
        $this->_rgt = $rgt;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Conflict|null $parent
     */
    public function setParent(?Conflict $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param mixed $children
     */
    public function setChildren($children): void
    {
        $this->children = $children;
    }

    /**
     * @return ConflictReason|null
     */
    public function getConflictReason(): ?ConflictReason
    {
        return $this->conflictReason;
    }

    /**
     * @param ConflictReason|null $conflictReason
     */
    public function setConflictReason(?ConflictReason $conflictReason): void
    {
        $this->conflictReason = $conflictReason;
    }

    /**
     * @return ConflictResult|null
     */
    public function getConflictResult(): ?ConflictResult
    {
        return $this->conflictResult;
    }

    /**
     * @param ConflictResult|null $conflictResult
     */
    public function setConflictResult(?ConflictResult $conflictResult): void
    {
        $this->conflictResult = $conflictResult;
    }

    /**
     * @return Industry|null
     */
    public function getIndustry(): ?Industry
    {
        return $this->industry;
    }

    /**
     * @param Industry|null $industry
     */
    public function setIndustry(?Industry $industry): void
    {
        $this->industry = $industry;
    }

    /**
     * @return Region|null
     */
    public function getRegion(): ?Region
    {
        return $this->region;
    }

    /**
     * @param Region|null $region
     */
    public function setRegion(?Region $region): void
    {
        $this->region = $region;
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
     * Получить локализованный заголовок
     * @param string $locale
     * @return string|null
     */
    public function getTitleByLocale(string $locale) : ?string
    {
        $getterName = 'getTitle' . $locale;

        return $this->$getterName();
    }
}