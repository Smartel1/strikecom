<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Requests\Comment\CommentDestroyRequest;
use App\Http\Requests\Comment\CommentIndexRequest;
use App\Http\Requests\Comment\CommentShowRequest;
use App\Http\Requests\Comment\CommentStoreRequest;
use App\Http\Requests\Comment\CommentUpdateRequest;
use App\Http\Resources\Comment\CommentResource;
use App\Models\News;
use Illuminate\Support\Facades\Auth;

class NewsCommentController extends Controller
{
    public function index(CommentIndexRequest $request, $locale, News $news)
    {
        return CommentResource::collection($news->comments);
    }

    public function store(CommentStoreRequest $request, $locale, News $news)
    {
        $this->authorize('create', Comment::class);

        $data = $request->validated();

        $comment = $news->comments()->create([
            'user_id' => Auth::user()->id,
            'content' => $data['content'],
        ]);

        foreach (array_get($request->validated(), 'photo_urls', []) as $url) {
            $comment->photos()->create([
                'url'           => $url,
            ]);
        }

        return CommentResource::make($comment);
    }

    public function show(CommentShowRequest $request, $locale, News $news, Comment $comment)
    {
        return CommentResource::make($comment);
    }

    public function update(CommentUpdateRequest $request, $locale, News $news, Comment $comment)
    {
        $this->authorize('update', $comment);

        $comment->update($request->only('content'));

        $comment->photos()->delete();

        foreach (array_get($request->validated(), 'photo_urls', []) as $url) {
            $comment->photos()->create([
                'url'           => $url,
            ]);
        }

        return CommentResource::make($comment);
    }

    public function destroy(CommentDestroyRequest $request, $locale, News $news, Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return $comment->id;
    }
}
