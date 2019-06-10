<?php

namespace Tests\Feature;

use App\Entities\Comment;
use App\Entities\Conflict;
use App\Entities\Event;
use App\Entities\User;
use DateTime;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\CreatesApplication;
use Tests\TestCase;
use Tests\Traits\DoctrineTransactions;

class EventClaimTest extends TestCase
{
    use DoctrineTransactions;
    use CreatesApplication;

    /**
     * Подготовить базу к тетсированию
     * @return array id созданных сущностей
     */
    public function prepareDB()
    {
        EntityManager::createQueryBuilder()->from(Event::class, 'e')->delete()->getQuery()->getResult();

        $conflict = entity(Conflict::class)->create([
            'title_ru'           => 'Острый конфликт',
            'latitude'           => 1351315135.45,
            'longitude'          => 1256413515.45,
            'company_name'       => 'ЗАО ПАО',
            'conflict_reason_id' => 1,
            'conflict_result_id' => 3,
            'industry_id'        => 3,
            'region_id'          => 3,
            'date_from'          => DateTime::createFromFormat('U', 1544680093),
            'date_to'            => DateTime::createFromFormat('U', 1544690093),
        ]);

        $event = entity(Event::class)->create([
            'conflict_id'     => $conflict->getId(),
            'title_ru'        => 'Трудовой конфликт',
            'content_ru'      => 'Такие вот дела',
            'date'            => DateTime::createFromFormat('U', 1544680093),
            'source_link'     => 'https://domain.ru/img.gif',
            'event_status_id' => '1',
            'event_type_id'   => '3',
        ]);

        $comment = new Comment();
        $comment->setContent('Вот это дела');

        $event->getComments()->add($comment);

        EntityManager::persist($comment);
        EntityManager::persist($event);

        EntityManager::createQueryBuilder()->from(User::class, 'u')->delete()->getQuery()->getResult();

        entity(User::class)->create([
            'uuid'  => '1',
            'name'  => 'John Doe',
            'email' => 'jd@mail.ty',
            'roles' => ['MODERATOR'],
        ]);

        return [
            'event'    => $event->getId(),
            'comment'  => $comment->getId()
        ];
    }

    /**
     * запрос на создание жалобы
     */
    public function testStore()
    {
        $ids = $this->prepareDB();

        $user = entity(User::class)->create([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'roles' => ['MODERATOR'],
        ]);

        $this->actingAs($user)->post('/api/ru/event/' . $ids['event']
            . '/comment/' . $ids['comment']
            . '/claim', ['claim_type_id' => 1])
            ->assertStatus(200);
    }

    /**
     * запрос на создание жалобы несуществующего коммента
     */
    public function testStoreWrong()
    {
        $ids = $this->prepareDB();

        $this->post('/api/ru/event/' . $ids['event']
            . '/comment/' . 0
            . '/claim', ['claim_type_id' => 1])
            ->assertStatus(404);
    }

    /**
     * запрос на создание жалобы несуществущего типа
     */
    public function testStoreInvalid()
    {
        $ids = $this->prepareDB();

        $this->post('/api/ru/event/' . $ids['event']
            . '/comment/' . $ids['comment']
            . '/claim', ['claim_type_id' => 0])
            ->assertStatus(422);
    }
}
