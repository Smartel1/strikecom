<?php

namespace Tests\Feature;

use App\Entities\References\Country;
use App\Entities\User;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\CreatesApplication;
use Tests\TestCase;
use Tests\Traits\DoctrineTransactions;

class CountryControllerTest extends TestCase
{
    use DoctrineTransactions;
    use CreatesApplication;

    public function testIndex()
    {
        EntityManager::createQuery('DELETE FROM App\Entities\References\Country c')->execute();
        $country = new Country;
        $country->setNameRu('Россия');
        $country->setNameEn('Russia');
        $country->setNameEs('Rusia');
        EntityManager::persist($country);
        EntityManager::flush();

        $this->get('/api/ru/country?name=Росс')->assertStatus(200);
        $this->get('/api/ru/country?name=a')->assertStatus(422);
    }

    public function testStore()
    {
        $user = entity(User::class)->create([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'roles' => ['MODERATOR'],
        ]);

        $this->post('/api/ru/country', [
            'name_ru'      => 'Роисся',
            'name_en'      => 'Roissia',
            'name_es'      => 'Raisa',
        ])->assertStatus(403);

        $this->actingAs($user)->post('/api/ru/country', [
            'name_ru'      => 'Роисся',
            'name_en'      => 'Roissia',
            'name_es'      => 'Raisa',
        ])->assertStatus(200);

        $this->actingAs($user)->post('/api/ru/country', [])
            ->assertStatus(422);
    }
}
