<?php


namespace App\Services;


use App\Entities\Comment;
use App\Entities\Interfaces\Commentable;
use App\Entities\Photo;
use App\Entities\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
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
     * @return Comment[]|ArrayCollection
     */
    public function getComments(Commentable $commentable)
    {
        return $commentable->getComments();
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

        foreach ($comment->getPhotos() as $oldPhoto) {
            $this->em->remove($oldPhoto);
        };

        foreach (Arr::get($data, 'photo_urls', []) as $photoUrl) {
            $photo = new Photo();
            $photo->setUrl($photoUrl);
            $comment->getPhotos()->add($photo);
            $this->em->persist($photo);
        }

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