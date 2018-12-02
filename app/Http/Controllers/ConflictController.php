<?php

namespace App\Http\Controllers;

use App\Conflict;
use App\Http\Requests\ConflictRequest;
use Illuminate\Support\Facades\Auth;

class ConflictController extends Controller
{
    protected $relations = ['user', 'tags', 'conflictPhotos'];

    public function index()
    {
        return Conflict::with($this->relations)->get();
    }

    public function store(ConflictRequest $request)
    {
        $this->authorize('create', Conflict::class);

        $data = $request->validated();

        $conflict = Auth::user()->conflicts()->create(
            array_except($data, ['tags', 'image_urls'])
        );

        $conflict->syncTagsFromArray(array_get($data, 'tags', []));

        $conflict->syncImageUrlsFromArray(array_get($data, 'image_urls', []));

        return $conflict->fresh($this->relations)->toArray();
    }

    public function show(Conflict $conflict)
    {
        $conflict->views += 1;

        $conflict->save();

        return $conflict->fresh($this->relations);
    }

    public function update(ConflictRequest $request, Conflict $conflict)
    {
        $this->authorize('update', $conflict);

        $data = $request->validated();

        $conflict->update(
            array_except($data, ['tags', 'image_urls'])
        );

        $conflict->syncTagsFromArray(array_get($data, 'tags', []));

        $conflict->syncImageUrlsFromArray(array_get($data, 'image_urls', []));

        return $conflict->fresh($this->relations)->toArray();
    }

    public function destroy(Conflict $conflict)
    {
        $this->authorize('delete', $conflict);

        $conflict->delete();

        return $conflict->id;
    }
}
