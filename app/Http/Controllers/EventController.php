<?php

namespace App\Http\Controllers;

use App\Event;
use App\Http\Requests\EventIndexRequest;
use App\Http\Requests\EventRequest;
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
            ->with($this->relations)->get();
    }

    public function store(EventRequest $request)
    {
        $this->authorize('create', Event::class);

        $event = Auth::user()->events()->create($request->validated());

        foreach ($request->get('image_urls', []) as $image) {
            $event->photos()->create(['url' => $image]);
        }

        $this->tagService->updateEventTags($event, $request->get('tags', []));

        return $event->fresh($this->relations);
    }

    public function show(Event $event)
    {
        return $event->fresh($this->relations);
    }

    public function update(EventRequest $request, Event $event)
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

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        $event->delete();

        return $event->id;
    }
}
