<?php

namespace App\Http\Controllers;

use App\Conflict;
use App\Http\Requests\ConflictRequest;
use Illuminate\Http\Request;

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

    public function show($id)
    {
        $conflict = Conflict::findOrFail($id);

        return $conflict;
    }

    public function update(ConflictRequest $request, $id)
    {
        $conflict = Conflict::findOrFail($id)->update($request->validated());

        return $conflict->toArray();
    }

    public function destroy($id)
    {
        $conflict = Conflict::findOrFail($id);

        return $conflict->delete();
    }
}
