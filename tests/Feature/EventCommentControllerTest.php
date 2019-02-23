<?php

namespace Tests\Feature;

use App\Event;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\CreatesApplication;
use Tests\TestCase;

class EventCommentControllerTest extends TestCase
{
    use DatabaseTransactions;
    use CreatesApplication;

    private function prepareDB()
    {
        DB::table('conflicts')->delete();

        DB::table('conflicts')->insert([
            'id'                => 1,
            'title'             => 'Острый конфликт',
            'latitude'          => 1351315135.45,
            'longitude'         => 1256413515.45,
            'company_name'      => 'ЗАО ПАО',
        ]);

        DB::table('events')->insert([
            'id'            => 1,
            'conflict_id'   => 1,
            'title'         => 'Трудовой конфликт',
            'content'       => 'Такие вот дела',
            'date'          => 1544680093,
        ]);

        DB::table('users')->where('id',1)->delete();

        DB::table('users')->insert([
            'id'            => 1,
            'uid'           => 'tasd446d6a4sd46as4d6',
            'name'          => 'John Doe',
            'email'         => 'jd@mail.ty',
            'admin'         => true,
        ]);
    }

    /**
     * запрос на список комментариев к событиям
     */
    public function testIndex ()
    {
        $this->prepareDB();

        Event::find(1)->comments()->create([
            'content'         => 'Вот это дела'
        ]);

        Event::find(1)->comments()->create([
            'content'         => 'Ну и дела'
        ]);

        $this->get('/api/event/1/comment')
            ->assertStatus(200);
    }

    /**
     * запрос одного коммента
     */
    public function testView ()
    {
        $this->prepareDB();

        $comment = Event::find(1)->comments()->create([
            'content'         => 'Ну и дела'
        ]);

        $this->get("/api/event/1/comment/$comment->id")
            ->assertStatus(200);
    }

    /**
     * запрос несуществующего коммента события
     */
    public function testViewWrong ()
    {
        $this->prepareDB();

        $this->get('/api/event/1/comment/2')
            ->assertStatus(404);
    }

    /**
     * запрос на создание коммента события
     */
    public function testStore ()
    {
        $this->prepareDB();

        $this->post('/api/event/1/comment', [
                'content'       => 'Надо что-то менять!',
                'image_urls'    => ['https://heroku.com/image.png']
            ])
            ->assertStatus(200);
    }

    /**
     * некорректный запрос на создание коммента события
     */
    public function testStoreInvalid ()
    {
        $this->prepareDB();

        $this->post('/api/event/1/comment', [
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

        $comment = Event::find(1)->comments()->create([
            'content'         => 'Ну и дела'
        ]);

        $this->put("/api/event/1/comment/$comment->id", [
            'content'       => 'Надо что-то менять!',
            'image_urls'    => ['https://heroku.com/image.png']
        ])
            ->assertStatus(200);
    }

    /**
     * некорректый запрос на обновление коммента события
     */
    public function testUpdateInvalid ()
    {
        $this->prepareDB();

        $comment = Event::find(1)->comments()->create([
            'content'         => 'Ну и дела'
        ]);

        $this->put("/api/event/1/comment/$comment->id", [
            'content'       => 1,
            'image_urls'    => 1
        ])
            ->assertStatus(422);
    }

    /**
     * запрос на обновление несуществующего комментария
     */
    public function testUpdateWrong ()
    {
        $this->prepareDB();

        $this->put('/api/event/1/comment/1', [
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

        $comment = Event::find(1)->comments()->create([
            'content'         => 'Ну и дела'
        ]);

        $this->delete("/api/event/1/comment/$comment->id")
            ->assertStatus(200);
    }

    /**
     * запрос на удаление несущесвующего комментария
     */
    public function testDeleteWrong ()
    {
        $this->prepareDB();

        $this->delete('/api/event/1/comment/1')
            ->assertStatus(404);
    }
}
