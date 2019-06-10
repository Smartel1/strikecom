<?php


namespace App\Services;


use App\Entities\Comment;
use App\Entities\Event;
use App\Entities\Interfaces\Commentable;
use App\Entities\News;
use App\Entities\Photo;
use App\Entities\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class CommentService
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * NewsService constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Commentable $commentable
     * @param $perPage
     * @param $page
     * @return LengthAwarePaginator
     * @throws \Exception
     */
    public function index(Commentable $commentable, $perPage, $page)
    {
        switch(get_class($commentable)) {
            case Event::class: $relationName = 'events'; break;
            case News::class: $relationName = 'news'; break;
            default: throw new \Exception('у комментариев нет связи с этой сущностью');
        }

        $queryBuilder = $this->em->createQueryBuilder()
            ->select('c')
            ->from(Comment::class, 'c')
            ->where($this->em->getExpressionBuilder()->isMemberOf(':ca', 'c.'.$relationName))
            ->setParameter('ca', $commentable)
            ->orderBy('c.createdAt', 'desc');

        //Пагинируем результат
        $doctrinePaginator = new Paginator(
            $queryBuilder->setFirstResult($perPage * ($page - 1))->setMaxResults($perPage)->getQuery()
        );

        //Переводим в формат, понятный laravel
        $laravelPaginator = new LengthAwarePaginator(
            collect($doctrinePaginator),
            $doctrinePaginator->count(),
            (integer)$perPage,
            $page,
            ['path'=>request()->url()]
        );

        return $laravelPaginator;
    }

    /**
     * Получить комментарии с жалобами
     * @param $perPage
     * @param $page
     * @return LengthAwarePaginator
     * @throws \Exception
     */
    public function getComplainedComments($perPage, $page)
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('c')
            ->from(Comment::class, 'c')
            ->where('c.claims is not empty')
            ->orderBy('c.createdAt', 'desc');

        //Пагинируем результат
        $doctrinePaginator = new Paginator(
            $queryBuilder->setFirstResult($perPage * ($page - 1))->setMaxResults($perPage)->getQuery()
        );

        //Переводим в формат, понятный laravel
        $laravelPaginator = new LengthAwarePaginator(
            collect($doctrinePaginator),
            $doctrinePaginator->count(),
            (integer)$perPage,
            $page,
            ['path'=>request()->url()]
        );

        return $laravelPaginator;
    }

    /**
     * Прокомментировать событие
     * @param Commentable $commentable
     * @param $data
     * @param User $user
     * @return Comment
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create(Commentable $commentable, $data, User $user)
    {
        $this->em->beginTransaction();

        $comment = new Comment();
        $comment->setContent(Arr::get($data, 'content'));
        $comment->setUser($user);

        foreach (Arr::get($data, 'photo_urls', []) as $photoUrl) {
            $photo = new Photo();
            $photo->setUrl($photoUrl);
            $comment->getPhotos()->add($photo);
            $this->em->persist($photo);
        }

        $commentable->getComments()->add($comment);

        $this->em->persist($comment);
        $this->em->persist($commentable);
        $this->em->flush();
        $this->em->commit();

        return $comment;
    }

    /**
     * @param Comment $comment
     * @param $data
     * @return Comment
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function update(Comment $comment, $data)
    {
        $this->em->beginTransaction();

        $comment->setContent(Arr::get($data, 'content'));

        foreach ($comment->getPhotos() as $claim) {
            $this->em->remove($claim);
        };

        foreach (Arr::get($data, 'photo_urls', []) as $photoUrl) {
            $photo = new Photo();
            $photo->setUrl($photoUrl);
            $comment->getPhotos()->add($photo);
            $this->em->persist($photo);
        }

        //Очищаем жалобы при обновлении коммента
        foreach ($comment->getClaims() as $claim) {
            $this->em->remove($claim);
        };

        $this->em->persist($comment);
        $this->em->flush();
        $this->em->commit();

        return $comment;
    }

    /**
     * @param Comment $comment
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Comment $comment)
    {
        $this->em->beginTransaction();

        foreach ($comment->getPhotos() as $oldPhoto) {
            $this->em->remove($oldPhoto);
        };

        $this->em->remove($comment);
        $this->em->flush();
        $this->em->commit();
    }

}