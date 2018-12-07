<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\CreatesApplication;
use Tests\TestCase;

class ReferenceControllerTest extends TestCase
{
    use DatabaseTransactions;
    use CreatesApplication;

    public function testReferences()
    {
        DB::table('event_types')->where('id','>',3)->delete();
        DB::table('event_statuses')->where('id','>',3)->delete();
        DB::table('industries')->where('id','>',3)->delete();
        DB::table('regions')->where('id','>',3)->delete();
        DB::table('conflict_reasons')->where('id','>',3)->delete();
        DB::table('conflict_results')->where('id','>',3)->delete();

        //запрос на список конфликтов
        $response = $this->get('/api/references');

        $response->assertStatus(200);
    }


}
