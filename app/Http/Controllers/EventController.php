<?php

namespace App\Http\Controllers;

use App\Event;
use App\Http\Requests\Event\EventDestroyRequest;
use App\Http\Requests\Event\EventIndexRequest;
use App\Http\Requests\Event\EventStoreRequest;
use App\Http\Requests\Event\EventShowRequest;
use App\Http\Requests\Event\EventUpdateRequest;
use App\Services\TagService;

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

    public function index(EventIndexRequest $request)
    {
        $tag_id = array_get($request->validated(),'filters.tag_id');

        return Event::with($this->relations)
            ->when($tag_id, function($query) use ($tag_id){
                $query->whereHas('tags', function($query) use ($tag_id){
                    $query->where('id', $tag_id);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(array_get($request, 'per_page', 20));
    }

    public function store(EventStoreRequest $request)
    {
        $this->authorize('create', Event::class);

        $event = Event::create($request->validated());

        foreach ($request->get('image_urls', []) as $image) {
            $event->photos()->create(['url' => $image]);
        }

        $this->tagService->updateEventTags($event, $request->get('tags', []));

        return $event->fresh($this->relations);
    }

    public function show(EventShowRequest $request, Event $event)
    {
        $event->increment('views');

        return $event->fresh(array_merge($this->relations, ['comments.photos']));
    }

    public function update(EventUpdateRequest $request, Event $event)
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

    public function destroy(EventDestroyRequest $request, Event $event)
    {
        $this->authorize('delete', $event);

        $event->delete();

        return $event->id;
    }
}
