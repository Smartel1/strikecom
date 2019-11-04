<?php

namespace App\Http\Controllers;

use App\Entities\References\Country;
use App\Entities\References\Region;
use App\Http\Requests\Reference\RegionSearchRequest;
use App\Http\Requests\Reference\RegionStoreRequest;
use App\Http\Resources\Reference\RegionResource;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Illuminate\Auth\Access\AuthorizationException;

class RegionController extends Controller
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
     * Вернуть список регионов, попадающих под поисковую фразу
     * @param RegionSearchRequest $request
     * @return mixed
     */
    public function index(RegionSearchRequest $request, $locale)
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('r')
            ->from(Region::class, 'r')
            ->where('lower(r.name) LIKE :name')
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
     * @param RegionStoreRequest $request
     * @return RegionResource
     * @throws AuthorizationException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function store(RegionStoreRequest $request)
    {
        $this->authorize('moderate');

        $region = new Region;
        $region->setName($request->name);
        $region->setCountry($this->em->getReference(Country::class, $request->country_id));
        $this->em->persist($region);
        $this->em->flush();

        return RegionResource::make($region);
    }
}
