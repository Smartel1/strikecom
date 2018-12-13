<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\CreatesApplication;
use Tests\TestCase;

class EventCommentControllerTest extends TestCase
{
    use DatabaseTransactions;
    use CreatesApplication;


    public function seed()
    {
        DB::table('events')->where('id',1)->delete();

        DB::table('events')->insert([
            'id'            => 1,
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
        $this->seed();

        DB::table('event_comments')->insert([
            'user_id'         => 1,
            'event_id'        => 1,
            'content'         => 'Вот это дела',
            'created_at'      => 1544686018,
            'updated_at'      => 1544686018,
        ]);

        DB::table('event_comments')->insert([
            'user_id'         => 1,
            'event_id'        => 1,
            'content'         => 'Ну и дела',
            'created_at'      => 1544686030,
            'updated_at'      => 1544686030,
        ]);

        $this->get('/api/event/1/comment')
            ->assertStatus(200);
    }

    /**
     * запрос одного коммента
     */
    public function testView ()
    {
        $this->seed();

        DB::table('event_comments')->insert([
            'id'              => 1,
            'user_id'         => 1,
            'event_id'        => 1,
            'content'         => 'Ну и дела',
            'created_at'      => 1544686030,
            'updated_at'      => 1544686030,
        ]);

        $this->get('/api/event/1/comment/1')
            ->assertStatus(200);
    }

    /**
     * запрос несуществующего коммента события
     */
    public function testViewWrong ()
    {
        $this->seed();

        $this->get('/api/event/1/comment/2')
            ->assertStatus(404);
    }

    /**
     * запрос на создание коммента события
     */
    public function testStore ()
    {
        $this->seed();

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
        $this->seed();

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
        $this->seed();

        DB::table('event_comments')->insert([
            'id'              => 1,
            'user_id'         => 1,
            'event_id'        => 1,
            'content'         => 'Вот это дела',
            'created_at'      => 1544686018,
            'updated_at'      => 1544686018,
        ]);

        $this->put('/api/event/1/comment/1', [
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
        $this->seed();

        DB::table('event_comments')->insert([
            'id'              => 1,
            'user_id'         => 1,
            'event_id'        => 1,
            'content'         => 'Вот это дела',
            'created_at'      => 1544686018,
            'updated_at'      => 1544686018,
        ]);

        $this->put('/api/event/1/comment/1', [
            'content'       => 1,
            'image_urls'    => 1
        ])
            ->assertStatus(422);
    }

    /**
     * запрос на обновление несуществующего события
     */
    public function testUpdateWrong ()
    {
        $this->seed();

        $this->put('/api/event/1/comment/1', [
            'content'       => 'comment',
        ])
            ->assertStatus(404);
    }

    /**
     * запрос на удаление события
     */
    public function testDelete ()
    {
        $this->seed();

        DB::table('event_comments')->insert([
            'id'              => 1,
            'user_id'         => 1,
            'event_id'        => 1,
            'content'         => 'Вот это дела',
            'created_at'      => 1544686018,
            'updated_at'      => 1544686018,
        ]);

        $this->delete('/api/event/1/comment/1')
            ->assertStatus(200);
    }

    /**
     * запрос на удаление несущесвующего события
     */
    public function testDeleteWrong ()
    {
        $this->seed();

        $this->delete('/api/event/1/comment/1')
            ->assertStatus(404);
    }
}
