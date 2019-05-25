<?php

namespace App\Http\Controllers;

use App\Entities\Event;
use App\Entities\Comment;
use App\Http\Requests\Comment\CommentDestroyRequest;
use App\Http\Requests\Comment\CommentIndexRequest;
use App\Http\Requests\Comment\CommentShowRequest;
use App\Http\Requests\Comment\CommentStoreRequest;
use App\Http\Requests\Comment\CommentUpdateRequest;
use App\Http\Resources\Comment\CommentResource;
use App\Services\CommentService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class EventCommentController extends Controller
{
    protected $commentService;

    /**
     * EventCommentController constructor.
     * @param CommentService $commentService
     */
    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * @param CommentIndexRequest $request
     * @param $locale
     * @param Event $event
     * @return AnonymousResourceCollection
     * @throws \Exception
     */
    public function index(CommentIndexRequest $request, $locale, Event $event)
    {
        $comments = $this->commentService->index(
            $event,
            Arr::get($request, 'per_page', 20),
            Arr::get($request, 'page', 1)
        );

        return CommentResource::collection($comments);
    }

    /**
     * @param CommentStoreRequest $request
     * @param $locale
     * @param Event $event
     * @return CommentResource
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws AuthorizationException
     */
    public function store(CommentStoreRequest $request, $locale, Event $event)
    {
        $this->authorize('create', Comment::class);

        $comment = $this->commentService->create($event, $request->validated(), Auth::getUser());

        return CommentResource::make($comment);
    }

    /**
     * @param CommentShowRequest $request
     * @param $locale
     * @param Event $event
     * @param Comment $comment
     * @return CommentResource
     */
    public function show(CommentShowRequest $request, $locale, Event $event, Comment $comment)
    {
        return CommentResource::make($comment);
    }

    /**
     * @param CommentUpdateRequest $request
     * @param $locale
     * @param Event $event
     * @param Comment $comment
     * @return CommentResource
     * @throws AuthorizationException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function update(CommentUpdateRequest $request, $locale, Event $event, Comment $comment)
    {
        $this->authorize('update', $comment);

        $comment = $this->commentService->update($comment, $request->validated());

        return CommentResource::make($comment);
    }

    /**
     * @param CommentDestroyRequest $request
     * @param $locale
     * @param Event $event
     * @param Comment $comment
     * @throws AuthorizationException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function destroy(CommentDestroyRequest $request, $locale, Event $event, Comment $comment)
    {
        $this->authorize('delete', $comment);

        $this->commentService->delete($comment);
    }
}
