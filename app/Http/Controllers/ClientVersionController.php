<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientVersion\ClientVersionDestroyRequest;
use App\Http\Requests\ClientVersion\ClientVersionIndexRequest;
use App\Http\Requests\ClientVersion\ClientVersionStoreRequest;
use App\Http\Resources\ClientVersion\ClientVersionResource;
use App\Models\ClientVersion;
use App\Rules\UniqueVersion;
use App\Services\BusinessValidationService;

class ClientVersionController extends Controller
{
    public function index(ClientVersionIndexRequest $request, $locale)
    {
        $currentVersion = ClientVersion::where('version', $request->current_version)
            ->where('client_id', $request->client_id)
            ->firstOrFail();

        return ClientVersionResource::collection(
            ClientVersion::where('id','>', $currentVersion->id)
                ->orderBy('id','desc')
                ->get()
        );
    }

    public function store(ClientVersionStoreRequest $request, $locale, BusinessValidationService $businessValidationService)
    {
        $this->authorize('create', ClientVersion::class);

        //Проверяем, что еще не существует такой версии
        $businessValidationService->validate([
            new UniqueVersion($request->version, $request->client_id)
        ]);

        $data = $request->validated();

        $clientVersion = ClientVersion::create($data);

        return ClientVersionResource::make($clientVersion);
    }

    public function destroy(ClientVersionDestroyRequest $request, $locale, ClientVersion $clientVersion)
    {
        $this->authorize('delete', $clientVersion);

        $clientVersion->delete();

        return $clientVersion->id;
    }
}
