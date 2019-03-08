<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\CreatesApplication;
use Tests\TestCase;

class UserTest extends TestCase
{
    use CreatesApplication;

    /**
     * запрос пользователя
     */
    public function test ()
    {
        $this->get('/api/ru/user')
            ->assertStatus(200);
    }

}
