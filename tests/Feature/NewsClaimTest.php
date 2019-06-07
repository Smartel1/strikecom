<?php

namespace Tests\Feature;

use App\Entities\Comment;
use App\Entities\News;
use App\Entities\User;
use DateTime;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\CreatesApplication;
use Tests\TestCase;
use Tests\Traits\DoctrineTransactions;

class NewsClaimTest extends TestCase
{
    use DoctrineTransactions;
    use CreatesApplication;

    /**
     * Подготовить базу к тетсированию
     * @return array id созданных сущностей
     */
    public function prepareDB()
    {
        EntityManager::createQueryBuilder()->from(News::class, 'n')->delete()->getQuery()->getResult();

        $news = entity(News::class)->create([
            'title_ru'   => 'Новость из соседнего села',
            'content_ru' => 'Такие вот дела',
            'date'       => DateTime::createFromFormat('U', 1544680093),
        ]);

        $comment = new Comment();
        $comment->setContent('Вот это дела');

        $news->getComments()->add($comment);

        EntityManager::persist($comment);
        EntityManager::persist($news);

        EntityManager::createQueryBuilder()->from(User::class, 'u')->delete()->getQuery()->getResult();

        entity(User::class)->create([
            'uuid'  => '1',
            'name'  => 'John Doe',
            'email' => 'jd@mail.ty',
            'admin' => true,
        ]);

        return ['news' => $news->getId(), 'comment' => $comment->getId()];
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
            'admin' => true,
        ]);

        $this->actingAs($user)->post('/api/ru/news/' . $ids['news'] . '/comment/' . $ids['comment'] . '/claim', ['claim_type_id' => 1])
            ->assertStatus(200);
    }

    /**
     * запрос на создание жалобы несуществующего коммента
     */
    public function testStoreWrong()
    {
        $ids = $this->prepareDB();

        $this->post('/api/ru/news/' . $ids['news'] . '/comment/0/claim', ['claim_type_id' => 1])
            ->assertStatus(404);
    }

    /**
     * запрос на создание жалобы несуществущего типа
     */
    public function testStoreInvalid()
    {
        $ids = $this->prepareDB();

        $this->post('/api/ru/news/' . $ids['news'] . '/comment/' . $ids['comment'] . '/claim', ['claim_type_id' => 0])
            ->assertStatus(422);
    }
}
