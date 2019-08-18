<?php


namespace App\Services;


use App\Criteria\AncestorsOfConflict;
use App\Criteria\ChildrenOfConflict;
use App\Criteria\HasLocalizedContent;
use App\Criteria\HasLocalizedTitle;
use App\Criteria\SafeGTE;
use App\Criteria\SafeIn;
use App\Criteria\SafeLTE;
use App\Entities\Conflict;
use App\Entities\Event;
use App\Entities\References\ConflictReason;
use App\Entities\References\ConflictResult;
use App\Entities\References\Industry;
use App\Exceptions\BusinessRuleValidationException;
use App\Rules\NotAParentConflict;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\QueryException;
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
            ->addCriteria(SafeGTE::make(
                'c.dateFrom',
                Arr::has($filters, 'date_from')
                    ? Datetime::createFromFormat('U', Arr::get($filters, 'date_from'))
                    : null
            ))
            ->addCriteria(SafeLTE::make(
                'c.dateTo',
                Arr::has($filters, 'date_to')
                    ? Datetime::createFromFormat('U', Arr::get($filters, 'date_to'))
                    : null
            ))
            ->addCriteria(SafeIn::make('c.conflictResult', Arr::get($filters, 'conflict_result_ids')))
            ->addCriteria(SafeIn::make('c.conflictReason', Arr::get($filters, 'conflict_reason_ids')))
            ->addCriteria(AncestorsOfConflict::make('c', Arr::get($filters, 'ancestors_of')))
            ->addCriteria(ChildrenOfConflict::make('c', Arr::get($filters, 'children_of')))
            //Если указана конкретная локаль, то выводим только те конфликты, которые содержат локализованные события
            ->innerJoin('c.events', 'e')
            ->addCriteria(HasLocalizedTitle::make('e', app('locale')))
            ->addCriteria(HasLocalizedContent::make('e', app('locale')));

        //Если передан фильтр "вблизи точки", то применяем ограничение по формуле гаверсинуса
        //https://stackoverflow.com/questions/21084886/how-to-calculate-distance-using-latitude-and-longitude
        if (Arr::has($filters, 'near')) {
            $queryBuilder->andWhere('6371 * acos(cos(radians(:lat)) * cos(radians(c.latitude)) * cos(radians(c.longitude) - radians(:lng)) + sin(radians(:lat)) * sin(radians(c.latitude))) <= :radius')
                ->setParameter('lat', Arr::get($filters, 'near.lat'))
                ->setParameter('lng', Arr::get($filters, 'near.lng'))
                ->setParameter('radius', Arr::get($filters, 'near.radius'));
        }

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
        if (Arr::get($data, 'title_ru')) $conflict->setTitleRu(Arr::get($data, 'title_ru'));
        if (Arr::get($data, 'title_en')) $conflict->setTitleEn(Arr::get($data, 'title_en'));
        if (Arr::get($data, 'title_es')) $conflict->setTitleEs(Arr::get($data, 'title_es'));
        if (Arr::get($data, 'latitude')) $conflict->setLatitude(Arr::get($data, 'latitude'));
        if (Arr::get($data, 'longitude')) $conflict->setLongitude(Arr::get($data, 'longitude'));
        if (Arr::get($data, 'company_name')) $conflict->setCompanyName(Arr::get($data, 'company_name'));
        if (Arr::get($data, 'date_from')) $conflict->setDateFrom(Arr::get($data, 'date_from'));
        if (Arr::get($data, 'date_to')) $conflict->setDateTo(Arr::get($data, 'date_to'));

        if (Arr::get($data, 'conflict_reason_id')) $this->setConflictReason($conflict, Arr::get($data, 'conflict_reason_id'));
        if (Arr::get($data, 'conflict_result_id')) $this->setConflictResult($conflict, Arr::get($data, 'conflict_result_id'));
        if (Arr::get($data, 'industry_id')) $this->setIndustry($conflict, Arr::get($data, 'industry_id'));
        if (Arr::get($data, 'parent_event_id')) $this->setParentEvent($conflict, Arr::get($data, 'parent_event_id'));

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