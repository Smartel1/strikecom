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
            'roles' => ['MODERATOR'],
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
     * запрос на подписку
     */
    public function testSubscribe()
    {
        /** @var User $user */
        $user = entity(User::class)->make([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'roles' => ['MODERATOR'],
        ]);

        $this->actingAs($user)->post('/api/ru/subscribe',
            [
                'state' => 1,
                'fcm'   => 'aj2osdf832la93hp'
            ])
            ->assertStatus(200);
    }

    /**
     * запрос на подписку с некорректными данными
     */
    public function testSubscribeInvalid()
    {
        /** @var User $user */
        $user = entity(User::class)->make([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'roles' => ['MODERATOR'],
        ]);

        $this->actingAs($user)->post('/api/ru/subscribe',
            [
                'state' => 1,
            ])
            ->assertStatus(422);
    }

    /**
     * запрос на подписку неаутентифиц. пользователем
     */
    public function testSubscribeUnauth()
    {
        $this->post('/api/ru/subscribe',
            [
                'state' => 1,
                'fcm'   => 'aj2osdf832la93hp'
            ])
            ->assertStatus(401);
    }
}
