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

    /**
     * запрос на список событий
     */
    public function testIndex ()
    {
        DB::table('events')->delete();

        DB::table('events')->insert([
            'id'                => 1,
            'title'             => 'Трудовой конфликт',
            'content'           => 'Такие вот дела',
            'date'              => '2018-01-15',
            'source_link'       => 'https://domain.ru/img.gif',
            'event_status_id'   => '1',
            'event_type_id'     => '3',
        ]);

        $this->get('/api/event')
            ->assertStatus(200);
    }

    /**
     * запрос одного события
     */
    public function testView ()
    {
        DB::table('events')->where('id',1)->delete();

        DB::table('events')->insert([
            'id'            => 1,
            'title'         => 'Трудовой конфликт',
            'content'       => 'Такие вот дела',
            'date'          => '2018-01-15',
        ]);

        $this->get('/api/event/1')
            ->assertStatus(200);
    }

    /**
     * запрос несуществующего события
     */
    public function testViewWrong ()
    {
        DB::table('events')->where('id',1)->delete();

        $this->get('/api/event/1')
            ->assertStatus(404);
    }

    /**
     * запрос на создание события
     */
    public function testStore ()
    {
        $this->post('/api/event', [
            'conflict_id'       => null,
            'title'             => 'Беда в городе',
            'content'           => 'Рабы кричат и гневятся',
            'date'              => '2018-10-01',
            'source_link'       => 'https://domain.ru/img.gif',
            'event_status_id'   => '1',
            'event_type_id'     => '3',
            'tags'              => ['нищета', 'голод'],
            'image_urls'        => ['images/ff.gif'],
        ])
            ->assertStatus(200);
    }

    /**
     * некорректный запрос на создание события
     */
    public function testStoreInvalid ()
    {
        $this->post('/api/event', [
            'conflict_id'       => -1,
            'title'             => [],
            'content'           => [],
            'date'              => 15,
            'source_link'       => [],
            'event_status_id'   => -1,
            'event_type_id'     => -1,
            'tags'              => '55',
            'image_urls'        => 'images/ff.gif',
        ])
            ->assertStatus(422);
    }

    /**
     * запрос на обновление события
     */
    public function testUpdate ()
    {
        DB::table('events')->where('id',1)->delete();

        DB::table('events')->insert([
            'id'            => 1,
            'title'         => 'Трудовой конфликт',
            'content'       => 'Такие вот дела',
            'date'          => '2018-01-15',
        ]);

        $this->put('/api/event/1', [
            'conflict_id'       => null,
            'title'             => 'Беда в мегаполисе',
            'content'           => 'Рабы беснуются и гневятся',
            'date'              => '2018-10-02',
            'source_link'       => 'https://domain.ru/img.png',
            'event_status_id'   => '2',
            'event_type_id'     => '5',
            'tags'              => ['голод'],
            'image_urls'        => ['images/ff.gif'],
        ])
            ->assertStatus(200);
    }

    /**
     * некорректый запрос на обновление события
     */
    public function testUpdateInvalid ()
    {
        DB::table('events')->where('id',1)->delete();

        DB::table('events')->insert([
            'id'            => 1,
            'title'         => 'Трудовой конфликт',
            'content'       => 'Такие вот дела',
            'date'          => '2018-01-15',
        ]);

        $this->put('/api/event/1', [
            'conflict_id'       => -1,
            'title'             => [],
            'content'           => [],
            'date'              => 15,
            'source_link'       => [],
            'event_status_id'   => -1,
            'event_type_id'     => -1,
            'tags'              => '55',
            'image_urls'        => 'images/ff.gif',
        ])
            ->assertStatus(422);
    }

    /**
     * запрос на обновление несуществующего события
     */
    public function testUpdateWrong ()
    {
        DB::table('events')->where('id',1)->delete();

        $this->put('/api/event/1', [
            'conflict_id'       => null,
            'title'             => 'Беда в мегаполисе',
            'content'           => 'Рабы беснуются и гневятся',
            'date'              => '2018-10-02',
            'source_link'       => 'https://domain.ru/img.png',
            'event_status_id'   => '2',
            'event_type_id'     => '5',
            'tags'              => ['голод'],
            'image_urls'        => ['images/ff.gif'],
        ])
            ->assertStatus(404);
    }

    /**
     * запрос на удаление события
     */
    public function testDelete ()
    {
        DB::table('events')->where('id',1)->delete();

        DB::table('events')->insert([
            'id'            => 1,
            'title'         => 'Трудовой конфликт',
            'content'       => 'Такие вот дела',
            'date'          => '2018-01-15',
        ]);

        $this->delete('/api/event/1')
            ->assertStatus(200);
    }

    /**
     * запрос на удаление несущесвующего события
     */
    public function testDeleteWrong ()
    {
        DB::table('events')->where('id',1)->delete();

        $this->delete('/api/event/1')
            ->assertStatus(404);
    }
}
