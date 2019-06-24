<?php

namespace App\Http\Controllers;

use App\Entities\References\Locality;
use App\Entities\References\Region;
use App\Http\Requests\Reference\LocalitySearchRequest;
use App\Http\Requests\Reference\LocalityStoreRequest;
use App\Http\Resources\Reference\LocalityResource;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Illuminate\Auth\Access\AuthorizationException;

class LocalityController extends Controller
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
     * Вернуть список населенных пунктов, попадающих под поисковую фразу
     * @param LocalitySearchRequest $request
     * @return mixed
     */
    public function index(LocalitySearchRequest $request, $locale)
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('l')
            ->from(Locality::class, 'l')
            ->where('lower(l.name) LIKE :name')
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
     * @param LocalityStoreRequest $request
     * @return LocalityResource
     * @throws AuthorizationException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function store(LocalityStoreRequest $request)
    {
        $this->authorize('moderate');

        $locality = new Locality;
        $locality->setName($request->name);
        $locality->setRegion($this->em->getReference(Region::class, $request->region_id));
        $this->em->persist($locality);
        $this->em->flush();

        return LocalityResource::make($locality);
    }
}
