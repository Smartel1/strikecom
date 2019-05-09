<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\CreatesApplication;
use Tests\TestCase;

class ClientVersionControllerTest extends TestCase
{
    use DatabaseTransactions;
    use CreatesApplication;

    /**
     * запрос на список новых версий
     */
    public function testIndex()
    {
        DB::table('client_versions')->delete();

        DB::table('client_versions')->insert([
            'id'             => 1,
            'version'        => '1.2.0',
            'client_id'      => 'org.nrstudio.strikecom',
            'required'       => true,
            'description_ru' => 'Добавлена возможность свергать деспотов',
            'description_en' => 'Features added',
            'description_es' => 'Smth',
        ]);

        DB::table('client_versions')->insert([
            'id'             => 2,
            'version'        => '1.2.1',
            'client_id'      => 'org.nrstudio.strikecom',
            'required'       => false,
            'description_ru' => 'Исправление ошибок свержения',
            'description_en' => 'Bugfixes',
            'description_es' => 'Smth',
        ]);

        $this->get('/api/ru/client-version?client_id=org.nrstudio.strikecom&current_version=1.2.0')
            ->assertStatus(200);
    }

    /**
     * некорректный запрос на список новых версий
     */
    public function testIndexInvalid()
    {
        DB::table('client_versions')->delete();

        DB::table('client_versions')->insert([
            'id'             => 1,
            'version'        => '1.2.0',
            'client_id'      => 'org.nrstudio.strikecom',
            'required'       => true,
            'description_ru' => 'Добавлена возможность свергать деспотов',
            'description_en' => 'Features added',
            'description_es' => 'Smth',
        ]);

        DB::table('client_versions')->insert([
            'id'             => 2,
            'version'        => '1.2.1',
            'client_id'      => 'org.nrstudio.strikecom',
            'required'       => false,
            'description_ru' => 'Исправление ошибок свержения',
            'description_en' => 'Bugfixes',
            'description_es' => 'Smth',
        ]);

        $this->get('/api/ru/client-version?client_id=org.nrstudio.strikecom&current_version=1.2')
            ->assertStatus(422);
    }

    /**
     * запрос на создание версии
     */
    public function testStore()
    {
        $this->post('/api/ru/client-version', [
            'version'        => '1.2.1',
            'client_id'      => 'org.nrstudio.strikecom',
            'required'       => false,
            'description_ru' => 'Исправление ошибок свержения',
            'description_en' => 'Bugfixes',
            'description_es' => 'Smth',
        ])
            ->assertStatus(201);
    }

    /**
     * некорректный запрос на создание версии
     */
    public function testStoreInvalid()
    {
        $this->post('/api/ru/client-version', [
            'version'        => true,
            'client_id'      => 'org.nrstudio.strikecom',
            'required'       => 6,
            'description_ru' => false,
            'description_en' => false,
            'description_es' => [],
        ])
            ->assertStatus(422);
    }

    /**
     * запрос на удаление версии
     */
    public function testDelete()
    {
        DB::table('client_versions')->where('id', 1)->delete();

        DB::table('client_versions')->insert([
            'id'             => 1,
            'version'        => '1.2.0',
            'client_id'      => 'org.nrstudio.strikecom',
            'required'       => true,
            'description_ru' => 'Добавлена возможность свергать деспотов',
            'description_en' => 'Features added',
            'description_es' => 'Smth',
        ]);

        $this->delete('/api/ru/client-version/1')
            ->assertStatus(200);
    }

    /**
     * запрос на удаление несущесвующей версии
     */
    public function testDeleteWrong()
    {
        DB::table('client_versions')->where('id', 1)->delete();

        $this->delete('/api/ru/client-version/1')
            ->assertStatus(404);
    }
}
