<?php


namespace App\Rules;


use Doctrine\ORM\NonUniqueResultException;
use LaravelDoctrine\ORM\Facades\EntityManager;

class VersionExists extends BusinessRule
{
    private $version;
    private $clientId;

    /**
     * VersionExists constructor.
     * @param $version
     * @param $clientId
     */
    public function __construct($version, $clientId)
    {
        $this->version = $version;
        $this->clientId = $clientId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @return bool
     * @throws NonUniqueResultException
     */
    public function passes()
    {
        $versionCount = EntityManager::createQueryBuilder()
            ->from('App\Entities\ClientVersion', 'cv')
            ->where('cv.client_id = :clientId')
            ->andWhere('cv.version = :version')
            ->setParameter('clientId', $this->clientId)
            ->setParameter('version', $this->version)
            ->select('count(cv)')
            ->getQuery()
            ->getSingleScalarResult();

        return $versionCount > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Такой версии не существует';
    }
}