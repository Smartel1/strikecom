<?php

namespace Tests\Feature;

use App\Entities\Claim;
use App\Entities\Comment;
use App\Entities\News;
use App\Entities\References\ClaimType;
use App\Entities\User;
use DateTime;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\CreatesApplication;
use Tests\TestCase;
use Tests\Traits\DoctrineTransactions;

class ModerationControllerTest extends TestCase
{
    use DoctrineTransactions;
    use CreatesApplication;

    /**
     * запрос данных для панели модератора
     */
    public function testDashboard ()
    {
        $user = entity(User::class)->create([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'roles' => ['MODERATOR'],
        ]);

        $this->actingAs($user)->get('/api/ru/moderation/dashboard/')
            ->assertStatus(200);
    }

    /**
     * запрос данных для панели модератора немодератором
     */
    public function testDashboardNonModerator ()
    {
        $user = entity(User::class)->make([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'roles' => [],
        ]);

        $this->actingAs($user)->get('/api/ru/moderation/dashboard/')
            ->assertStatus(403);
    }

    /**
     * запрос комментариев с жалобами
     */
    public function testClaimComments ()
    {
        EntityManager::createQuery('DELETE FROM App\Entities\Comment')->execute();

        $user = entity(User::class)->create([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'roles' => ['MODERATOR'],
        ]);

        $news = entity(News::class)->create([
            'title_ru'   => 'Новость из соседнего села',
            'content_ru' => 'Такие вот дела',
        ]);

        $comment = new Comment();
        $comment->setContent('Вот это дела');
        $comment->setUser($user);
        $news->getComments()->add($comment);

        //Создаём жалобу на комментарий
        $claim = new Claim;
        $claim->setClaimType(EntityManager::getReference(ClaimType::class, 1));
        $claim->setComment($comment);
        $claim->setUser($user);

        EntityManager::persist($claim);
        EntityManager::persist($comment);
        EntityManager::persist($news);
        EntityManager::flush();
        //освобождаем память от уже загруженных моделей (иначе не отображаются связанные сущности комментариев)
        EntityManager::clear();
        $this->actingAs($user)->get('/api/ru/moderation/claim-comment/')
            ->assertStatus(200);
    }

    /**
     * запрос комментариев с жалобами
     */
    public function testClaimCommentsNonModerator ()
    {
        $user = entity(User::class)->make([
            'name'  => 'John Doe',
            'email' => 'john@doe.com',
            'roles' => [],
        ]);

        $this->actingAs($user)->get('/api/ru/moderation/claim-comment/')
            ->assertStatus(403);
    }
}
