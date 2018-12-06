<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Tests\CreatesApplication;
use Tests\TestCase;

class ReferenceControllerTest extends TestCase
{
    use DatabaseTransactions;
    use CreatesApplication;

    public function testReferences()
    {
        Artisan::call('migrate:fresh');

        //запрос на список конфликтов
        $response = $this->get('/api/references');

        $response->assertStatus(200);
    }


}
