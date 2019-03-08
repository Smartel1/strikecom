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
            'title_ru'             => 'Новости соседнего села',
            'content_ru'           => 'Такие вот дела',
            'date'              => 1544680093,
            'source_link'       => 'https://domain.ru/img.gif',
        ]);

        $this->get('/api/ru/news')
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
            'title_ru'         => 'Трудовой конфликт',
            'content_ru'       => 'Такие вот дела',
            'date'          => 1544680093,
        ]);

        $this->get('/api/ru/news/1')
            ->assertStatus(200);
    }

    /**
     * запрос несуществующей новости
     */
    public function testViewWrong ()
    {
        DB::table('news')->where('id',1)->delete();

        $this->get('/api/ru/news/1')
            ->assertStatus(404);
    }

    /**
     * запрос на создание новости
     */
    public function testStore ()
    {
        $this->post('/api/ru/news', [
            'title'             => 'Беда в городе',
            'content'           => 'Рабы кричат и гневятся',
            'date'              => 1544680093,
            'source_link'       => 'https://domain.ru/img.gif',
            'tags'              => ['нищета', 'голод'],
            'photo_urls'        => ['images/ff.gif'],
            'videos'            => [
                ['url'=> 'http://videos.ru/1', 'video_type_id' => 1, 'preview_url'=> 'http://a']
            ],
        ])
            ->assertStatus(201);
    }

    /**
     * некорректный запрос на создание новости
     */
    public function testStoreInvalid ()
    {
        $this->post('/api/ru/news', [
            'title'             => [],
            'content'           => [],
            'date'              => 15,
            'source_link'       => [],
            'tags'              => '55',
            'photo_urls'        => 'images/ff.gif',
            'videos'            => [[]],
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
            'title_ru'         => 'Новости села',
            'content_ru'       => 'Такие вот дела',
            'date'          => 1544680093,
        ]);

        $this->put('/api/ru/news/1', [
            'title'             => 'Беда в мегаполисе',
            'content'           => 'Рабы беснуются и гневятся',
            'date'              => 1544690093,
            'source_link'       => 'https://domain.ru/img.png',
            'tags'              => ['голод'],
            'photo_urls'        => ['images/ff.gif'],
            'videos'            => [
                ['url'=> 'http://videos.ru/1', 'video_type_id' => 1, 'preview_url'=> 'http://a']
            ],
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
            'title_ru'         => 'Рабы захотели деньжат',
            'content_ru'       => 'Богатые дяди визжат',
            'date'          => 1544680093,
        ]);

        $this->put('/api/ru/news/1', [
            'title'             => [],
            'content'           => [],
            'date'              => 15,
            'source_link'       => [],
            'tags'              => '55',
            'photo_urls'        => 'images/ff.gif',
            'videos'            => [],
        ])
            ->assertStatus(422);
    }

    /**
     * запрос на обновление несуществующей новости
     */
    public function testUpdateWrong ()
    {
        DB::table('news')->where('id',1)->delete();

        $this->put('/api/ru/news/1', [
            'title'             => 'Беда в мегаполисе',
            'content'           => 'Рабы беснуются и гневятся',
            'date'              => '2018-10-02',
            'source_link'       => 'https://domain.ru/img.png',
            'tags'              => ['голод'],
            'photo_urls'        => ['images/ff.gif'],
            'videos'            => [
                ['url'=> 'http://videos.ru/1', 'video_type_id' => 1, 'preview_url'=> 'http://a']
            ],
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
            'title_ru'         => 'Трудовой заговор',
            'content_ru'       => 'Рабочие тайком сговорились работать на совесть',
            'date'          => 1544680093,
        ]);

        $this->delete('/api/ru/news/1')
            ->assertStatus(200);
    }

    /**
     * запрос на удаление несущесвующей новости
     */
    public function testDeleteWrong ()
    {
        DB::table('news')->where('id',1)->delete();

        $this->delete('/api/ru/news/1')
            ->assertStatus(404);
    }
}
