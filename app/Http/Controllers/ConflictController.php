<?php

namespace App\Http\Controllers;

use App\Conflict;
use App\Http\Requests\ConflictRequest;
use Illuminate\Support\Facades\Auth;

class ConflictController extends Controller
{
    public function index()
    {
        return Conflict::with('user')->get();
    }

    public function store(ConflictRequest $request)
    {
        $this->authorize('create', Conflict::class);

        $data = $request->validated();

        $data['user_id'] = object_get(Auth::user(), 'id');

        $conflict = Conflict::create($data);

        return $conflict->fresh('user')->toArray();
    }

    public function show(Conflict $conflict)
    {
        $conflict->views += 1;

        $conflict->save();

        return $conflict->fresh('user');
    }

    public function update(ConflictRequest $request, Conflict $conflict)
    {
        $this->authorize('update', $conflict);

        $conflict->update($request->validated());

        return $conflict->fresh('user')->toArray();
    }

    public function destroy(Conflict $conflict)
    {
        $this->authorize('delete', $conflict);

        $conflict->delete();

        return $conflict->id;
    }
}
