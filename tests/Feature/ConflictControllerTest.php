<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Tests\CreatesApplication;
use Tests\TestCase;

class ConflictControllerTest extends TestCase
{
    use DatabaseTransactions;
    use CreatesApplication;

    public function testConflicts()
    {
        Artisan::call('migrate:fresh');

        $response = $this->post('/api/conflict', [
            'title'         => 'Трудовой конфликт',
            'latitude'      => 54.5943,
            'longitude'     => 57.1670,
            'company_name'  => 'ПАО АМЗ',
            'date_from'     => '2019-01-15',
            'date_to'       => '2019-02-05',
            'conflict_reason_id'     => 2,
            'conflict_result_id'     => 3,
            'industry_id'            => 2,
            'region_id'              => 57
        ]);

        $response->assertStatus(200);

        //запрос на список конфликтов с флагом brief
        $response = $this->get('/api/conflict?brief=1');

        $response->assertStatus(200);

        //запрос на список конфликтов
        $response = $this->get('/api/conflict');

        $response->assertStatus(200);

        //запрос одного конфликта
        $response = $this->get('/api/conflict/1');

        $response->assertStatus(200);

        //запрос несуществующего конфликта
        $response = $this->get('/api/conflict/2');

        $response->assertStatus(404);

        //запрос на обновление конфликта
        $response = $this->put('/api/conflict/1', [
            'title'         => 'Трудовой конфликт',
            'latitude'      => 54.5944,
            'longitude'     => 57.1671,
            'company_name'  => 'ПАО АМЗ',
            'date_from'     => '2019-01-14',
            'date_to'       => '2019-02-04',
            'conflict_reason_id'     => 5,
            'conflict_result_id'     => 2,
            'industry_id'            => 1,
            'region_id'              => 54
        ]);

        $response->assertStatus(200);

        //запрос на обновление несуществующего конфликта
        $response = $this->put('/api/conflict/2', [
            'title'         => 'Трудовой конфликт',
            'latitude'      => 54.5944,
            'longitude'     => 57.1671,
            'company_name'  => 'ПАО АМЗ',
            'date_from'     => '2019-01-14',
            'date_to'       => '2019-02-04',
            'conflict_reason_id'     => 5,
            'conflict_result_id'     => 2,
            'industry_id'            => 1,
            'region_id'              => 54
        ]);

        $response->assertStatus(404);

        //запрос на удаление конфликта
        $response = $this->delete('/api/conflict/1');

        $response->assertStatus(200);

        //запрос на удаление несуществующего конфликта
        $response = $this->delete('/api/conflict/1');

        $response->assertStatus(404);
    }


}
