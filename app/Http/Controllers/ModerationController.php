<?php

namespace App\Http\Controllers;

use App\Entities\Comment;
use App\Entities\Event;
use App\Entities\News;
use App\Http\Requests\Moderation\DashboardRequest;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Auth\Access\AuthorizationException;

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
     * Получить
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
}
