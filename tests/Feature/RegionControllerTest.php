<?php

namespace Tests\Feature;

use App\Entities\References\Country;
use App\Entities\References\Region;
use App\Entities\User;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\CreatesApplication;
use Tests\TestCase;
use Tests\Traits\DoctrineTransactions;

class RegionControllerTest extends TestCase
{
    use DoctrineTransactions;
    use CreatesApplication;

    public function testIndex()
    {
        EntityManager::createQuery('DELETE FROM App\Entities\References\Country c')->execute();
        EntityManager::createQuery('DELETE FROM App\Entities\References\Region r')->execute();
        $country = new Country;
        $country->setNameRu('Россия');

        $region = new Region;
        $region->setName('Выдумская область');
        $region->setCountry($country);

        EntityManager::persist($country);
        EntityManager::persist($region);
        EntityManager::flush();

        $this->get('/api/ru/region?name=выду')->assertStatus(200);
        $this->get('/api/ru/region?name=a')->assertStatus(422);
    }

    public function testStore()
    {
        $country = new Country;
        $country->setNameRu('Россия');

        EntityManager::persist($country);
        EntityManager::flush();

        $user = entity(User::class)->create([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'roles' => ['MODERATOR'],
        ]);

        $this->post('/api/ru/region', [
            'name'      => 'Стачковая область',
            'country_id' => $country->getId(),
        ])->assertStatus(403);

        $this->actingAs($user)->post('/api/ru/region', [
            'name'      => 'Стачковая область',
            'country_id' => $country->getId(),
        ])->assertStatus(200);

        $this->actingAs($user)->post('/api/ru/region', [])
            ->assertStatus(422);
    }
}
