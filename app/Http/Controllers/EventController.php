<?php

namespace App\Http\Controllers;

use App\Event;
use App\Http\Requests\Event\EventDestroyRequest;
use App\Http\Requests\Event\EventIndexRequest;
use App\Http\Requests\Event\EventStoreRequest;
use App\Http\Requests\Event\EventShowRequest;
use App\Http\Requests\Event\EventUpdateRequest;
use App\Http\Resources\Event\EventDetailResource;
use App\Http\Resources\Event\EventIndexResource;
use App\Services\TagService;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    protected $tagService;

    /**
     * EventController constructor.
     * @param $tagService
     */
    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function index(EventIndexRequest $request, $locale)
    {
        $tag_id = array_get($request->validated(),'filters.tag_id');
        $conflict_id = array_get($request->validated(),'filters.conflict_id');

        $events = Event::with(['photos', 'videos' ,'user', 'tags', 'conflict'])
            ->when($conflict_id, function($query) use ($conflict_id){
                $query->where('conflict_id', $conflict_id);
            })
            ->when($tag_id, function($query) use ($tag_id){
                $query->whereHas('tags', function($query) use ($tag_id){
                    $query->where('id', $tag_id);
                });
            })
            //Только локализованные записи
            ->when($locale !== 'all', function ($query) use ($locale){
                $query->whereNotNull("title_$locale")->whereNotNull("content_$locale");
            })
            ->orderBy('date', 'desc')
            ->paginate(array_get($request, 'per_page', 20));

        return EventIndexResource::collection($events);
    }

    public function store(EventStoreRequest $request, $locale)
    {
        $this->authorize('create', Event::class);

        $event = Auth::check()
            ? Auth::user()->events()->create($request->validated())
            : Event::create($request->validated());

        foreach (array_get($request->validated(), 'photo_urls', []) as $url) {
            $event->photos()->create([
                'url'           => $url,
            ]);
        }

        foreach (array_get($request->validated(), 'videos', []) as $video) {
            $event->videos()->create($video);
        }

        $this->tagService->updateEventTags($event, $request->get('tags', []));

        return EventDetailResource::make($event);
    }

    public function show(EventShowRequest $request, $locale, Event $event)
    {
        $event->increment('views');

        return EventDetailResource::make($event);
    }

    public function update(EventUpdateRequest $request, $locale, Event $event)
    {
        $this->authorize('update', $event);

        $event->update($request->validated());

        $event->photos()->delete();
        $event->videos()->delete();

        foreach (array_get($request->validated(), 'photo_urls', []) as $url) {
            $event->photos()->create([
                'url'           => $url,
            ]);
        }

        foreach (array_get($request->validated(), 'videos', []) as $video) {
            $event->videos()->create($video);
        }

        $this->tagService->updateEventTags($event, $request->get('tags', []));

        return EventDetailResource::make($event);
    }

    public function destroy(EventDestroyRequest $request, $locale, Event $event)
    {
        $this->authorize('delete', $event);

        $event->delete();

        return $event->id;
    }
}
