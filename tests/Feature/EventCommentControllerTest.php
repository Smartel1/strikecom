<?php

namespace Tests\Feature;

use App\Entities\Claim;
use App\Entities\Comment;
use App\Entities\Conflict;
use App\Entities\Event;
use App\Entities\Photo;
use App\Entities\References\ClaimType;
use App\Entities\User;
use DateTime;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\CreatesApplication;
use Tests\TestCase;
use Tests\Traits\DoctrineTransactions;

class EventCommentControllerTest extends TestCase
{
    use DoctrineTransactions;
    use CreatesApplication;

    /**
     * Подготовить базу к тетсированию
     * @return Event тестовое событие
     */
    private function prepareDB(): Event
    {
        EntityManager::createQueryBuilder()->from(User::class, 'u')->delete()->getQuery()->getResult();

        $user = entity(User::class)->create([
            'uuid'  => '1',
            'name'  => 'John Doe',
            'email' => 'jd@mail.ty',
            'roles' => ['MODERATOR'],
        ]);

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
            'date'        => DateTime::createFromFormat('U', 1544680093),
        ]);

        $comment1 = new Comment();
        $comment1->setContent('Вот это дела');

        //Создаём жалобу на первый комментарий
        $claim = new Claim;
        $claim->setClaimType(EntityManager::getReference(ClaimType::class, 1));
        $claim->setComment($comment1);
        $claim->setUser($user);

        $comment2 = new Comment();
        $comment2->setContent('Ну и дела');

        $photo = new Photo;
        $photo->setUrl('https://my.photo.com/thebestcom');
        $comment2->setPhotos([$photo]);

        $event->getComments()->add($comment1);
        $event->getComments()->add($comment2);

        EntityManager::persist($comment1);
        EntityManager::persist($claim);
        EntityManager::persist($comment2);
        EntityManager::persist($photo);
        EntityManager::persist($event);
        EntityManager::flush();
        //освобождаем память от уже загруженных моделей для чистоты экспериментов
        EntityManager::clear();

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

        $user = entity(User::class)->create([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'roles' => ['MODERATOR'],
        ]);

        $this->actingAs($user)->post('/api/ru/event/' . $event->getId() . '/comment', [
            'content'    => 'Надо что-то менять!',
            'image_urls' => ['https://heroku.com/image.png']
        ])
            ->assertStatus(200);
    }

    /**
     * запрос на создание коммента события неаутентифиц.
     */
    public function testStoreUnauth()
    {
        $event = $this->prepareDB();

        $this->post('/api/ru/event/' . $event->getId() . '/comment', [
            'content'    => 'Надо что-то менять!',
            'image_urls' => ['https://heroku.com/image.png']
        ])
            ->assertStatus(403);
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
        $user = entity(User::class)->create([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'roles' => ['MODERATOR'],
        ]);

        $event = $this->prepareDB();

        $this->actingAs($user)->put('/api/ru/event/' . $event->getId() . '/comment/' . $event->getComments()->first()->getId(), [
            'content'    => 'Надо что-то менять!',
            'image_urls' => ['https://heroku.com/image.png']
        ])
            ->assertStatus(200);
    }

    /**
     * запрос на обновление коммента события
     */
    public function testUpdateUnauth()
    {
        $event = $this->prepareDB();

        $this->put('/api/ru/event/' . $event->getId() . '/comment/' . $event->getComments()->first()->getId(), [
            'content'    => 'Надо что-то менять!',
            'image_urls' => ['https://heroku.com/image.png']
        ])
            ->assertStatus(403);
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
        $user = entity(User::class)->create([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'roles' => ['MODERATOR'],
        ]);

        $event = $this->prepareDB();

        $this->actingAs($user)->delete('/api/ru/event/' . $event->getId() . '/comment/' . $event->getComments()->first()->getId())
            ->assertStatus(200);
    }

    /**
     * запрос на удаление комментария неаутентифиц.
     */
    public function testDeleteUnauth()
    {
        $event = $this->prepareDB();

        $this->delete('/api/ru/event/' . $event->getId() . '/comment/' . $event->getComments()->first()->getId())
            ->assertStatus(403);
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