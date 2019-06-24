<?php

namespace Tests\Feature;

use App\Entities\References\Country;
use App\Entities\References\Locality;
use App\Entities\References\Region;
use App\Entities\User;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\CreatesApplication;
use Tests\TestCase;
use Tests\Traits\DoctrineTransactions;

class LocalityControllerTest extends TestCase
{
    use DoctrineTransactions;
    use CreatesApplication;

    public function testIndex()
    {
        EntityManager::createQuery('DELETE FROM App\Entities\References\Country c')->execute();
        EntityManager::createQuery('DELETE FROM App\Entities\References\Region r')->execute();
        EntityManager::createQuery('DELETE FROM App\Entities\References\Locality l')->execute();
        $country = new Country;
        $country->setNameRu('Россия');

        $region = new Region;
        $region->setName('Выдумская область');
        $region->setCountry($country);

        $locality = new Locality();
        $locality->setName('деревня Выдумка');
        $locality->setRegion($region);

        EntityManager::persist($country);
        EntityManager::persist($region);
        EntityManager::persist($locality);
        EntityManager::flush();

        $this->get('/api/ru/locality?name=выду')->assertStatus(200);
        $this->get('/api/ru/locality?name=a')->assertStatus(422);
    }

    public function testStore()
    {
        $country = new Country;
        $country->setNameRu('Россия');

        $region = new Region;
        $region->setName('Выдумская область');
        $region->setCountry($country);

        EntityManager::persist($country);
        EntityManager::persist($region);
        EntityManager::flush();

        $user = entity(User::class)->create([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'roles' => ['MODERATOR'],
        ]);

        $this->post('/api/ru/locality', [
            'name'      => 'с. Малые Забастовки',
            'region_id' => $region->getId(),
        ])->assertStatus(403);

        $this->actingAs($user)->post('/api/ru/locality', [
            'name'      => 'с. Малые Забастовки',
            'region_id' => $region->getId(),
        ])->assertStatus(200);

        $this->actingAs($user)->post('/api/ru/locality', [])
            ->assertStatus(422);
    }
}
