<?php

namespace Tests\Feature;

use App\News;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\CreatesApplication;
use Tests\TestCase;

class NewsCommentControllerTest extends TestCase
{
    use DatabaseTransactions;
    use CreatesApplication;


    public function prepareDB()
    {
        DB::table('news')->where('id',1)->delete();

        DB::table('news')->insert([
            'id'            => 1,
            'title_ru'         => 'Новость из соседнего села',
            'content_ru'       => 'Такие вот дела',
            'date'          => 1544680093,
        ]);

        DB::table('users')->where('id',1)->delete();

        DB::table('users')->insert([
            'id'            => 1,
            'uuid'           => 'tasd446d6a4sd46as4d6',
            'name'          => 'John Doe',
            'email'         => 'jd@mail.ty',
            'admin'         => true,
        ]);
    }

    /**
     * запрос на список комментариев к новости
     */
    public function testIndex ()
    {
        $this->prepareDB();

        News::find(1)->comments()->create([
            'content'         => 'Вот это дела'
        ]);

        News::find(1)->comments()->create([
            'content'         => 'Ну и дела'
        ]);

        $this->get('/api/ru/news/1/comment')
            ->assertStatus(200);
    }

    /**
     * запрос одного коммента
     */
    public function testView ()
    {
        $this->prepareDB();

        $comment = News::find(1)->comments()->create([
            'content'         => 'Ну и дела'
        ]);

        $this->get("/api/ru/news/1/comment/$comment->id")
            ->assertStatus(200);
    }

    /**
     * запрос несуществующего коммента события
     */
    public function testViewWrong ()
    {
        $this->prepareDB();

        $this->get('/api/ru/news/1/comment/2')
            ->assertStatus(404);
    }

    /**
     * запрос на создание коммента новости
     */
    public function testStore ()
    {
        $this->prepareDB();

        $this->post('/api/ru/news/1/comment', [
                'content'       => 'Надо реагировать!',
                'image_urls'    => ['https://heroku.com/image.png']
            ])
            ->assertStatus(201);
    }

    /**
     * некорректный запрос на создание коммента новости
     */
    public function testStoreInvalid ()
    {
        $this->prepareDB();

        $this->post('/api/ru/news/1/comment', [
            'content'       => 1,
            'image_urls'    => 1
        ])
            ->assertStatus(422);
    }

    /**
     * запрос на обновление коммента события
     */
    public function testUpdate ()
    {
        $this->prepareDB();

        $comment = News::find(1)->comments()->create([
            'content'         => 'Ну и дела'
        ]);

        $this->put("/api/ru/news/1/comment/$comment->id", [
            'content'       => 'Надо что-то думать!',
            'image_urls'    => ['https://heroku.com/image.png']
        ])
            ->assertStatus(200);
    }

    /**
     * некорректый запрос на обновление коммента новости
     */
    public function testUpdateInvalid ()
    {
        $this->prepareDB();

        $comment = News::find(1)->comments()->create([
            'content'         => 'Ну и дела'
        ]);

        $this->put("/api/ru/news/1/comment/$comment->id", [
            'content'       => 1,
            'image_urls'    => 1
        ])
            ->assertStatus(422);
    }

    /**
     * запрос на обновление несуществующего комментария новости
     */
    public function testUpdateWrong ()
    {
        $this->prepareDB();

        $this->put('/api/ru/news/1/comment/1', [
            'content'       => 'comment',
        ])
            ->assertStatus(404);
    }

    /**
     * запрос на удаление комментария
     */
    public function testDelete ()
    {
        $this->prepareDB();

        $comment = News::find(1)->comments()->create([
            'content'         => 'Ну и дела'
        ]);

        $this->delete("/api/ru/news/1/comment/$comment->id")
            ->assertStatus(200);
    }

    /**
     * запрос на удаление несущесвующего комментария
     */
    public function testDeleteWrong ()
    {
        $this->prepareDB();

        $this->delete('/api/ru/news/1/comment/1')
            ->assertStatus(404);
    }
}
