<?php

namespace Tests\Feature;

use App\Entities\Event;
use App\Entities\News;
use App\Entities\User;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\CreatesApplication;
use Tests\TestCase;
use Tests\Traits\DoctrineTransactions;

class UserControllerTest extends TestCase
{
    use DoctrineTransactions;
    use CreatesApplication;

    /**
     * запрос пользователя
     */
    public function testShow()
    {
        /** @var User $user */
        $user = entity(User::class)->make([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'roles' => [User::$ROLE_MODERATOR],
        ]);

        $user->getFavouriteEvents()->add(
            entity(Event::class)->create()
        );

        $user->getFavouriteNews()->add(
            entity(News::class)->create()
        );

        EntityManager::persist($user);
        EntityManager::flush();

        $this->actingAs($user)->get('/api/ru/user')
            ->assertStatus(200);
    }

    /**
     * запрос пользователя без аутентификации
     */
    public function testShowUnauth()
    {
        $this->get('/api/ru/user')
            ->assertStatus(401);
    }

    /**
     * запрос на изменение
     */
    public function testUpdate()
    {
        /** @var User $user */
        $user = entity(User::class)->create([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'roles' => [User::$ROLE_MODERATOR],
        ]);

        $this->actingAs($user)->put('/api/ru/user/' . $user->getId(),
            [
                'fcm'   => 'aj2osdf832la93hp',
                'roles' => [User::$ROLE_ADMIN]
            ])
            ->assertStatus(200);
    }

    /**
     * запрос на изменение с некорректными данными
     */
    public function testUpdateInvalid()
    {
        /** @var User $user */
        $user = entity(User::class)->create([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'roles' => [User::$ROLE_MODERATOR],
        ]);

        $this->actingAs($user)->put('/api/ru/user/' . $user->getId(),
            [
                'fcm' => ['foo']
            ])
            ->assertStatus(422);
    }

    /**
     * запрос на изменение неаутентифиц. пользователем
     */
    public function testUpdateUnauth()
    {
        $user = entity(User::class)->create([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'roles' => [User::$ROLE_MODERATOR],
        ]);

        $this->put('/api/ru/user/' . $user->getId(),
            [
                'fcm'   => 'aj2osdf832la93hp'
            ])
            ->assertStatus(403);
    }

    /**
     * запрос на изменение ролей немодератором
     */
    public function testUpdateRoles()
    {
        $user = entity(User::class)->create([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'roles' => [],
        ]);

        $this->put('/api/ru/user/' . $user->getId(),
            [
                'fcm'   => 'aj2osdf832la93hp',
                'roles'   => [User::$ROLE_ADMIN]
            ])
            ->assertStatus(403);
    }
}
