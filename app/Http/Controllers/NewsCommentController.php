<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Requests\Comment\CommentDestroyRequest;
use App\Http\Requests\Comment\CommentIndexRequest;
use App\Http\Requests\Comment\CommentShowRequest;
use App\Http\Requests\Comment\CommentStoreRequest;
use App\Http\Requests\Comment\CommentUpdateRequest;
use App\News;
use Illuminate\Support\Facades\Auth;

class NewsCommentController extends Controller
{
    public function index(CommentIndexRequest $request, News $news)
    {
        return $news->comments()->with('user', 'photos')->get();
    }

    public function store(CommentStoreRequest $request, News $news)
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

        return $comment->fresh('user', 'photos');
    }

    public function show(CommentShowRequest $request, News $news, Comment $comment)
    {
        return $comment->fresh('user', 'photos');
    }

    public function update(CommentUpdateRequest $request, News $news, Comment $comment)
    {
        $this->authorize('update', $comment);

        $comment->update($request->only('content'));

        $comment->photos()->delete();

        foreach (array_get($request->validated(), 'photo_urls', []) as $url) {
            $comment->photos()->create([
                'url'           => $url,
            ]);
        }

        return $comment->fresh('user', 'photos');
    }

    public function destroy(CommentDestroyRequest $request, News $news, Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return $comment->id;
    }
}
