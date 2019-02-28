<?php

namespace App\Http\Controllers;

use App\Http\Requests\News\NewsDestroyRequest;
use App\Http\Requests\News\NewsIndexRequest;
use App\Http\Requests\News\NewsShowRequest;
use App\Http\Requests\News\NewsStoreRequest;
use App\Http\Requests\News\NewsUpdateRequest;
use App\News;
use App\Services\TagService;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
    protected $tagService;

    protected $relations = ['photos', 'videos', 'user', 'tags'];

    /**
     * NewsController constructor.
     * @param $tagService
     */
    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function index(NewsIndexRequest $request)
    {
        $tag_id = array_get($request->validated(), 'filters.tag_id');

        return News::when($tag_id, function ($query) use ($tag_id) {
            $query->whereHas('tags', function ($query) use ($tag_id) {
                $query->where('id', $tag_id);
            });
        })
            ->with($this->relations)
            ->orderBy('date', 'desc')
            ->paginate(array_get($request, 'per_page', 20));
    }

    public function store(NewsStoreRequest $request)
    {
        $this->authorize('create', News::class);

        $news = Auth::check()
            ? Auth::user()->news()->create($request->validated())
            : News::create($request->validated());

        foreach ($request->get('photo_urls', []) as $url) {
            $news->photos()->create([
                'url'           => $url,
            ]);
        }

        foreach (array_get($request->validated(), 'videos', []) as $video) {
            $news->videos()->create($video);
        }

        $this->tagService->updateNewsTags($news, $request->get('tags', []));

        return $news->fresh($this->relations);
    }

    public function show(NewsShowRequest $request, News $news)
    {
        $news->increment('views');

        return $news->fresh(array_merge($this->relations, ['comments.photos']));
    }

    public function update(NewsUpdateRequest $request, News $news)
    {
        $this->authorize('update', $news);

        $news->update($request->validated());

        $news->photos()->delete();
        $news->videos()->delete();

        foreach ($request->get('photo_urls', []) as $url) {
            $news->photos()->create([
                'url'           => $url,
            ]);
        }

        foreach (array_get($request->validated(), 'videos', []) as $video) {
            $news->videos()->create($video);
        }

        $this->tagService->updateNewsTags($news, $request->get('tags', []));

        return $news->fresh($this->relations);
    }

    public function destroy(NewsDestroyRequest $request, News $news)
    {
        $this->authorize('delete', $news);

        $news->delete();

        return $news->id;
    }
}
