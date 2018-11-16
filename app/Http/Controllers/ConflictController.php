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
        return Conflict::create($request->validated())->toArray();
    }

    public function show(Conflict $conflict)
    {
        $conflict->views += 1;

        $conflict->save();

        return $conflict;
    }

    public function update(ConflictRequest $request, Conflict $conflict)
    {
        $conflict->update($request->validated());

        return $conflict->toArray();
    }

    public function destroy(Conflict $conflict)
    {
        $conflict ->delete();

        return $conflict->id;
    }
}
