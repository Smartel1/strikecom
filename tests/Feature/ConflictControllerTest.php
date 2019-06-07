<?php

namespace Tests\Feature;

use App\Entities\Conflict;
use App\Entities\User;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\CreatesApplication;
use Tests\TestCase;
use Tests\Traits\DoctrineTransactions;

class ConflictControllerTest extends TestCase
{
    use DoctrineTransactions;
    use CreatesApplication;

    /**
     * Удалить все конфликты из базы даннных
     */
    private function deleteAllConflictsFromDB()
    {
        EntityManager::createQueryBuilder()->from(Conflict::class,'c')->delete()->getQuery()->getResult();
    }

    /**
     * запрос на список конфликтов с флагом brief
     */
    public function testIndexBrief()
    {
        $this->get('/api/ru/conflict?brief=1')
            ->assertStatus(200);
    }

    /**
     * запрос на список конфликтов
     */
    public function testIndex()
    {
        $this->deleteAllConflictsFromDB();

        entity(Conflict::class)->create([
            'title_ru'           => 'Трудовой конфликт',
            'latitude'           => 54.5943,
            'longitude'          => 57.1670,
            'company_name'       => 'ПАО АМЗ',
            'date_from'          => 1544680093,
            'date_to'            => 1544690093,
            'conflict_reason_id' => 2,
            'conflict_result_id' => 3,
            'industry_id'        => 2,
            'region_id'          => 3
        ]);

        $this->get('/api/ru/conflict')
            ->assertStatus(200);
    }

    /**
     * запрос на список конфликтов с неверным флагом brief
     */
    public function testIndexInvalid()
    {
        $this->get('/api/ru/conflict?brief=true')
            ->assertStatus(422);
    }

    /**
     * запрос одного конфликта
     */
    public function testView()
    {
        $conflict = entity(Conflict::class)->create([
            'title_ru'           => 'Трудовой конфликт',
            'latitude'           => 54.5943,
            'longitude'          => 57.1670,
            'company_name'       => 'ПАО АМЗ',
            'date_from'          => 1544680093,
            'date_to'            => 1544690093,
            'conflict_reason_id' => 2,
            'conflict_result_id' => 3,
            'industry_id'        => 2,
            'region_id'          => 3
        ]);

        $this->get('/api/ru/conflict/' . $conflict->getId())
            ->assertStatus(200);
    }

    /**
     * запрос несуществующего конфликта
     */
    public function testViewWrong()
    {
        $this->deleteAllConflictsFromDB();

        $this->get('/api/ru/conflict/1')
            ->assertStatus(404);
    }

    /**
     * запрос на создание конфликта
     */
    public function testStore()
    {
        $user = entity(User::class)->create([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'admin' => true,
        ]);

        $this->actingAs($user)->post('/api/ru/conflict', [
            'title'              => 'Трудовой конфликт',
            'latitude'           => 54.5943,
            'longitude'          => 57.1670,
            'company_name'       => 'ПАО АМЗ',
            'date_from'          => 1544690093,
            'date_to'            => 1545680093,
            'conflict_reason_id' => 2,
            'conflict_result_id' => 3,
            'industry_id'        => 2,
            'region_id'          => 3
        ])
            ->assertStatus(200);
    }

    /**
     * запрос на создание конфликта неаутентифиц. пользователем
     */
    public function testStoreUnauth()
    {
        $this->post('/api/ru/conflict', [
            'title'              => 'Трудовой конфликт',
            'latitude'           => 54.5943,
            'longitude'          => 57.1670,
            'company_name'       => 'ПАО АМЗ',
            'date_from'          => 1544690093,
            'date_to'            => 1545680093,
            'conflict_reason_id' => 2,
            'conflict_result_id' => 3,
            'industry_id'        => 2,
            'region_id'          => 3
        ])
            ->assertStatus(403);
    }

    /**
     * некорректный запрос на создание конфликта
     */
    public function testStoreInvalid()
    {
        $this->post('/api/ru/conflict', [])
            ->assertStatus(422);
    }

    /**
     * запрос на обновление конфликта
     */
    public function testUpdate()
    {
        $user = entity(User::class)->create([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'admin' => true,
        ]);

        $conflict = entity(Conflict::class)->create([
            'title_ru'     => 'Трудовой конфликт',
            'latitude'     => 54.5943,
            'longitude'    => 57.1670,
            'company_name' => 'ПАО АМЗ',
        ]);

        $this->actingAs($user)->put('/api/ru/conflict/' . $conflict->getId(), [
            'title'              => 'Трудовой конфликт',
            'latitude'           => 54.5944,
            'longitude'          => 57.1671,
            'company_name'       => 'ПАО АМЗ',
            'date_from'          => 1544680093,
            'date_to'            => 1544780093,
            'conflict_reason_id' => 3,
            'conflict_result_id' => 2,
            'industry_id'        => 1,
            'region_id'          => 3
        ])
            ->assertStatus(200);
    }

    /**
     * запрос на обновление конфликта неаутентфиц. пользователем
     */
    public function testUpdateUnauth()
    {
        $conflict = entity(Conflict::class)->create([
            'title_ru'     => 'Трудовой конфликт',
            'latitude'     => 54.5943,
            'longitude'    => 57.1670,
            'company_name' => 'ПАО АМЗ',
        ]);

        $this->put('/api/ru/conflict/' . $conflict->getId(), [
            'title'              => 'Трудовой конфликт',
            'latitude'           => 54.5944,
            'longitude'          => 57.1671,
            'company_name'       => 'ПАО АМЗ',
            'date_from'          => 1544680093,
            'date_to'            => 1544780093,
            'conflict_reason_id' => 3,
            'conflict_result_id' => 2,
            'industry_id'        => 1,
            'region_id'          => 3
        ])
            ->assertStatus(403);
    }

    /**
     * некорректный запрос на обновление конфликта
     */
    public function testUpdateInvalid()
    {
        $conflict = entity(Conflict::class)->create([
            'title_ru'     => 'Трудовой конфликт',
            'latitude'     => 54.5943,
            'longitude'    => 57.1670,
            'company_name' => 'ПАО АМЗ',
        ]);

        $this->put('/api/ru/conflict/' . $conflict->getId(), [
            'title'              => [],
            'latitude'           => [],
            'longitude'          => [],
            'company_name'       => [],
            'date_from'          => [],
            'date_to'            => 5,
            'conflict_reason_id' => [],
            'conflict_result_id' => [],
            'industry_id'        => [],
            'region_id'          => []
        ])
            ->assertStatus(422);
    }

    /**
     * запрос на обновление несуществующего конфликта
     */
    public function testUpdateWrong()
    {
        $this->deleteAllConflictsFromDB();

        $this->put('/api/ru/conflict/1', [
            'title'              => 'Трудовой конфликт',
            'latitude'           => 54.5944,
            'longitude'          => 57.1671,
            'company_name'       => 'ПАО АМЗ',
            'date_from'          => 1544680093,
            'date_to'            => 1544980093,
            'conflict_reason_id' => 5,
            'conflict_result_id' => 2,
            'industry_id'        => 1,
            'region_id'          => 3
        ])
            ->assertStatus(404);
    }

    /**
     * запрос на удаление конфликта
     */
    public function testDelete()
    {
        $user = entity(User::class)->create([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'admin' => true,
        ]);

        $conflict = entity(Conflict::class)->create([
            'title_ru'     => 'Трудовой конфликт',
            'latitude'     => 54.5943,
            'longitude'    => 57.1670,
            'company_name' => 'ПАО АМЗ',
        ]);

        $this->actingAs($user)->delete('/api/ru/conflict/' . $conflict->getId())
            ->assertStatus(200);
    }

    /**
     * запрос на удаление конфликта неаутентифиц. пользователем
     */
    public function testDeleteUnauth()
    {
        $conflict = entity(Conflict::class)->create([
            'title_ru'     => 'Трудовой конфликт',
            'latitude'     => 54.5943,
            'longitude'    => 57.1670,
            'company_name' => 'ПАО АМЗ',
        ]);

        $this->delete('/api/ru/conflict/' . $conflict->getId())
            ->assertStatus(403);
    }

    /**
     * запрос на удаление несуществующего конфликта
     */
    public function testDeleteWrong()
    {
        $this->deleteAllConflictsFromDB();

        $this->delete('/api/ru/conflict/1')
            ->assertStatus(404);
    }
}
