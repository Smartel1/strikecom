<?php

namespace Tests\Feature;

use App\Entities\ClientVersion;
use App\Entities\User;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\CreatesApplication;
use Tests\TestCase;
use Tests\Traits\DoctrineTransactions;

class ClientVersionControllerTest extends TestCase
{
    use DoctrineTransactions;
    use CreatesApplication;

    /**
     * Удалить все версии из базы даннных
     */
    private function deleteAllVersionsFromDB()
    {
        EntityManager::createQueryBuilder()->from(ClientVersion::class,'c')->delete()->getQuery()->getResult();
    }
    /**
     * запрос на список новых версий
     */
    public function testIndex()
    {
        $this->deleteAllVersionsFromDB();

        entity(ClientVersion::class)->create([
            'version'   => '1.2.0',
            'client_id' => 'org.nrstudio.strikecom',
        ]);

        entity(ClientVersion::class)->create([
            'version'   => '1.2.1',
            'client_id' => 'org.nrstudio.strikecom',
        ]);

        $this->get('/api/ru/client-version?client_id=org.nrstudio.strikecom&current_version=1.2.0')
            ->assertStatus(200);
    }

    /**
     * некорректный запрос на список новых версий
     */
    public function testIndexInvalid()
    {
        $this->get('/api/ru/client-version?client_id=org.nrstudio.strikecom&current_version=1.2')
            ->assertStatus(422);
    }

    /**
     * запрос на создание версии
     */
    public function testStore()
    {
        $user = entity(User::class)->create([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'roles' => ['MODERATOR'],
        ]);

        $this->actingAs($user)->post('/api/ru/client-version', [
            'version'        => '1.2.1',
            'client_id'      => 'org.nrstudio.strikecom',
            'required'       => false,
            'description_ru' => 'Исправление ошибок свержения',
            'description_en' => 'Bugfixes',
            'description_es' => 'Smth',
        ])
            ->assertStatus(200);
    }

    /**
     * запрос на создание версии неаутентифиц. пользователем
     */
    public function testStoreUnauth()
    {
        $this->post('/api/ru/client-version', [
            'version'        => '1.2.1',
            'client_id'      => 'org.nrstudio.strikecom',
            'required'       => false,
            'description_ru' => 'Исправление ошибок свержения',
            'description_en' => 'Bugfixes',
            'description_es' => 'Smth',
        ])
            ->assertStatus(403);
    }

    /**
     * некорректный запрос на создание версии
     */
    public function testStoreInvalid()
    {
        $this->post('/api/ru/client-version', [
            'version'        => true,
            'client_id'      => 'org.nrstudio.strikecom',
            'required'       => 6,
            'description_ru' => false,
            'description_en' => false,
            'description_es' => [],
        ])
            ->assertStatus(422);
    }

    /**
     * запрос на удаление версии
     */
    public function testDelete()
    {
        $version = entity(ClientVersion::class)->create([
            'version'   => '1.2.0',
            'client_id' => 'org.nrstudio.strikecom',
        ]);

        $user = entity(User::class)->create([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'roles' => ['MODERATOR'],
        ]);

        $this->actingAs($user)->delete('/api/ru/client-version/' . $version->getId())
            ->assertStatus(200);
    }

    /**
     * запрос на удаление версии неаутентифиц. пользователем
     */
    public function testDeleteUnauth()
    {
        $version = entity(ClientVersion::class)->create([
            'version'   => '1.2.0',
            'client_id' => 'org.nrstudio.strikecom',
        ]);

        $this->delete('/api/ru/client-version/' . $version->getId())
            ->assertStatus(403);
    }

    /**
     * запрос на удаление несуществующей версии
     */
    public function testDeleteWrong()
    {
        $this->deleteAllVersionsFromDB();

        $this->delete('/api/ru/client-version/1')
            ->assertStatus(404);
    }
}
