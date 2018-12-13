<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\CreatesApplication;
use Tests\TestCase;

class ConflictControllerTest extends TestCase
{
    use DatabaseTransactions;
    use CreatesApplication;

    /**
     * запрос на список конфликтов
     */
    public function testIndex ()
    {
        DB::table('conflicts')->delete();

        DB::table('conflicts')->insert([
            'id'            => 1,
            'title'         => 'Трудовой конфликт',
            'latitude'      => 54.5943,
            'longitude'     => 57.1670,
            'company_name'  => 'ПАО АМЗ',
            'date_from'     => 1544680093,
            'date_to'       => 1544690093,
            'conflict_reason_id'     => 2,
            'conflict_result_id'     => 3,
            'industry_id'            => 2,
            'region_id'              => 57
        ]);

        $this->get('/api/conflict')
            ->assertStatus(200);
    }

    /**
     * запрос на список конфликтов с флагом brief
     */
    public function testIndexBrief ()
    {
        $this->get('/api/conflict?brief=1')
            ->assertStatus(200);
    }

    /**
     * запрос на список конфликтов с неверным флагом brief
     */
    public function testIndexInvalid ()
    {
        $this->get('/api/conflict?brief=true')
            ->assertStatus(422);
    }

    /**
     * запрос одного конфликта
     */
    public function testView ()
    {
        DB::table('conflicts')->where('id',1)->delete();

        DB::table('conflicts')->insert([
            'id'            => 1,
            'title'         => 'Трудовой конфликт',
            'latitude'      => 54.5943,
            'longitude'     => 57.1670,
            'company_name'  => 'ПАО АМЗ',
            'date_from'     => 1544680093,
            'date_to'       => 1544690093,
            'conflict_reason_id'     => 2,
            'conflict_result_id'     => 3,
            'industry_id'            => 2,
            'region_id'              => 57
        ]);

        $this->get('/api/conflict/1')
            ->assertStatus(200);
    }

    /**
     * запрос несуществующего конфликта
     */
    public function testViewWrong ()
    {
        DB::table('conflicts')->where('id',1)->delete();

        $this->get('/api/conflict/1')
            ->assertStatus(404);
    }

    /**
     * запрос на создание конфликта
     */
    public function testStore ()
    {
        $this->post('/api/conflict', [
            'title'         => 'Трудовой конфликт',
            'latitude'      => 54.5943,
            'longitude'     => 57.1670,
            'company_name'  => 'ПАО АМЗ',
            'date_from'     => 1544690093,
            'date_to'       => 1545680093,
            'conflict_reason_id'     => 2,
            'conflict_result_id'     => 3,
            'industry_id'            => 2,
            'region_id'              => 57
        ])
            ->assertStatus(200);
    }

    /**
     * некорректный запрос на создание конфликта
     */
    public function testStoreInvalid ()
    {
        $this->post('/api/conflict', [])
            ->assertStatus(422);
    }

    /**
     * запрос на обновление конфликта
     */
    public function testUpdate ()
    {
        DB::table('conflicts')->where('id',1)->delete();

        DB::table('conflicts')->insert([
            'id'            => 1,
            'title'         => 'Трудовой конфликт',
            'latitude'      => 54.5943,
            'longitude'     => 57.1670,
            'company_name'  => 'ПАО АМЗ',
        ]);

        $this->put('/api/conflict/1', [
            'title'         => 'Трудовой конфликт',
            'latitude'      => 54.5944,
            'longitude'     => 57.1671,
            'company_name'  => 'ПАО АМЗ',
            'date_from'     => 1544680093,
            'date_to'       => 1544780093,
            'conflict_reason_id'     => 5,
            'conflict_result_id'     => 2,
            'industry_id'            => 1,
            'region_id'              => 54
        ])
            ->assertStatus(200);
    }

    /**
     * некорректный запрос на обновление конфликта
     */
    public function testUpdateInvalid ()
    {
        DB::table('conflicts')->where('id',1)->delete();

        DB::table('conflicts')->insert([
            'id'            => 1,
            'title'         => 'Трудовой конфликт',
            'latitude'      => 54.5943,
            'longitude'     => 57.1670,
            'company_name'  => 'ПАО АМЗ',
        ]);

        $this->put('/api/conflict/1', [
            'title'         => [],
            'latitude'      => [],
            'longitude'     => [],
            'company_name'  => [],
            'date_from'     => [],
            'date_to'       => 5,
            'conflict_reason_id'     => [],
            'conflict_result_id'     => [],
            'industry_id'            => [],
            'region_id'              => []
        ])
            ->assertStatus(422);
    }

    /**
     * запрос на обновление несуществующего конфликта
     */
    public function testUpdateWrong ()
    {
        DB::table('conflicts')->where('id',1)->delete();

        $this->put('/api/conflict/1', [
            'title'         => 'Трудовой конфликт',
            'latitude'      => 54.5944,
            'longitude'     => 57.1671,
            'company_name'  => 'ПАО АМЗ',
            'date_from'     => 1544680093,
            'date_to'       => 1544980093,
            'conflict_reason_id'     => 5,
            'conflict_result_id'     => 2,
            'industry_id'            => 1,
            'region_id'              => 54
        ])
            ->assertStatus(404);
    }

    /**
     * запрос на удаление конфликта
     */
    public function testDelete ()
    {
        DB::table('conflicts')->where('id',1)->delete();

        DB::table('conflicts')->insert([
            'id'            => 1,
            'title'         => 'Трудовой конфликт',
            'latitude'      => 54.5943,
            'longitude'     => 57.1670,
            'company_name'  => 'ПАО АМЗ',
        ]);

        $this->delete('/api/conflict/1')
            ->assertStatus(200);
    }

    /**
     * запрос на удаление несуществующего конфликта
     */
    public function testDeleteWrong ()
    {
        DB::table('conflicts')->where('id',1)->delete();

        $this->delete('/api/conflict/1')
            ->assertStatus(404);
    }
}
