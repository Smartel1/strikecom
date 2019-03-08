<?php

namespace App\Http\Controllers;

use App\Conflict;
use App\Http\Requests\Conflict\ConflictDestroyRequest;
use App\Http\Requests\Conflict\ConflictIndexRequest;
use App\Http\Requests\Conflict\ConflictShowRequest;
use App\Http\Requests\Conflict\ConflictStoreRequest;
use App\Http\Requests\Conflict\ConflictUpdateRequest;
use App\Http\Resources\Conflict\ConflictBriefIndexResource;
use App\Http\Resources\Conflict\ConflictDetailResource;
use App\Http\Resources\Conflict\ConflictIndexResource;

class ConflictController extends Controller
{
    public function index(ConflictIndexRequest $request, $locale)
    {
        if ($request->get('brief')) {
            return ConflictBriefIndexResource::collection(Conflict::get());
        }

        return ConflictIndexResource::collection(Conflict::get());
    }

    public function store(ConflictStoreRequest $request, $locale)
    {
        $this->authorize('create', Conflict::class);

        $data = $request->validated();

        $conflict = Conflict::create($data);

        return ConflictDetailResource::make($conflict);
    }

    public function show(ConflictShowRequest $request, $locale, Conflict $conflict)
    {
        return ConflictDetailResource::make($conflict);
    }

    public function update(ConflictUpdateRequest $request, $locale, Conflict $conflict)
    {
        $this->authorize('update', $conflict);

        $data = $request->validated();

        $conflict->update($data);

        return ConflictDetailResource::make($conflict);
    }

    public function destroy(ConflictDestroyRequest $request, $locale, Conflict $conflict)
    {
        $this->authorize('delete', $conflict);

        $conflict->delete();

        return $conflict->id;
    }
}
