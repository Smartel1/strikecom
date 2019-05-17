<?php

namespace App\Http\Controllers;

use App\Entities\News;
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
use Illuminate\Support\Facades\Auth;

class NewsCommentController extends Controller
{
    protected $commentService;

    /**
     * NewsCommentController constructor.
     * @param CommentService $commentService
     */
    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * @param CommentIndexRequest $request
     * @param $locale
     * @param News $news
     * @return AnonymousResourceCollection
     */
    public function index(CommentIndexRequest $request, $locale, News $news)
    {
        $comments = collect($this->commentService->getComments($news));

        return CommentResource::collection($comments);
    }

    /**
     * @param CommentStoreRequest $request
     * @param $locale
     * @param News $news
     * @return CommentResource
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws AuthorizationException
     */
    public function store(CommentStoreRequest $request, $locale, News $news)
    {
        $this->authorize('create', Comment::class);

        $comment = $this->commentService->create($news,  $request->validated(), Auth::getUser());

        return CommentResource::make($comment);
    }

    /**
     * @param CommentShowRequest $request
     * @param $locale
     * @param News $news
     * @param Comment $comment
     * @return CommentResource
     */
    public function show(CommentShowRequest $request, $locale, News $news, Comment $comment)
    {
        return CommentResource::make($comment);
    }

    /**
     * @param CommentUpdateRequest $request
     * @param $locale
     * @param News $news
     * @param Comment $comment
     * @return CommentResource
     * @throws AuthorizationException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function update(CommentUpdateRequest $request, $locale, News $news, Comment $comment)
    {
        $this->authorize('update', $comment);

        $comment = $this->commentService->update($comment, $request->validated());

        return CommentResource::make($comment);
    }

    /**
     * @param CommentDestroyRequest $request
     * @param $locale
     * @param News $news
     * @param Comment $comment
     * @throws AuthorizationException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function destroy(CommentDestroyRequest $request, $locale, News $news, Comment $comment)
    {
        $this->authorize('delete', $comment);

        $this->commentService->delete($comment);
    }
}
