<?php


namespace App\Services;


use App\Criteria\AncestorsOfConflict;
use App\Criteria\DateFromAfter;
use App\Criteria\DateToBefore;
use App\Criteria\HasLocalizedContent;
use App\Criteria\HasLocalizedTitle;
use App\Entities\Conflict;
use App\Entities\Event;
use App\Entities\References\ConflictReason;
use App\Entities\References\ConflictResult;
use App\Entities\References\Industry;
use App\Entities\References\Region;
use App\Exceptions\BusinessRuleValidationException;
use App\Rules\NotAParentConflict;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\TransactionRequiredException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ConflictService
{
    /**
     * @var EntityManager
     */
    protected $em;
    /**
     * @var BusinessValidationService
     */
    protected $bvs;

    /**
     * NewsService constructor.
     * @param EntityManager $em
     * @param BusinessValidationService $bvs
     */
    public function __construct(EntityManager $em, BusinessValidationService $bvs)
    {
        $this->em = $em;
        $this->bvs = $bvs;
    }

    /**
     * Вернуть конфликты из бд
     * @param array $filters
     * @return Collection
     * @throws QueryException
     */
    public function index(array $filters)
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('c')
            ->from(Conflict::class, 'c')
            ->addCriteria(DateFromAfter::make('c', Arr::get($filters, 'date_from')))
            ->addCriteria(DateToBefore::make('c', Arr::get($filters, 'date_to')))
            ->addCriteria(AncestorsOfConflict::make('c', Arr::get($filters, 'ancestors_of')))
            //Если указана конкретная локаль, то выводим только те конфликты, которые содержат локализованные события
            ->innerJoin('c.events', 'e')
            ->addCriteria(HasLocalizedTitle::make('e', app('locale')))
            ->addCriteria(HasLocalizedContent::make('e', app('locale')));

        $conflicts = $queryBuilder->getQuery()->getResult();

        return collect($conflicts);
    }

    /**
     * @param $data
     * @return Conflict
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create($data)
    {
        $this->em->beginTransaction();

        $conflict = new Conflict();
        $this->fillConflictFields($conflict, $data);

        $this->em->persist($conflict);
        $this->em->flush();
        $this->em->commit();

        return $conflict;
    }

    /**
     * @param Conflict $conflict
     * @param $data
     * @return Conflict
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function update(Conflict $conflict, $data)
    {
        $this->em->beginTransaction();

        $this->fillConflictFields($conflict, $data);

        $this->em->persist($conflict);
        $this->em->flush();
        $this->em->commit();

        return $conflict;
    }

    /**
     * @param Conflict $conflict
     * @param $data
     * @throws ORMException
     */
    private function fillConflictFields(Conflict $conflict, $data)
    {
        //todo use Builder pattern
        $conflict->setTitleRu(Arr::get($data, 'title_ru'));
        $conflict->setTitleEn(Arr::get($data, 'title_en'));
        $conflict->setTitleEs(Arr::get($data, 'title_es'));
        $conflict->setLatitude(Arr::get($data, 'latitude'));
        $conflict->setLongitude(Arr::get($data, 'longitude'));
        $conflict->setCompanyName(Arr::get($data, 'company_name'));
        $conflict->setDateFrom(Arr::get($data, 'date_from'));
        $conflict->setDateTo(Arr::get($data, 'date_to'));

        $this->setConflictReason($conflict, Arr::get($data, 'conflict_reason_id'));
        $this->setConflictResult($conflict, Arr::get($data, 'conflict_result_id'));
        $this->setIndustry($conflict, Arr::get($data, 'industry_id'));
        $this->setRegion($conflict, Arr::get($data, 'region_id'));
        $this->setParentEvent($conflict, Arr::get($data, 'parent_event_id'));

        $locale = app('locale');

        //В зависимости от локали
        //при сохранении новости мы поле title записываем в поле title_ru [en/es]
        if (Arr::has($data, 'title') and $locale !== 'all') {
            $titleSetterName = 'setTitle' . $locale;
            $conflict->$titleSetterName(Arr::get($data, 'title'));
        }
    }

    /**
     * Установить для конфликта его причину или null
     * @param Conflict $conflict
     * @param int|null $conflictReasonId
     * @throws ORMException
     */
    private function setConflictReason(Conflict $conflict, ?int $conflictReasonId)
    {
        if (!$conflictReasonId) {
            $conflict->setConflictReason(null);
            return;
        }

        /** @var $reason ConflictReason */
        $reason = $this->em->getReference(ConflictReason::class, $conflictReasonId);

        $conflict->setConflictReason($reason);
    }

    /**
     * Установить для конфликта его результат или null
     * @param Conflict $conflict
     * @param int|null $conflictResultId
     * @throws ORMException
     */
    private function setConflictResult(Conflict $conflict, ?int $conflictResultId)
    {
        if (!$conflictResultId) {
            $conflict->setConflictResult(null);
            return;
        }

        /** @var $result ConflictResult */
        $result = $this->em->getReference(ConflictResult::class, $conflictResultId);

        $conflict->setConflictResult($result);
    }

    /**
     * Установить для конфликта его отрасль или null
     * @param Conflict $conflict
     * @param int|null $industryId
     * @throws ORMException
     */
    private function setIndustry(Conflict $conflict, ?int $industryId)
    {
        if (!$industryId) {
            $conflict->setIndustry(null);
            return;
        }

        /** @var $industry Industry */
        $industry = $this->em->getReference(Industry::class, $industryId);

        $conflict->setIndustry($industry);
    }

    /**
     * Установить для конфликта его регион или null
     * @param Conflict $conflict
     * @param int|null $regionId
     * @throws ORMException
     */
    private function setRegion(Conflict $conflict, ?int $regionId)
    {
        if (!$regionId) {
            $conflict->setRegion(null);
            return;
        }

        /** @var $region Region */
        $region = $this->em->getReference(Region::class, $regionId);

        $conflict->setRegion($region);
    }

    /**
     * @param Conflict $conflict
     * @param int|null $parentEventId
     * @throws ORMException
     */
    private function setParentEvent(Conflict $conflict, ?int $parentEventId)
    {
        if (!$parentEventId) {
            $conflict->setParentEvent(null);
            return;
        }

        /** @var $parentEvent Event */
        $parentEvent = $this->em->getReference(Event::class, $parentEventId);

        $conflict->setParentEvent($parentEvent);
    }

    /**
     * @param Conflict $conflict
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws BusinessRuleValidationException
     */
    public function delete(Conflict $conflict)
    {
        $this->bvs->validate([
            new NotAParentConflict($conflict)
        ]);

        $this->em->remove($conflict);

        $this->em->flush();
    }
}