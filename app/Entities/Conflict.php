<?php


namespace App\Entities;

use App\Entities\References\ConflictReason;
use App\Entities\References\ConflictResult;
use App\Entities\References\Industry;
use App\Entities\Traits\CoordinatesTrait;
use App\Entities\Traits\Timestamps;
use App\Entities\Traits\TitlesTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="conflicts")
 */
class Conflict
{
    use TitlesTrait;
    use Timestamps;
    use CoordinatesTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    protected $company_name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime|null
     */
    protected $dateFrom;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime|null
     */
    protected $dateTo;

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
     * @ORM\OneToMany(targetEntity="Event", mappedBy="conflict")
     * @var Event[]|ArrayCollection
     */
    protected $events;

    /**
     * Связь с событием другого конфликта, от которого пошло ветвление
     * @ORM\ManyToOne(targetEntity="App\Entities\Event")
     * @ORM\JoinColumn(name="parent_event_id")
     * @var Event|null
     */
    protected $parentEvent;

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
        return $this->dateFrom ? $this->dateFrom->getTimestamp() : null;
    }

    /**
     * @param int|null $dateFrom
     */
    public function setDateFrom(?int $dateFrom): void
    {
        if (is_null($dateFrom)) {
            $this->dateFrom = null;
        } else {
            $this->dateFrom = DateTime::createFromFormat('U', $dateFrom);
        }
    }

    /**
     * @return integer|null
     */
    public function getDateTo()
    {
        return $this->dateTo ? $this->dateTo->getTimestamp() : null;
    }

    /**
     * @param int|null $dateTo
     */
    public function setDateTo(?int $dateTo): void
    {
        if (is_null($dateTo)) {
            $this->dateTo = null;
        } else {
            $this->dateTo = DateTime::createFromFormat('U', $dateTo);
        }
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
     * @return Event|null
     */
    public function getParentEvent(): ?Event
    {
        return $this->parentEvent;
    }

    /**
     * @param Event|null $parentEvent
     */
    public function setParentEvent(?Event $parentEvent): void
    {
        $this->parentEvent = $parentEvent;
    }
}