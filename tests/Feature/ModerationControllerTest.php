<?php

namespace Tests\Feature;

use App\Entities\User;
use Tests\CreatesApplication;
use Tests\TestCase;
use Tests\Traits\DoctrineTransactions;

class ModerationControllerTest extends TestCase
{
    use DoctrineTransactions;
    use CreatesApplication;

    /**
     * запрос пользователя
     */
    public function testDashboard ()
    {
        $user = entity(User::class)->make([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'admin' => true,
        ]);

        $this->actingAs($user)->get('/api/ru/moderation/dashboard/')
            ->assertStatus(200);
    }

    /**
     * запрос пользователя
     */
    public function testDashboardNonModerator ()
    {
        $user = entity(User::class)->make([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'admin' => false,
        ]);

        $this->actingAs($user)->get('/api/ru/moderation/dashboard/')
            ->assertStatus(403);
    }

}
