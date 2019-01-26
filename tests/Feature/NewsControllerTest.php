<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\CreatesApplication;
use Tests\TestCase;

class NewsControllerTest extends TestCase
{
    use DatabaseTransactions;
    use CreatesApplication;

    /**
     * запрос на список новостей
     */
    public function testIndex ()
    {
        DB::table('news')->delete();

        DB::table('news')->insert([
            'id'                => 1,
            'title'             => 'Новости соседнего села',
            'content'           => 'Такие вот дела',
            'date'              => 1544680093,
            'source_link'       => 'https://domain.ru/img.gif',
            'event_status_id'   => '1',
            'event_type_id'     => '3',
        ]);

        $this->get('/api/news')
            ->assertStatus(200);
    }

    /**
     * запрос одной новости
     */
    public function testView ()
    {
        DB::table('news')->where('id',1)->delete();

        DB::table('news')->insert([
            'id'            => 1,
            'title'         => 'Трудовой конфликт',
            'content'       => 'Такие вот дела',
            'date'          => 1544680093,
        ]);

        $this->get('/api/news/1')
            ->assertStatus(200);
    }

    /**
     * запрос несуществующей новости
     */
    public function testViewWrong ()
    {
        DB::table('news')->where('id',1)->delete();

        $this->get('/api/news/1')
            ->assertStatus(404);
    }

    /**
     * запрос на создание новости
     */
    public function testStore ()
    {
        $this->post('/api/news', [
            'title'             => 'Беда в городе',
            'content'           => 'Рабы кричат и гневятся',
            'date'              => 1544680093,
            'source_link'       => 'https://domain.ru/img.gif',
            'event_status_id'   => '1',
            'event_type_id'     => '3',
            'tags'              => ['нищета', 'голод'],
            'image_urls'        => ['images/ff.gif'],
        ])
            ->assertStatus(200);
    }

    /**
     * некорректный запрос на создание новости
     */
    public function testStoreInvalid ()
    {
        $this->post('/api/news', [
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
     * запрос на обновление новости
     */
    public function testUpdate ()
    {
        DB::table('news')->where('id',1)->delete();

        DB::table('news')->insert([
            'id'            => 1,
            'title'         => 'Новости села',
            'content'       => 'Такие вот дела',
            'date'          => 1544680093,
        ]);

        $this->put('/api/news/1', [
            'title'             => 'Беда в мегаполисе',
            'content'           => 'Рабы беснуются и гневятся',
            'date'              => 1544690093,
            'source_link'       => 'https://domain.ru/img.png',
            'event_status_id'   => '2',
            'event_type_id'     => '5',
            'tags'              => ['голод'],
            'image_urls'        => ['images/ff.gif'],
        ])
            ->assertStatus(200);
    }

    /**
     * некорректый запрос на обновление новости
     */
    public function testUpdateInvalid ()
    {
        DB::table('news')->where('id',1)->delete();

        DB::table('news')->insert([
            'id'            => 1,
            'title'         => 'Рабы захотели деньжат',
            'content'       => 'Богатые дяди визжат',
            'date'          => 1544680093,
        ]);

        $this->put('/api/news/1', [
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
     * запрос на обновление несуществующей новости
     */
    public function testUpdateWrong ()
    {
        DB::table('news')->where('id',1)->delete();

        $this->put('/api/news/1', [
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
     * запрос на удаление новости
     */
    public function testDelete ()
    {
        DB::table('news')->where('id',1)->delete();

        DB::table('news')->insert([
            'id'            => 1,
            'title'         => 'Трудовой заговор',
            'content'       => 'Рабочие тайком сговорились работать на совесть',
            'date'          => 1544680093,
        ]);

        $this->delete('/api/news/1')
            ->assertStatus(200);
    }

    /**
     * запрос на удаление несущесвующей новости
     */
    public function testDeleteWrong ()
    {
        DB::table('news')->where('id',1)->delete();

        $this->delete('/api/news/1')
            ->assertStatus(404);
    }
}
