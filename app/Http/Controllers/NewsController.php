<?php

namespace App\Http\Controllers;

use App\Http\Requests\News\NewsDestroyRequest;
use App\Http\Requests\News\NewsIndexRequest;
use App\Http\Requests\News\NewsShowRequest;
use App\Http\Requests\News\NewsStoreRequest;
use App\Http\Requests\News\NewsUpdateRequest;
use App\Http\Resources\News\NewsDetailResource;
use App\Http\Resources\News\NewsIndexResourceDoctrine;
use App\Models\News;
use App\Services\NewsService;
use App\Services\TagService;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{
    protected $tagService;
    protected $service;

    protected $relations = ['photos', 'videos', 'user', 'tags'];

    /**
     * NewsController constructor.
     * @param NewsService $service
     * @param TagService $tagService
     */
    public function __construct(NewsService $service, TagService $tagService)
    {
        $this->service = $service;
        $this->tagService = $tagService;
    }

    /**
     * @param NewsIndexRequest $request
     * @param $locale
     * @return AnonymousResourceCollection
     */
    public function index(NewsIndexRequest $request, $locale)
    {
        $news = $this->service->index(
            array_get($request->validated(), 'filters',[]),
            array_get($request, 'per_page', 20),
            array_get($request, 'page', 1)
        );

        return NewsIndexResourceDoctrine::collection($news);
    }

    public function store(NewsStoreRequest $request, $locale)
    {
        $this->authorize('create', News::class);

        //todo поставить драйвер аутентификации на рельсы doctrine
        $user = app('em')->find('App\Entities\User', Auth::user()->id);

        $news = $this->service->create($request->validated(), $user);

        return NewsDetailResource::make($news);
    }

    public function show(NewsShowRequest $request, $locale, News $news)
    {
        $news->increment('views');

        return NewsDetailResource::make($news);
    }

    public function update(NewsUpdateRequest $request, $locale, News $news)
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

        return NewsDetailResource::make($news);
    }

    public function destroy(NewsDestroyRequest $request, $locale, News $news)
    {
        $this->authorize('delete', $news);

        $news->delete();

        return $news->id;
    }
}
