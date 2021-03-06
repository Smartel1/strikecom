<?php

namespace App\Http\Controllers;

use App\Entities\Comment;
use App\Entities\Event;
use App\Entities\News;
use App\Http\Requests\Moderation\ComplainedCommentsRequest;
use App\Http\Requests\Moderation\DashboardRequest;
use App\Http\Resources\Comment\CommentResource;
use App\Http\Resources\Comment\ModerationCommentResource;
use App\Services\CommentService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;

class ModerationController extends Controller
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * RefController constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Получить цифры для отображения на панели администратора
     * @param DashboardRequest $request
     * @param $locale
     * @return array
     * @throws AuthorizationException
     * @throws NonUniqueResultException
     */
    public function dashboard(DashboardRequest $request, $locale)
    {
        $this->authorize('moderate');

        $complaintCommentsCount = $this->em->createQueryBuilder()
            ->select('count(c)')
            ->from(Comment::class, 'c')
            ->where('c.claims is not empty')
            ->getQuery()
            ->getSingleScalarResult();

        $nonpublishedEventsCount = $this->em->createQueryBuilder()
            ->select('count(e)')
            ->from(Event::class, 'e')
            ->where('e.published = false')
            ->getQuery()
            ->getSingleScalarResult();

        $nonpublishedNewsCount = $this->em->createQueryBuilder()
            ->select('count(e)')
            ->from(News::class, 'e')
            ->where('e.published = false')
            ->getQuery()
            ->getSingleScalarResult();

        return [
            'complaint_comments_count'  => $complaintCommentsCount,
            'nonpublished_events_count' => $nonpublishedEventsCount,
            'nonpublished_news_count'   => $nonpublishedNewsCount,
        ];
    }

    /**
     * Получить все комментарии, на которые жаловались
     * @param ComplainedCommentsRequest $request
     * @param $locale
     * @param CommentService $service
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     * @throws \Exception
     */
    public function getComplainComments(ComplainedCommentsRequest $request, $locale, CommentService $service)
    {
        $this->authorize('moderate');

        $comments = $service->getComplainedComments(
            Arr::get($request, 'per_page', 20),
            Arr::get($request, 'page', 1)
        );

        return ModerationCommentResource::collection($comments);
    }
}
