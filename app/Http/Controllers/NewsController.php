<?php

namespace App\Http\Controllers;

use App\Entities\News;
use App\Http\Requests\News\NewsIndexRequest;
use App\Http\Requests\News\NewsStoreRequest;
use App\Http\Requests\News\NewsUpdateRequest;
use App\Http\Resources\News\NewsDetailResource;
use App\Http\Resources\News\NewsIndexResourceDoctrine;
use App\Services\NewsService;
use App\Services\TagService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

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
     * @return AnonymousResourceCollection
     */
    public function index(NewsIndexRequest $request)
    {
        $news = $this->service->index(
            array_get($request->validated(), 'filters',[]),
            array_get($request, 'per_page', 20),
            array_get($request, 'page', 1)
        );

        return NewsIndexResourceDoctrine::collection($news);
    }

    /**
     * @param NewsStoreRequest $request
     * @return NewsDetailResource
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws AuthorizationException
     */
    public function store(NewsStoreRequest $request)
    {
        $this->authorize('create', News::class);

        $news = $this->service->create($request->validated(), Auth::user());

        return NewsDetailResource::make($news);
    }

    /**
     * @param News $news
     * @return NewsDetailResource
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function show(News $news)
    {
        $this->service->incrementViews($news);

        return NewsDetailResource::make($news);
    }

    /**
     * @param NewsUpdateRequest $request
     * @param News $news
     * @return NewsDetailResource
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     * @throws AuthorizationException
     */
    public function update(NewsUpdateRequest $request, News $news)
    {
        $this->authorize('update', $news);

        $news = $this->service->update($news, $request->validated());

        return NewsDetailResource::make($news);
    }

    /**
     * @param News $news
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws AuthorizationException
     */
    public function destroy(News $news)
    {
        $this->authorize('delete', $news);

        $this->service->delete($news);
    }
}
