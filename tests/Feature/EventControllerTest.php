<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\CreatesApplication;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use DatabaseTransactions;
    use CreatesApplication;

    private function clearConflictsAndAddOne()
    {
        DB::table('conflicts')->delete();

        DB::table('conflicts')->insert([
            'id'                 => 1,
            'title_ru'           => 'Острый конфликт',
            'latitude'           => 1351315135.45,
            'longitude'          => 1256413515.45,
            'company_name'       => 'ЗАО ПАО',
            'conflict_reason_id' => 1,
            'conflict_result_id' => 3,
            'industry_id'        => 3,
            'region_id'          => 5,
            'date_from'          => 1544680093,
            'date_to'            => 1544690093,
        ]);
    }

    /**
     * запрос на список событий
     */
    public function testIndex()
    {
        $this->clearConflictsAndAddOne();

        DB::table('events')->insert([
            'id'              => 1,
            'conflict_id'     => 1,
            'title_ru'        => 'Трудовой конфликт',
            'content_ru'      => 'Такие вот дела',
            'date'            => 1544680093,
            'source_link'     => 'https://domain.ru/img.gif',
            'event_status_id' => '1',
            'event_type_id'   => '3',
        ]);

        $this->get('/api/ru/event')
            ->assertStatus(200);
    }

    /**
     * запрос одного события
     */
    public function testView()
    {
        $this->clearConflictsAndAddOne();

        DB::table('events')->insert([
            'id'          => 1,
            'conflict_id' => 1,
            'title_ru'    => 'Трудовой конфликт',
            'content_ru'  => 'Такие вот дела',
            'date'        => 1544680093,
        ]);

        $this->get('/api/ru/event/1')
            ->assertStatus(200);
    }

    /**
     * запрос несуществующего события
     */
    public function testViewWrong()
    {
        $this->clearConflictsAndAddOne();

        $this->get('/api/ru/event/1')
            ->assertStatus(404);
    }

    /**
     * запрос на создание события
     */
    public function testStore()
    {
        $this->clearConflictsAndAddOne();

        $this->post('/api/ru/event', [
            'conflict_id'     => '1',
            'title'           => 'Беда в городе',
            'content'         => 'Рабы кричат и гневятся',
            'date'            => 1544680093,
            'source_link'     => 'https://domain.ru/img.gif',
            'event_status_id' => '1',
            'event_type_id'   => '3',
            'tags'            => ['нищета', 'голод'],
            'photo_urls'      => ['images/ff.gif'],
            'videos'          => [
                ['url' => 'http://videos.ru/1', 'video_type_id' => 1, 'preview_url' => 'http://a']
            ],
        ])
            ->assertStatus(201);
    }

    /**
     * некорректный запрос на создание события
     */
    public function testStoreInvalid()
    {
        $this->clearConflictsAndAddOne();

        $this->post('/api/ru/event', [
            'conflict_id'     => -1,
            'title'           => [],
            'content'         => [],
            'date'            => 15,
            'source_link'     => [],
            'event_status_id' => -1,
            'event_type_id'   => -1,
            'tags'            => '55',
            'photo_urls'      => 'images/ff.gif',
            'videos'          => [[]],
        ])
            ->assertStatus(422);
    }

    /**
     * запрос на обновление события
     */
    public function testUpdate()
    {
        $this->clearConflictsAndAddOne();

        DB::table('events')->insert([
            'id'          => 1,
            'conflict_id' => 1,
            'title_ru'    => 'Трудовой конфликт',
            'content_ru'  => 'Такие вот дела',
            'date'        => 1544680093,
        ]);

        $this->put('/api/ru/event/1', [
            'title'           => 'Беда в мегаполисе',
            'content'         => 'Рабы беснуются и гневятся',
            'date'            => 1544690093,
            'source_link'     => 'https://domain.ru/img.png',
            'event_status_id' => '2',
            'event_type_id'   => '5',
            'tags'            => ['голод'],
            'photo_urls'      => ['images/ff.gif'],
            'videos'          => [
                ['url' => 'http://videos.ru/1', 'video_type_id' => 1, 'preview_url' => 'http://a']
            ],
        ])
            ->assertStatus(200);
    }

    /**
     * некорректый запрос на обновление события
     */
    public function testUpdateInvalid()
    {
        $this->clearConflictsAndAddOne();

        DB::table('events')->insert([
            'id'          => 1,
            'conflict_id' => 1,
            'title_ru'    => 'Трудовой конфликт',
            'content_ru'  => 'Такие вот дела',
            'date'        => 1544680093,
        ]);

        $this->put('/api/ru/event/1', [
            'title'           => [],
            'content'         => [],
            'date'            => 15,
            'source_link'     => [],
            'event_status_id' => -1,
            'event_type_id'   => -1,
            'tags'            => '55',
            'photo_urls'      => 'images/ff.gif',
            'videos'          => [[]],
        ])
            ->assertStatus(422);
    }

    /**
     * запрос на обновление несуществующего события
     */
    public function testUpdateWrong()
    {
        $this->clearConflictsAndAddOne();

        $this->put('/api/ru/event/1', [
            'title'           => 'Беда в мегаполисе',
            'content'         => 'Рабы беснуются и гневятся',
            'date'            => '2018-10-02',
            'source_link'     => 'https://domain.ru/img.png',
            'event_status_id' => '2',
            'event_type_id'   => '5',
            'tags'            => ['голод'],
            'photo_urls'      => ['images/ff.gif'],
            'videos'          => [
                ['url' => 'http://videos.ru/1', 'video_type_id' => 1, 'preview_url' => 'http://a']
            ],
        ])
            ->assertStatus(404);
    }

    /**
     * запрос на удаление события
     */
    public function testDelete()
    {
        $this->clearConflictsAndAddOne();

        DB::table('events')->insert([
            'id'          => 1,
            'conflict_id' => 1,
            'title_ru'    => 'Трудовой конфликт',
            'content_ru'  => 'Такие вот дела',
            'date'        => 1544680093,
        ]);

        $this->delete('/api/ru/event/1')
            ->assertStatus(200);
    }

    /**
     * запрос на удаление несущесвующего события
     */
    public function testDeleteWrong()
    {
        $this->clearConflictsAndAddOne();

        $this->delete('/api/ru/event/1')
            ->assertStatus(404);
    }
}
