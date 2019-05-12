<?php


namespace App\Entities;

use App\Entities\References\ConflictReason;
use App\Entities\References\ConflictResult;
use App\Entities\References\Industry;
use App\Entities\References\Region;
use App\Entities\Traits\Timestamps;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="conflicts")
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
     * @ORM\Column(type="integer")
     */
    protected $conflict_reason_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $conflict_result_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $industry_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $region_id;

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
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude): void
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
    public function getConflictReasonId()
    {
        if ($this->conflictReason !== null) return $this->conflictReason->getId();

        return $this->conflict_reason_id;
    }

    /**
     * @param mixed $conflict_reason_id
     */
    public function setConflictReasonId($conflict_reason_id): void
    {
        $this->conflict_reason_id = $conflict_reason_id;
    }

    /**
     * @return mixed
     */
    public function getConflictResultId()
    {
        if ($this->conflictResult !== null) return $this->conflictResult->getId();

        return $this->conflict_result_id;
    }

    /**
     * @param mixed $conflict_result_id
     */
    public function setConflictResultId($conflict_result_id): void
    {
        $this->conflict_result_id = $conflict_result_id;
    }

    /**
     * @return mixed
     */
    public function getIndustryId()
    {
        if ($this->industry !== null) return $this->industry->getId();

        return $this->industry_id;
    }

    /**
     * @param mixed $industry_id
     */
    public function setIndustryId($industry_id): void
    {
        $this->industry_id = $industry_id;
    }

    /**
     * @return mixed
     */
    public function getRegionId()
    {
        if ($this->region !== null) return $this->region->getId();

        return $this->region_id;
    }

    /**
     * @param mixed $region_id
     */
    public function setRegionId($region_id): void
    {
        $this->region_id = $region_id;
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
}