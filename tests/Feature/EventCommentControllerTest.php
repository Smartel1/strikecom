<?php

namespace Tests\Feature;

use App\Entities\Comment;
use App\Entities\Conflict;
use App\Entities\Event;
use App\Entities\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\CreatesApplication;
use Tests\TestCase;

class EventCommentControllerTest extends TestCase
{
    use DatabaseTransactions;
    use CreatesApplication;

    /**
     * Подготовить базу к тетсированию
     * @return Event тестовое событие
     */
    private function prepareDB(): Event
    {
        EntityManager::createQueryBuilder()->from(Conflict::class, 'c')->delete()->getQuery()->getResult();

        $conflict = entity(Conflict::class)->create([
            'title_ru'     => 'Острый конфликт',
            'latitude'     => 1351315135.45,
            'longitude'    => 1256413515.45,
            'company_name' => 'ЗАО ПАО',
        ]);

        $event = entity(Event::class)->create([
            'conflict_id' => $conflict->getId(),
            'title_ru'    => 'Трудовой конфликт',
            'content_ru'  => 'Такие вот дела',
            'date'        => 1544680093,
        ]);

        $comment1 = new Comment();
        $comment1->setContent('Вот это дела');

        $comment2 = new Comment();
        $comment2->setContent('Ну и дела');

        $event->getComments()->add($comment1);
        $event->getComments()->add($comment2);

        EntityManager::persist($comment1);
        EntityManager::persist($comment2);
        EntityManager::persist($event);

        EntityManager::createQueryBuilder()->from(User::class, 'u')->delete()->getQuery()->getResult();

        entity(User::class)->create([
            'uuid'  => '1',
            'name'  => 'John Doe',
            'email' => 'jd@mail.ty',
            'admin' => true,
        ]);

        return $event;
    }

    /**
     * запрос на список комментариев к событиям
     */
    public function testIndex()
    {
        $event = $this->prepareDB();

        $this->get('/api/ru/event/' . $event->getId() . '/comment')
            ->assertStatus(200);
    }

    /**
     * запрос одного коммента
     */
    public function testView()
    {
        $event = $this->prepareDB();

        $this->get('/api/ru/event/' . $event->getId() . '/comment/' . $event->getComments()->first()->getId())
            ->assertStatus(200);
    }

    /**
     * запрос несуществующего коммента события
     */
    public function testViewWrong()
    {
        $event = $this->prepareDB();

        $this->get('/api/ru/event/' . $event->getId() . '/comment/-1')
            ->assertStatus(404);
    }

    /**
     * запрос на создание коммента события
     */
    public function testStore()
    {
        $event = $this->prepareDB();

        $this->post('/api/ru/event/' . $event->getId() . '/comment', [
            'content'    => 'Надо что-то менять!',
            'image_urls' => ['https://heroku.com/image.png']
        ])
            ->assertStatus(200);
    }

    /**
     * некорректный запрос на создание коммента события
     */
    public function testStoreInvalid()
    {
        $event = $this->prepareDB();

        $this->post('/api/ru/event/' . $event->getId() . '/comment', [
            'content'    => 1,
            'image_urls' => 1
        ])
            ->assertStatus(422);
    }

    /**
     * запрос на обновление коммента события
     */
    public function testUpdate()
    {
        $event = $this->prepareDB();

        $this->put('/api/ru/event/' . $event->getId() . '/comment/' . $event->getComments()->first()->getId(), [
            'content'    => 'Надо что-то менять!',
            'image_urls' => ['https://heroku.com/image.png']
        ])
            ->assertStatus(200);
    }

    /**
     * некорректый запрос на обновление коммента события
     */
    public function testUpdateInvalid()
    {
        $event = $this->prepareDB();

        $this->put('/api/ru/event/' . $event->getId() . '/comment/' . $event->getComments()->first()->getId(), [
            'content'    => 1,
            'image_urls' => 1
        ])
            ->assertStatus(422);
    }

    /**
     * запрос на обновление несуществующего комментария
     */
    public function testUpdateWrong()
    {
        $event = $this->prepareDB();

        $this->put('/api/ru/event/' . $event->getId() . '/comment/-1', [
            'content' => 'comment',
        ])
            ->assertStatus(404);
    }

    /**
     * запрос на удаление комментария
     */
    public function testDelete()
    {
        $event = $this->prepareDB();

        $this->delete('/api/ru/event/' . $event->getId() . '/comment/' . $event->getComments()->first()->getId())
            ->assertStatus(200);
    }

    /**
     * запрос на удаление несущесвующего комментария
     */
    public function testDeleteWrong()
    {
        $event = $this->prepareDB();

        $this->delete('/api/ru/event/' . $event->getId() . '/comment/-1')
            ->assertStatus(404);
    }
}
