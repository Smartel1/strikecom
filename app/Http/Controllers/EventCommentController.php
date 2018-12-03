<?php

namespace App\Http\Controllers;

use App\Event;
use App\EventComment;
use App\Http\Requests\EventCommentRequest;
use Illuminate\Support\Facades\Auth;

class EventCommentController extends Controller
{
    public function index(Event $event)
    {
        return $event->comments()->with('user')->get();
    }

    public function store(EventCommentRequest $request,Event $event)
    {
        $this->authorize('create', EventComment::class);

        $data = $request->validated();

        $comment = $event->comments()->create([
            'user_id' => Auth::user()->id,
            'content' => $data['content'],
        ]);

        foreach (array_get($data, 'image_urls', []) as $image) {
            $comment->commentPhotos()->create(['url' => $image]);
        }

        return $comment->fresh('user', 'commentPhotos');
    }

    public function show(Event $event, EventComment $comment)
    {
        return $comment->fresh('user', 'commentPhotos');
    }

    public function update(EventCommentRequest $request, Event $event, EventComment $comment)
    {
        $this->authorize('update', $comment);

        $comment->update($request->only('content'));

        $comment->commentPhotos()->delete();

        foreach (array_get($request->validated(), 'image_urls', []) as $image) {
            $comment->commentPhotos()->create(['url' => $image]);
        }

        return $comment->fresh('user', 'commentPhotos');
    }

    public function destroy(Event $event, EventComment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return $comment->id;
    }
}
