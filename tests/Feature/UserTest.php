<?php

namespace Tests\Feature;

use App\Entities\User;
use Tests\CreatesApplication;
use Tests\TestCase;
use Tests\Traits\DoctrineTransactions;

class UserTest extends TestCase
{
    use DoctrineTransactions;
    use CreatesApplication;

    /**
     * запрос пользователя
     */
    public function test ()
    {
        $user = entity(User::class)->make([
            'name' => 'John Doe',
            'email' => 'john@doe.com',
        ]);

        $this->actingAs($user)->get('/api/ru/user')
            ->assertStatus(200);

    }

}
