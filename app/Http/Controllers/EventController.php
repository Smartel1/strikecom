<?php

namespace App\Http\Controllers;

use App\Entities\Event;
use App\Exceptions\BusinessRuleValidationException;
use App\Http\Requests\Event\EventDestroyRequest;
use App\Http\Requests\Event\EventIndexRequest;
use App\Http\Requests\Event\EventStoreRequest;
use App\Http\Requests\Event\EventShowRequest;
use App\Http\Requests\Event\EventUpdateRequest;
use App\Http\Resources\Event\EventDetailResource;
use App\Http\Resources\Event\EventIndexResource;
use App\Services\EventService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\TransactionRequiredException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    protected $service;

    /**
     * EventController constructor.
     * @param EventService $service
     */
    public function __construct(EventService $service)
    {
        $this->service = $service;
    }

    /**
     * @param EventIndexRequest $request
     * @param $locale
     * @return AnonymousResourceCollection
     * @throws QueryException
     */
    public function index(EventIndexRequest $request, $locale)
    {
        $events = $this->service->index(
            array_get($request->validated(), 'filters',[]),
            array_get($request, 'per_page', 20),
            array_get($request, 'page', 1)
        );

        return EventIndexResource::collection($events);
    }

    /**
     * @param EventStoreRequest $request
     * @param $locale
     * @return EventDetailResource
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws AuthorizationException
     */
    public function store(EventStoreRequest $request, $locale)
    {
        $this->authorize('create', Event::class);

        $event = $this->service->create($request->validated(), Auth::user());

        return EventDetailResource::make($event);
    }

    /**
     * @param EventShowRequest $request
     * @param $locale
     * @param Event $event
     * @return EventDetailResource
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function show(EventShowRequest $request, $locale, Event $event)
    {
        $this->service->incrementViews($event);

        return EventDetailResource::make($event);
    }

    /**
     * @param EventUpdateRequest $request
     * @param $locale
     * @param Event $event
     * @return EventDetailResource
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     * @throws AuthorizationException
     */
    public function update(EventUpdateRequest $request, $locale, Event $event)
    {
        $this->authorize('update', $event);

        $event = $this->service->update($event, $request->validated());

        return EventDetailResource::make($event);
    }

    /**
     * @param EventDestroyRequest $request
     * @param $locale
     * @param Event $event
     * @throws AuthorizationException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws BusinessRuleValidationException
     */
    public function destroy(EventDestroyRequest $request, $locale, Event $event)
    {
        $this->authorize('delete', $event);

        $this->service->delete($event);
    }
}
