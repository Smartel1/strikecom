<?php

namespace App\Http\Controllers;

use App\Entities\News;
use App\Exceptions\BusinessRuleValidationException;
use App\Http\Requests\News\NewsSetFavouriteRequest;
use App\Http\Requests\News\NewsDestroyRequest;
use App\Http\Requests\News\NewsIndexRequest;
use App\Http\Requests\News\NewsShowRequest;
use App\Http\Requests\News\NewsStoreRequest;
use App\Http\Requests\News\NewsUpdateRequest;
use App\Http\Resources\News\NewsDetailResource;
use App\Http\Resources\News\NewsIndexResource;
use App\Services\NewsService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\TransactionRequiredException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
    protected $service;

    protected $relations = ['photos', 'videos', 'user', 'tags'];

    /**
     * NewsController constructor.
     * @param NewsService $service
     */
    public function __construct(NewsService $service)
    {
        $this->service = $service;
    }

    /**
     * @param NewsIndexRequest $request
     * @param $locale
     * @return AnonymousResourceCollection
     * @throws QueryException
     */
    public function index(NewsIndexRequest $request, $locale)
    {
        $news = $this->service->index(
            array_get($request->validated(), 'filters',[]),
            array_get($request, 'per_page', 20),
            array_get($request, 'page', 1),
            $locale,
            Auth::user()
        );

        return NewsIndexResource::collection($news);
    }

    /**
     * @param NewsStoreRequest $request
     * @return NewsDetailResource
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws AuthorizationException
     * @throws BusinessRuleValidationException
     */
    public function store(NewsStoreRequest $request, $locale)
    {
        $this->authorize('create', News::class);

        $news = $this->service->create($request->validated(), $locale, Auth::user());

        return NewsDetailResource::make($news);
    }

    /**
     * @param NewsShowRequest $request
     * @param News $news
     * @return NewsDetailResource
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function show(NewsShowRequest $request, $locale, News $news)
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
     * @throws BusinessRuleValidationException
     */
    public function update(NewsUpdateRequest $request, $locale, News $news)
    {
        $this->authorize('update', $news);

        $news = $this->service->update($news, $request->validated(), $locale, Auth::user());

        return NewsDetailResource::make($news);
    }

    /**
     * @param NewsSetFavouriteRequest $request
     * @param $locale
     * @param News $news
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws AuthorizationException
     */
    public function setFavourite(NewsSetFavouriteRequest $request, $locale, News $news)
    {
        $this->authorize('setFavourite', News::class);

        $this->service->setFavourite($news, Auth::user(), $request->favourite);
    }

    /**
     * @param NewsDestroyRequest $request
     * @param News $news
     * @throws AuthorizationException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function destroy(NewsDestroyRequest $request, $locale, News $news)
    {
        $this->authorize('delete', $news);

        $this->service->delete($news);
    }
}
