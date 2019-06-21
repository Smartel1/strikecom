<?php

namespace Tests\Feature;

use App\Entities\References\Country;
use App\Entities\References\Locality;
use App\Entities\References\Region;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\CreatesApplication;
use Tests\TestCase;
use Tests\Traits\DoctrineTransactions;

class ReferenceControllerTest extends TestCase
{
    use DoctrineTransactions;
    use CreatesApplication;

    public function testReferences()
    {
        EntityManager::createQuery('DELETE FROM App\Entities\Event t')->getResult();
        EntityManager::createQuery('DELETE FROM App\Entities\Conflict t')->getResult();
        EntityManager::createQuery('DELETE FROM App\Entities\References\ClaimType t WHERE t.id > 3')->getResult();
        EntityManager::createQuery('DELETE FROM App\Entities\References\EventType t WHERE t.id > 3')->getResult();
        EntityManager::createQuery('DELETE FROM App\Entities\References\EventStatus t WHERE t.id > 3')->getResult();
        EntityManager::createQuery('DELETE FROM App\Entities\References\Industry t WHERE t.id > 3')->getResult();
        EntityManager::createQuery('DELETE FROM App\Entities\References\Region t WHERE t.id > 3')->getResult();
        EntityManager::createQuery('DELETE FROM App\Entities\References\ConflictReason t WHERE t.id > 3')->getResult();
        EntityManager::createQuery('DELETE FROM App\Entities\References\ConflictResult t WHERE t.id > 3')->getResult();

        //запрос на список конфликтов
        $response = $this->get('/api/ru/reference');

        $response->assertStatus(200);
    }

    public function testCheckSum()
    {
        $this->get('/api/ru/reference-checksum')->assertStatus(200);
    }

    public function testCountry()
    {
        EntityManager::createQuery('DELETE FROM App\Entities\References\Country c')->execute();
        $country = new Country;
        $country->setNameRu('Россия');
        $country->setNameEn('Russia');
        $country->setNameEs('Rusia');
        EntityManager::persist($country);
        EntityManager::flush();

        $this->get('/api/ru/country-search?name=Росс')->assertStatus(200);
        $this->get('/api/ru/country-search?name=a')->assertStatus(422);
    }

    public function testRegion()
    {
        EntityManager::createQuery('DELETE FROM App\Entities\References\Country c')->execute();
        EntityManager::createQuery('DELETE FROM App\Entities\References\Region r')->execute();
        $country = new Country;
        $country->setNameRu('Россия');

        $region = new Region;
        $region->setNameRu('Выдумская область');
        $region->setCountry($country);

        EntityManager::persist($country);
        EntityManager::persist($region);
        EntityManager::flush();

        $this->get('/api/ru/region-search?name=выду')->assertStatus(200);
        $this->get('/api/ru/region-search?name=a')->assertStatus(422);
    }

    public function testLocality()
    {
        EntityManager::createQuery('DELETE FROM App\Entities\References\Country c')->execute();
        EntityManager::createQuery('DELETE FROM App\Entities\References\Region r')->execute();
        EntityManager::createQuery('DELETE FROM App\Entities\References\Locality l')->execute();
        $country = new Country;
        $country->setNameRu('Россия');

        $region = new Region;
        $region->setNameRu('Выдумская область');
        $region->setCountry($country);

        $locality = new Locality();
        $locality->setNameRu('деревня Выдумка');
        $locality->setRegion($region);

        EntityManager::persist($country);
        EntityManager::persist($region);
        EntityManager::persist($locality);
        EntityManager::flush();

        $this->get('/api/ru/locality-search?name=выду')->assertStatus(200);
        $this->get('/api/ru/locality-search?name=a')->assertStatus(422);
    }
}
