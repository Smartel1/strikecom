<?php

namespace App\Http\Controllers;

use App\Event;
use App\Comment;
use App\Http\Requests\Comment\CommentDestroyRequest;
use App\Http\Requests\Comment\CommentIndexRequest;
use App\Http\Requests\Comment\CommentShowRequest;
use App\Http\Requests\Comment\CommentStoreRequest;
use App\Http\Requests\Comment\CommentUpdateRequest;
use Illuminate\Support\Facades\Auth;

class EventCommentController extends Controller
{
    public function index(CommentIndexRequest $request, Event $event)
    {
        return $event->comments()->with('user', 'photos')->get();
    }

    public function store(CommentStoreRequest $request, Event $event)
    {
        $this->authorize('create', Comment::class);

        $data = $request->validated();

        $comment = $event->comments()->create([
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

    public function show(CommentShowRequest $request, Event $event, Comment $comment)
    {
        return $comment->fresh('user', 'photos');
    }

    public function update(CommentUpdateRequest $request, Event $event, Comment $comment)
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

    public function destroy(CommentDestroyRequest $request, Event $event, Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return $comment->id;
    }
}
