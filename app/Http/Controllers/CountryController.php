<?php

namespace App\Http\Controllers;

use App\Entities\References\Country;
use App\Http\Requests\Reference\CountrySearchRequest;
use App\Http\Requests\Reference\CountryStoreRequest;
use App\Http\Resources\Reference\ReferenceResource;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Illuminate\Auth\Access\AuthorizationException;

class CountryController extends Controller
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
     * Вернуть список стран, попадающих под поисковую фразу
     * @param CountrySearchRequest $request
     * @return mixed
     */
    public function index(CountrySearchRequest $request, $locale)
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
     * @param CountryStoreRequest $request
     * @return ReferenceResource
     * @throws AuthorizationException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function store(CountryStoreRequest $request)
    {
        $this->authorize('moderate');

        $country = new Country;
        $country->setNameRu($request->name_ru);
        $country->setNameEn($request->name_en);
        $country->setNameEs($request->name_es);
        $this->em->persist($country);
        $this->em->flush();

        return ReferenceResource::make($country);
    }
}
