<?php

namespace App\Http\Controllers;

use App\Conflict;
use App\Event;
use App\Http\Requests\Event\EventDestroyRequest;
use App\Http\Requests\Event\EventIndexRequest;
use App\Http\Requests\Event\EventStoreRequest;
use App\Http\Requests\Event\EventShowRequest;
use App\Http\Requests\Event\EventUpdateRequest;
use App\Services\TagService;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    protected $tagService;

    protected $relations = ['photos', 'user', 'tags', 'conflict'];

    /**
     * EventController constructor.
     * @param $tagService
     */
    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function index(EventIndexRequest $request, Conflict $conflict)
    {
        $tag_id = array_get($request->validated(),'filters.tag_id');

        return $conflict->events()
            ->when($tag_id, function($query) use ($tag_id){
                $query->whereHas('tags', function($query) use ($tag_id){
                    $query->where('id', $tag_id);
                });
            })
            ->with($this->relations)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function store(EventStoreRequest $request, Conflict $conflict)
    {
        $this->authorize('create', Event::class);

        $event = $conflict->events()->create(
            array_merge(
                $request->validated(),
                ['user_id' => Auth::getUser()->id]
            ));

        foreach ($request->get('image_urls', []) as $image) {
            $event->photos()->create(['url' => $image]);
        }

        $this->tagService->updateEventTags($event, $request->get('tags', []));

        return $event->fresh($this->relations);
    }

    public function show(EventShowRequest $request, Conflict $conflict, Event $event)
    {
        $event->increment('views');

        return $event->fresh(array_merge($this->relations, ['comments.photos']));
    }

    public function update(EventUpdateRequest $request, Conflict $conflict, Event $event)
    {
        $this->authorize('update', $event);

        $event->update($request->validated());

        $event->photos()->delete();

        foreach (array_get($request->validated(), 'image_urls', []) as $image) {
            $event->photos()->create(['url' => $image]);
        }

        $this->tagService->updateEventTags($event, $request->get('tags', []));

        return $event->fresh($this->relations);
    }

    public function destroy(EventDestroyRequest $request, Conflict $conflict, Event $event)
    {
        $this->authorize('delete', $event);

        $event->delete();

        return $event->id;
    }
}
