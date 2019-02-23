<?php

namespace App\Http\Controllers;

use App\Conflict;
use App\Http\Requests\Conflict\ConflictDestroyRequest;
use App\Http\Requests\Conflict\ConflictIndexRequest;
use App\Http\Requests\Conflict\ConflictShowRequest;
use App\Http\Requests\Conflict\ConflictStoreRequest;
use App\Http\Requests\Conflict\ConflictUpdateRequest;

class ConflictController extends Controller
{
    public function index(ConflictIndexRequest $request)
    {
        $fields = $request->get('brief') ?  ['id','title'] : '*';

        $data = Conflict::select($fields)
            ->orderBy('created_at','desc')
            ->get();

        return ['data' => $data];
    }

    public function store(ConflictStoreRequest $request)
    {
        $this->authorize('create', Conflict::class);

        $conflict = Conflict::create($request->validated());

        return $conflict;
    }

    public function show(ConflictShowRequest $request, Conflict $conflict)
    {
        return $conflict;
    }

    public function update(ConflictUpdateRequest $request, Conflict $conflict)
    {
        $this->authorize('update', $conflict);

        $conflict->update($request->validated());

        return $conflict;
    }

    public function destroy(ConflictDestroyRequest $request, Conflict $conflict)
    {
        $this->authorize('delete', $conflict);

        $conflict->delete();

        return $conflict->id;
    }
}
