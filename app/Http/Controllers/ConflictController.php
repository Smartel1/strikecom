<?php

namespace App\Http\Controllers;

use App\Conflict;
use App\Http\Requests\ConflictRequest;

class ConflictController extends Controller
{
    public function index()
    {
        return Conflict::get();
    }

    public function store(ConflictRequest $request)
    {
        $this->authorize('create', Conflict::class);

        $conflict = Conflict::create($request->validated());

        return $conflict;
    }

    public function show(Conflict $conflict)
    {
        return $conflict;
    }

    public function update(ConflictRequest $request, Conflict $conflict)
    {
        $this->authorize('update', $conflict);

        $conflict->update($request->validated());

        return $conflict;
    }

    public function destroy(Conflict $conflict)
    {
        $this->authorize('delete', $conflict);

        $conflict->delete();

        return $conflict->id;
    }
}
