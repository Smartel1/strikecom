<?php

namespace Tests\Feature;

use App\Entities\News;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\CreatesApplication;
use Tests\TestCase;
use Tests\Traits\DoctrineTransactions;

class NewsControllerTest extends TestCase
{
    use DoctrineTransactions;
    use CreatesApplication;

    /**
     * Удалить все новости из базы даннных
     */
    private function deleteAllNewsFromDB()
    {
        EntityManager::createQueryBuilder()->from(News::class, 'n')->delete()->getQuery()->getResult();
    }

    /**
     * запрос на список новостей
     */
    public function testIndex()
    {
        $this->deleteAllNewsFromDB();

        entity(News::class)->create([
            'title_ru'    => 'Новости соседнего села',
            'content_ru'  => 'Такие вот дела',
            'date'        => 1544680093,
            'source_link' => 'https://domain.ru/img.gif',
        ]);

        $this->get('/api/ru/news')
            ->assertStatus(200);
    }

    /**
     * запрос на список новостей POST запросом
     */
    public function testIndexPostRequest()
    {
        $this->deleteAllNewsFromDB();

        entity(News::class)->create([
            'title_ru'    => 'Новости соседнего села',
            'content_ru'  => 'Такие вот дела',
            'date'        => 1544680093,
            'source_link' => 'https://domain.ru/img.gif',
        ]);

        $this->post('/api/ru/news-list')
            ->assertStatus(200);
    }

    /**
     * запрос на пометку события в избранное
     */
    public function testFavourite()
    {
        $this->deleteAllNewsFromDB();

        $news = entity(News::class)->create([
            'title_ru'    => 'Новости соседнего села',
            'content_ru'  => 'Такие вот дела',
            'date'        => 1544680093,
            'source_link' => 'https://domain.ru/img.gif',
        ]);

        $this->post('/api/ru/news/' . $news->getId() . '/favourite', ['favourite' => 1])
            ->assertStatus(200);
    }

    /**
     * запрос одной новости
     */
    public function testView()
    {
        $news = entity(News::class)->create([
            'id'         => 1,
            'title_ru'   => 'Трудовой конфликт',
            'content_ru' => 'Такие вот дела',
            'date'       => 1544680093,
        ]);

        $this->get('/api/ru/news/' . $news->getId())
            ->assertStatus(200);
    }

    /**
     * запрос несуществующей новости
     */
    public function testViewWrong()
    {
        $this->deleteAllNewsFromDB();

        $this->get('/api/ru/news/1')
            ->assertStatus(404);
    }

    /**
     * запрос на создание новости
     */
    public function testStore()
    {
        $this->post('/api/ru/news', [
            'title'       => 'Беда в городе',
            'content'     => 'Рабы кричат и гневятся',
            'date'        => 1544680093,
            'source_link' => 'https://domain.ru/img.gif',
            'tags'        => ['нищета', 'голод'],
            'photo_urls'  => ['images/ff.gif'],
            'videos'      => [
                ['url' => 'http://videos.ru/1', 'video_type_id' => 1, 'preview_url' => 'http://a']
            ],
        ])
            ->assertStatus(200);
    }

    /**
     * некорректный запрос на создание новости
     */
    public function testStoreInvalid()
    {
        $this->post('/api/ru/news', [
            'title'       => [],
            'content'     => [],
            'date'        => 15,
            'source_link' => [],
            'tags'        => '55',
            'photo_urls'  => 'images/ff.gif',
            'videos'      => [[]],
        ])
            ->assertStatus(422);
    }

    /**
     * запрос на обновление новости
     */
    public function testUpdate()
    {
        $news = entity(News::class)->create([
            'title_ru'   => 'Новости села',
            'content_ru' => 'Такие вот дела',
            'date'       => 1544680093,
        ]);

        $this->put('/api/ru/news/' . $news->getId(), [
            'title'       => 'Беда в мегаполисе',
            'content'     => 'Рабы беснуются и гневятся',
            'date'        => 1544690093,
            'source_link' => 'https://domain.ru/img.png',
            'tags'        => ['голод'],
            'photo_urls'  => ['images/ff.gif'],
            'videos'      => [
                ['url' => 'http://videos.ru/1', 'video_type_id' => 1, 'preview_url' => 'http://a']
            ],
        ])
            ->assertStatus(200);
    }

    /**
     * некорректый запрос на обновление новости
     */
    public function testUpdateInvalid()
    {
        $news = entity(News::class)->create([
            'title_ru'   => 'Рабы захотели деньжат',
            'content_ru' => 'Богатые дяди визжат',
            'date'       => 1544680093,
        ]);

        $this->put('/api/ru/news/' . $news->getId(), [
            'title'       => [],
            'content'     => [],
            'date'        => 15,
            'source_link' => [],
            'tags'        => '55',
            'photo_urls'  => 'images/ff.gif',
            'videos'      => [],
        ])
            ->assertStatus(422);
    }

    /**
     * запрос на обновление несуществующей новости
     */
    public function testUpdateWrong()
    {
        $this->deleteAllNewsFromDB();

        $this->put('/api/ru/news/1', [
            'title'       => 'Беда в мегаполисе',
            'content'     => 'Рабы беснуются и гневятся',
            'date'        => '2018-10-02',
            'source_link' => 'https://domain.ru/img.png',
            'tags'        => ['голод'],
            'photo_urls'  => ['images/ff.gif'],
            'videos'      => [
                ['url' => 'http://videos.ru/1', 'video_type_id' => 1, 'preview_url' => 'http://a']
            ],
        ])
            ->assertStatus(404);
    }

    /**
     * запрос на удаление новости
     */
    public function testDelete()
    {
        $news = entity(News::class)->create([
            'title_ru'   => 'Трудовой заговор',
            'content_ru' => 'Рабочие тайком сговорились работать на совесть',
            'date'       => 1544680093,
        ]);

        $this->delete('/api/ru/news/' . $news->getId())
            ->assertStatus(200);
    }

    /**
     * запрос на удаление несущесвующей новости
     */
    public function testDeleteWrong()
    {
        $this->deleteAllNewsFromDB();

        $this->delete('/api/ru/news/1')
            ->assertStatus(404);
    }
}
