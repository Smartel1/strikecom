<?php

namespace Tests\Feature;

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
        $this->get('/api/ru/user')
            ->assertStatus(200);
    }

}
