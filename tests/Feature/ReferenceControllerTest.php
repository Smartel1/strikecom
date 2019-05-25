<?php

namespace Tests\Feature;

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
}
