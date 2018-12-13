<?php

namespace App\Http\Controllers;

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

    public function index(EventIndexRequest $request)
    {
        $conflict_id = array_get($request->validated(),'filters.conflict_id');

        return Event::when($conflict_id, function($query) use ($conflict_id){
                $query->where('conflict_id', $conflict_id);
            })
            ->with($this->relations)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function store(EventStoreRequest $request)
    {
        $this->authorize('create', Event::class);

        $event = Auth::user()->events()->create($request->validated());

        foreach ($request->get('image_urls', []) as $image) {
            $event->photos()->create(['url' => $image]);
        }

        $this->tagService->updateEventTags($event, $request->get('tags', []));

        return $event->fresh($this->relations);
    }

    public function show(EventShowRequest $request, Event $event)
    {
        $event->increment('views');

        return $event->fresh($this->relations);
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
