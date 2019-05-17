<?php


namespace App\Services;


use App\Entities\ClientVersion;
use App\Exceptions\BusinessRuleValidationException;
use App\Rules\UniqueVersion;
use App\Rules\VersionExists;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr;
use Illuminate\Support\Arr;

class ClientVersionService
{
    protected $businessValidationService;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * NewsService constructor.
     * @param EntityManager $em
     * @param BusinessValidationService $businessValidationService
     */
    public function __construct(EntityManager $em, BusinessValidationService $businessValidationService)
    {
        $this->em = $em;
        $this->businessValidationService = $businessValidationService;
    }

    /**
     * Найти новые версии, которые вышли для клиента с clientId позднее версии currentVersion
     * @param $clientId
     * @param $currentVersion
     * @return mixed
     * @throws BusinessRuleValidationException
     */
    public function getNewVersions($clientId, $currentVersion)
    {
        //Проверяем, что переданная версия есть в базе
        $this->businessValidationService->validate([
            new VersionExists($currentVersion, $clientId)
        ]);

        try {
            $currentClientVersion = $this->em->createQueryBuilder()
                ->from('App\Entities\ClientVersion', 'cv')
                ->where('cv.client_id = :clientId')
                ->andWhere('cv.version = :currentVersion')
                ->setParameter('clientId', $clientId)
                ->setParameter('currentVersion', $currentVersion)
                ->select('cv')
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $exception) {
            throw new BusinessRuleValidationException(['В системе есть две записи об одной и той же версии']);
        }

        $newClientVersions = $this->em->createQueryBuilder()
            ->from('App\Entities\ClientVersion', 'cv')
            ->where('cv.client_id = :clientId')
            ->andWhere('cv.id > :currentVersionId')
            ->setParameter('clientId', $clientId)
            ->setParameter('currentVersionId', $currentClientVersion->getId())
            ->select('cv')
            ->getQuery()
            ->getResult();

        return $newClientVersions;
    }

    /**
     * Сохранить описание новой версии клиента
     * @param $data
     * @return ClientVersion
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws BusinessRuleValidationException
     */
    public function create($data)
    {
        //Проверяем, что еще не существует такой версии
        $this->businessValidationService->validate([
            new UniqueVersion(Arr::get($data, 'version'), Arr::get($data, 'client_id'))
        ]);

        $version = new ClientVersion();
        $version->setClientId(Arr::get($data, 'client_id'));
        $version->setVersion(Arr::get($data, 'version'));
        $version->setRequired(Arr::get($data, 'required'));
        $version->setDescriptionRu(Arr::get($data, 'description_ru'));
        $version->setDescriptionEn(Arr::get($data, 'description_en'));
        $version->setDescriptionEs(Arr::get($data, 'description_es'));

        $this->em->persist($version);
        $this->em->flush();

        return $version;
    }

    /**
     * @param ClientVersion $version
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(ClientVersion $version)
    {
        $this->em->remove($version);
        $this->em->flush();
    }
}