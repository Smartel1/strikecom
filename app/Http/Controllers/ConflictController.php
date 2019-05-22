<?php

namespace App\Http\Controllers;

use App\Entities\Conflict;
use App\Exceptions\BusinessRuleValidationException;
use App\Http\Requests\Conflict\ConflictDestroyRequest;
use App\Http\Requests\Conflict\ConflictIndexRequest;
use App\Http\Requests\Conflict\ConflictShowRequest;
use App\Http\Requests\Conflict\ConflictStoreRequest;
use App\Http\Requests\Conflict\ConflictUpdateRequest;
use App\Http\Resources\Conflict\ConflictBriefIndexResource;
use App\Http\Resources\Conflict\ConflictDetailResource;
use App\Http\Resources\Conflict\ConflictIndexResource;
use App\Services\ConflictService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;

class ConflictController extends Controller
{
    /**
     * @var ConflictService
     */
    protected $conflictService;

    /**
     * ConflictController constructor.
     * @param ConflictService $conflictService
     */
    public function __construct(ConflictService $conflictService)
    {
        $this->conflictService = $conflictService;
    }

    /**
     * @param ConflictIndexRequest $request
     * @param $locale
     * @return AnonymousResourceCollection
     */
    public function index(ConflictIndexRequest $request, $locale)
    {
        $conflictsCollection = $this->conflictService->index(Arr::get($request->validated(), 'filters',[]));

        if ($request->get('brief')) {
            return ConflictBriefIndexResource::collection($conflictsCollection);
        }

        return ConflictIndexResource::collection($conflictsCollection);
    }

    /**
     * @param ConflictStoreRequest $request
     * @param $locale
     * @return ConflictDetailResource
     * @throws AuthorizationException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function store(ConflictStoreRequest $request, $locale)
    {
        $this->authorize('create', Conflict::class);

        $conflict = $this->conflictService->create($request->validated());

        return ConflictDetailResource::make($conflict);
    }

    /**
     * @param ConflictShowRequest $request
     * @param $locale
     * @param Conflict $conflict
     * @return ConflictDetailResource
     */
    public function show(ConflictShowRequest $request, $locale, Conflict $conflict)
    {
        return ConflictDetailResource::make($conflict);
    }

    /**
     * @param ConflictUpdateRequest $request
     * @param $locale
     * @param Conflict $conflict
     * @return ConflictDetailResource
     * @throws AuthorizationException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function update(ConflictUpdateRequest $request, $locale, Conflict $conflict)
    {
        $this->authorize('update', $conflict);

        $conflict = $this->conflictService->update($conflict, $request->validated());

        return ConflictDetailResource::make($conflict);
    }

    /**
     * @param ConflictDestroyRequest $request
     * @param $locale
     * @param Conflict $conflict
     * @throws AuthorizationException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws BusinessRuleValidationException
     */
    public function destroy(ConflictDestroyRequest $request, $locale, Conflict $conflict)
    {
        $this->authorize('delete', $conflict);

        $this->conflictService->delete($conflict);
    }
}
