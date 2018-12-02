<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Conflict;
use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index(Conflict $conflict)
    {
        return $conflict->comments()->with('user')->get();
    }

    public function store(CommentRequest $request, Conflict $conflict)
    {
        $this->authorize('create', Comment::class);

        $data = $request->validated();

        $comment = $conflict->comments()->create([
            'user_id' => Auth::user()->id,
            'content' => $data['content'],
        ]);

        foreach (array_get($data, 'image_urls', []) as $image) {
            $comment->commentPhotos()->create(['url' => $image]);
        }

        return $comment->fresh('user', 'commentPhotos');
    }

    public function show(Conflict $conflict, Comment $comment)
    {
        return $comment->fresh('user', 'commentPhotos');
    }

    public function update(CommentRequest $request, Conflict $conflict, Comment $comment)
    {
        $this->authorize('update', $comment);

        $comment->update($request->only('content'));

        $comment->commentPhotos()->delete();

        foreach (array_get($request->validated(), 'image_urls', []) as $image) {
            $comment->commentPhotos()->create(['url' => $image]);
        }

        return $comment->fresh('user', 'commentPhotos');
    }

    public function destroy(Conflict $conflict, Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return $comment->id;
    }
}
