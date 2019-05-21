<?php


namespace App\Services;


use App\Entities\Conflict;
use App\Entities\References\ConflictReason;
use App\Entities\References\ConflictResult;
use App\Entities\References\Industry;
use App\Entities\References\Region;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ConflictService
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * NewsService constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Вернуть конфликты из бд
     * @param array $filters
     * @return Collection
     */
    public function index(array $filters)
    {
        $expr = $this->em->getExpressionBuilder();

        $queryBuilder = $this->em->createQueryBuilder()
            ->select('c')
            ->from(Conflict::class, 'c');

        //Если передан фильтр по дате начала, добавляем условие
        $dateFrom = Arr::get($filters, 'date_from');

        if ($dateFrom) {
            $queryBuilder->andWhere($expr->gte('c.date_from', $dateFrom));
        }

        //Если передан фильтр по дате окончания, добавляем условие
        $dateTo = Arr::get($filters, 'date_to');

        if ($dateTo) {
            $queryBuilder->andWhere($expr->lte('c.date_to', $dateTo));
        }

        //Если указана конкретная локаль, то выводим только те конфликты, которые содержат локализованные события
        $locale = app('locale');

        if ($locale !== 'all') {
            $queryBuilder
                ->innerJoin('c.events', 'e')
                ->andWhere($expr->isNotNull('e.title_' . $locale))
                ->andWhere($expr->isNotNull('e.content_' . $locale));
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
        $this->setParent($conflict, Arr::get($data, 'parent_id'));

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
     * @param string|null $conflictReasonId
     * @throws ORMException
     */
    private function setConflictReason(Conflict $conflict, ?string $conflictReasonId)
    {
        if (!$conflictReasonId) {
            $conflict->setConflictReason(null);
            return;
        }

        /** @var $reason ConflictReason */
        $reason = $this->em->getReference('App\Entities\References\ConflictReason', $conflictReasonId);

        $conflict->setConflictReason($reason);
    }

    /**
     * Установить для конфликта его результат или null
     * @param Conflict $conflict
     * @param string|null $conflictResultId
     * @throws ORMException
     */
    private function setConflictResult(Conflict $conflict, ?string $conflictResultId)
    {
        if (!$conflictResultId) {
            $conflict->setConflictResult(null);
            return;
        }

        /** @var $result ConflictResult */
        $result = $this->em->getReference('App\Entities\References\ConflictResult', $conflictResultId);

        $conflict->setConflictResult($result);
    }

    /**
     * Установить для конфликта его отрасль или null
     * @param Conflict $conflict
     * @param string|null $industryId
     * @throws ORMException
     */
    private function setIndustry(Conflict $conflict, ?string $industryId)
    {
        if (!$industryId) {
            $conflict->setIndustry(null);
            return;
        }

        /** @var $industry Industry */
        $industry = $this->em->getReference('App\Entities\References\Industry', $industryId);

        $conflict->setIndustry($industry);
    }

    /**
     * Установить для конфликта его регион или null
     * @param Conflict $conflict
     * @param string|null $regionId
     * @throws ORMException
     */
    private function setRegion(Conflict $conflict, ?string $regionId)
    {
        if (!$regionId) {
            $conflict->setRegion(null);
            return;
        }

        /** @var $region Region */
        $region = $this->em->getReference('App\Entities\References\Region', $regionId);

        $conflict->setRegion($region);
    }

    /**
     * Установить родительский конфликт
     * @param Conflict $child
     * @param int|null $parentId
     * @throws ORMException
     */
    private function setParent(Conflict $child, ?int $parentId)
    {
        /** @var $parent Conflict*/
        $parent = null;

        if (!is_null($parentId)) {
            $parent = $this->em->getReference(Conflict::class, $parentId);
        }

        $child->setParent($parent);
    }

    /**
     * Удалить конфликт, а его детей привязать к его родителю
     * @param Conflict $conflict
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Conflict $conflict)
    {
        /** @var NestedTreeRepository $repo */
        $repo = $this->em->getRepository(Conflict::class);

        $repo->removeFromTree($conflict);

        $this->em->flush();
    }
}