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
     * запрос данных для панели модератора
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
     * запрос данных для панели модератора немодератором
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

    /**
     * запрос комментариев с жалобами
     */
    public function testClaimComments ()
    {
        //todo создать комментарий
        $user = entity(User::class)->make([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'admin' => true,
        ]);

        $this->actingAs($user)->get('/api/ru/moderation/claim-comment/')
            ->assertStatus(200);
    }

    /**
     * запрос комментариев с жалобами
     */
    public function testClaimCommentsNonModerator ()
    {
        $user = entity(User::class)->make([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'admin' => false,
        ]);

        $this->actingAs($user)->get('/api/ru/moderation/claim-comment/')
            ->assertStatus(403);
    }
}
