<?php

namespace App\Http\Controllers;

use App\Entities\ClientVersion;
use App\Exceptions\BusinessRuleValidationException;
use App\Http\Requests\ClientVersion\ClientVersionDestroyRequest;
use App\Http\Requests\ClientVersion\ClientVersionIndexRequest;
use App\Http\Requests\ClientVersion\ClientVersionStoreRequest;
use App\Http\Resources\ClientVersion\ClientVersionResource;
use App\Services\ClientVersionService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClientVersionController extends Controller
{
    protected $service;

    /**
     * ClientVersionController constructor.
     * @param ClientVersionService $service
     */
    public function __construct(ClientVersionService $service)
    {
        $this->service = $service;
    }

    /**
     * @param ClientVersionIndexRequest $request
     * @param $locale
     * @return AnonymousResourceCollection
     * @throws BusinessRuleValidationException
     */
    public function index(ClientVersionIndexRequest $request, $locale)
    {
        $newVersions = $this->service->getNewVersions($request->client_id, $request->current_version);

        return ClientVersionResource::collection(collect($newVersions));
    }

    /**
     * @param ClientVersionStoreRequest $request
     * @param $locale
     * @return ClientVersionResource
     * @throws BusinessRuleValidationException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws AuthorizationException
     */
    public function store(ClientVersionStoreRequest $request, $locale)
    {
        $this->authorize('create', ClientVersion::class);

        $clientVersion = $this->service->create($request->validated());

        return ClientVersionResource::make($clientVersion);
    }

    /**
     * @param ClientVersionDestroyRequest $request
     * @param $locale
     * @param ClientVersion $clientVersion
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws AuthorizationException
     */
    public function destroy(ClientVersionDestroyRequest $request, $locale, ClientVersion $clientVersion)
    {
        $this->authorize('delete', $clientVersion);

        $this->service->delete($clientVersion);
    }
}
