<?php

namespace App\Http\Controllers;

use App\Entities\References\ClaimType;
use App\Entities\References\ConflictReason;
use App\Entities\References\ConflictResult;
use App\Entities\References\Country;
use App\Entities\References\EventStatus;
use App\Entities\References\EventType;
use App\Entities\References\Industry;
use App\Entities\References\Locality;
use App\Entities\References\Region;
use App\Entities\References\VideoType;
use App\Http\Requests\Reference\CountrySearchRequest;
use App\Http\Requests\Reference\LocalitySearchRequest;
use App\Http\Requests\Reference\RegionSearchRequest;
use App\Http\Resources\Reference\LocalityResource;
use App\Http\Resources\Reference\ReferenceResource;
use App\Http\Requests\Reference\ReferenceIndexRequest;
use App\Http\Resources\Reference\RegionResource;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\Collection;

class RefController extends Controller
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * RefController constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Получить список справочников
     * @param ReferenceIndexRequest $request
     * @param $locale
     * @return array
     */
    public function index(ReferenceIndexRequest $request, $locale)
    {
        $conflictReasons = $this->localizeReference($this->getReference(ConflictReason::class));
        $conflictResults = $this->localizeReference($this->getReference(ConflictResult::class));
        $eventStatuses = $this->localizeReference($this->getReference(EventStatus::class));
        $claimTypes = $this->localizeReference($this->getReference(ClaimType::class));
        $eventTypes = $this->localizeReference($this->getReference(EventType::class));
        $industries = $this->localizeReference($this->getReference(Industry::class));

        $videoTypes = $this->em
            ->createQuery('SELECT ref FROM App\Entities\References\VideoType ref')
            ->getResult();

        $videoTypes = collect($videoTypes)->map(function (VideoType $type) {
            return [
                'id'   => $type->getId(),
                'code' => $type->getCode()
            ];
        });

        return compact(
            'conflictReasons',
            'conflictResults',
            'eventStatuses',
            'claimTypes',
            'eventTypes',
            'industries',
            'videoTypes');
    }

    /**
     * Получить контрольную сумму справочников для контроля изменений
     * @return array
     */
    public function checkSum()
    {
        //функция, которую передадим в reducer для создания md5 хэша сущностей
        $reducerCallback = function ($carry, $item) {
            return md5($carry . $item->getId() .$item->getNameRu() . $item->getNameEn() . $item->getNameEs());
        };

        $conflictReasonCheckSum = $this->getReference(ConflictReason::class)->reduce($reducerCallback);
        $conflictResultCheckSum = $this->getReference(ConflictResult::class)->reduce($reducerCallback);
        $eventStatusCheckSum = $this->getReference(EventStatus::class)->reduce($reducerCallback);
        $claimTypeCheckSum = $this->getReference(ClaimType::class)->reduce($reducerCallback);
        $eventTypeCheckSum = $this->getReference(EventType::class)->reduce($reducerCallback);
        $industryCheckSum = $this->getReference(Industry::class)->reduce($reducerCallback);

        //хэш типа видео вычисляется по полям id и code
        $videoTypeCheckSum = $this->getReference(VideoType::class)->reduce(function ($carry, VideoType $item) {
            return md5($carry . $item->getId() . $item->getCode());
        });

        return [
            'checkSum' => md5(
                $conflictReasonCheckSum
                . $conflictResultCheckSum
                . $eventStatusCheckSum
                . $eventTypeCheckSum
                . $claimTypeCheckSum
                . $industryCheckSum
                . $videoTypeCheckSum)
        ];
    }

    /**
     * Вернуть список стран, попадающих под поисковую фразу
     * @param CountrySearchRequest $request
     * @return mixed
     */
    public function searchCountry(CountrySearchRequest $request, $locale)
    {
        $countries = $this->em->createQueryBuilder()
            ->select('c')
            ->from(Country::class, 'c')
            ->where('lower(c.name_ru) LIKE :name')
            ->orWhere('lower(c.name_en) LIKE :name')
            ->orWhere('lower(c.name_es) LIKE :name')
            ->setParameter('name', '%'.mb_strtolower($request->name).'%')
            ->getQuery()
            ->getResult();

        return ReferenceResource::collection(collect($countries));
    }

    /**
     * Вернуть список стран, попадающих под поисковую фразу
     * @param RegionSearchRequest $request
     * @return mixed
     */
    public function searchRegion(RegionSearchRequest $request, $locale)
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('r')
            ->from(Region::class, 'r')
            ->where($this->em->getExpressionBuilder()->orX(
                'lower(r.name_ru) LIKE :name',
                'lower(r.name_en) LIKE :name',
                'lower(r.name_es) LIKE :name'
            ))
            ->setParameter('name', '%'.mb_strtolower($request->name).'%');

        //Если передали country_id, ищем регионы в рамках страны
        if ($request->has('country_id')) {
            $queryBuilder->andWhere('r.country = :countryId')
                ->setParameter('countryId', $request->country_id);
        }

        $regions = $queryBuilder->getQuery()
            ->getResult();

        return RegionResource::collection(collect($regions));
    }

    /**
     * Вернуть список населенных пунктов, попадающих под поисковую фразу
     * @param LocalitySearchRequest $request
     * @return mixed
     */
    public function searchLocality(LocalitySearchRequest $request, $locale)
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('l')
            ->from(Locality::class, 'l')
            ->where($this->em->getExpressionBuilder()->orX(
                'lower(l.name_ru) LIKE :name',
                'lower(l.name_en) LIKE :name',
                'lower(l.name_es) LIKE :name'
            ))
            ->setParameter('name', '%'.mb_strtolower($request->name).'%');

        //Если передали region_id, ищем нас. пункты в рамках региона
        if ($request->has('region_id')) {
            $queryBuilder->andWhere('l.region = :regionId')
                ->setParameter('regionId', $request->region_id);
        }

        $localities = $queryBuilder->getQuery()
            ->getResult();

        return LocalityResource::collection(collect($localities));
    }

    /**
     * Получить справочник по типу сущности в виде массива
     * @param $class
     * @return Collection
     */
    private function getReference($class)
    {
        $array = $this->em
            ->createQuery('SELECT ref FROM '. $class .' ref')
            ->getResult();

        return collect($array);
    }

    /**
     * Локализовать справочник в зависимости от переданной в path локали
     * @param $referenceCollection Collection
     * @return array
     */
    private function localizeReference(Collection $referenceCollection)
    {
        return ReferenceResource::collection($referenceCollection)->toArray(null);
    }
}
