<?php

namespace Tests\Feature;

use App\Entities\Claim;
use App\Entities\Comment;
use App\Entities\News;
use App\Entities\Photo;
use App\Entities\References\ClaimType;
use App\Entities\User;
use DateTime;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\CreatesApplication;
use Tests\TestCase;
use Tests\Traits\DoctrineTransactions;

class NewsCommentControllerTest extends TestCase
{
    use DoctrineTransactions;
    use CreatesApplication;

    /**
     * Подготовить базу к тетсированию
     * @return News тестовая новость
     */
    public function prepareDB(): News
    {
        EntityManager::createQueryBuilder()->from(User::class, 'u')->delete()->getQuery()->getResult();

        $user = entity(User::class)->create([
            'uuid'  => '1',
            'name'  => 'John Doe',
            'email' => 'jd@mail.ty',
            'roles' => ['MODERATOR'],
        ]);

        EntityManager::createQueryBuilder()->from(News::class, 'n')->delete()->getQuery()->getResult();

        $news = entity(News::class)->create([
            'title_ru'   => 'Новость из соседнего села',
            'content_ru' => 'Такие вот дела',
            'date'       => DateTime::createFromFormat('U', 1544680093),
        ]);

        $comment1 = new Comment();
        $comment1->setContent('Вот это дела');
        $comment1->setUser($user);

        //Создаём жалобу на первый комментарий
        $claim = new Claim;
        $claim->setClaimType(EntityManager::getReference(ClaimType::class, 1));
        $claim->setComment($comment1);
        $claim->setUser($user);

        $comment2 = new Comment();
        $comment2->setContent('Ну и дела');
        $comment2->setUser($user);

        $photo = new Photo;
        $photo->setUrl('https://my.photo.com/thebestcom');
        $comment2->setPhotos([$photo]);

        $news->getComments()->add($comment1);
        $news->getComments()->add($comment2);

        EntityManager::persist($comment1);
        EntityManager::persist($claim);
        EntityManager::persist($comment2);
        EntityManager::persist($photo);
        EntityManager::persist($news);
        EntityManager::flush();
        //освобождаем память от уже загруженных моделей (иначе не отображаются связанные сущности комментариев)
        EntityManager::clear();

        return $news;
    }

    /**
     * запрос на список комментариев к новости
     */
    public function testIndex()
    {
        $news = $this->prepareDB();

        $this->get('/api/ru/news/' . $news->getId() . '/comment')
            ->assertStatus(200);
    }

    /**
     * запрос одного коммента
     */
    public function testView()
    {
        $news = $this->prepareDB();

        $this->get('/api/ru/news/' . $news->getId() . '/comment/' . $news->getComments()->first()->getId())
            ->assertStatus(200);
    }

    /**
     * запрос несуществующего коммента события
     */
    public function testViewWrong()
    {
        $news = $this->prepareDB();

        $this->get('/api/ru/news/' . $news->getId() . '/comment/' . -1)
            ->assertStatus(404);
    }

    /**
     * запрос на создание коммента новости
     */
    public function testStore()
    {
        $news = $this->prepareDB();

        $user = entity(User::class)->create([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'roles' => ['MODERATOR'],
        ]);

        $this->actingAs($user)->post('/api/ru/news/' . $news->getId() . '/comment', [
            'content'    => 'Надо реагировать!',
            'image_urls' => ['https://heroku.com/image.png']
        ])
            ->assertStatus(200);
    }

    /**
     * запрос на создание коммента новости
     */
    public function testStoreUnauth()
    {
        $news = $this->prepareDB();

        $this->post('/api/ru/news/' . $news->getId() . '/comment', [
            'content'    => 'Надо реагировать!',
            'image_urls' => ['https://heroku.com/image.png']
        ])
            ->assertStatus(403);
    }

    /**
     * некорректный запрос на создание коммента новости
     */
    public function testStoreInvalid()
    {
        $news = $this->prepareDB();

        $this->post('/api/ru/news/' . $news->getId() . '/comment', [
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
        $news = $this->prepareDB();

        $user = entity(User::class)->create([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'roles' => ['MODERATOR'],
        ]);

        $this->actingAs($user)->put('/api/ru/news/' . $news->getId() . '/comment/' . $news->getComments()->first()->getId(), [
            'content'    => 'Надо что-то думать!',
            'image_urls' => ['https://heroku.com/image.png']
        ])
            ->assertStatus(200);
    }

    /**
     * запрос на обновление коммента события
     */
    public function testUpdateUnauth()
    {
        $news = $this->prepareDB();

        $this->put('/api/ru/news/' . $news->getId() . '/comment/' . $news->getComments()->first()->getId(), [
            'content'    => 'Надо что-то думать!',
            'image_urls' => ['https://heroku.com/image.png']
        ])
            ->assertStatus(403);
    }

    /**
     * некорректый запрос на обновление коммента новости
     */
    public function testUpdateInvalid()
    {
        $news = $this->prepareDB();

        $this->put('/api/ru/news/' . $news->getId() . '/comment/' . $news->getComments()->first()->getId(), [
            'content'    => 1,
            'image_urls' => 1
        ])
            ->assertStatus(422);
    }

    /**
     * запрос на обновление несуществующего комментария новости
     */
    public function testUpdateWrong()
    {
        $news = $this->prepareDB();

        $this->put('/api/ru/news/' . $news->getId() . '/comment/-1', [
            'content' => 'comment',
        ])
            ->assertStatus(404);
    }

    /**
     * запрос на удаление комментария
     */
    public function testDelete()
    {
        $news = $this->prepareDB();

        $user = entity(User::class)->create([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'roles' => ['MODERATOR'],
        ]);

        $this->actingAs($user)->delete('/api/ru/news/' . $news->getId() . '/comment/' . $news->getComments()->first()->getId())
            ->assertStatus(200);
    }

    /**
     * запрос на удаление комментария неаутентифиц.
     */
    public function testDeleteUnauth()
    {
        $news = $this->prepareDB();

        $this->delete('/api/ru/news/' . $news->getId() . '/comment/' . $news->getComments()->first()->getId())
            ->assertStatus(403);
    }

    /**
     * запрос на удаление несуществующего комментария
     */
    public function testDeleteWrong()
    {
        $news = $this->prepareDB();

        $this->delete('/api/ru/news/' . $news->getId() . '/comment/-1')
            ->assertStatus(404);
    }
}
