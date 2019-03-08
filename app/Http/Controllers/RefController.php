<?php

namespace App\Http\Controllers;

use App\ConflictReason;
use App\ConflictResult;
use App\EventStatus;
use App\EventType;
use App\Http\Requests\Reference\ReferenceIndexRequest;
use App\Http\Resources\Reference\ReferenceResource;
use App\Industry;
use App\Region;
use App\VideoType;

class RefController extends Controller
{
    public function index(ReferenceIndexRequest $request, $locale)
    {
        $eventTypes = ReferenceResource::collection(EventType::get());
        $conflictReasons = ReferenceResource::collection(ConflictReason::get());
        $conflictResults = ReferenceResource::collection(ConflictResult::get());
        $eventStatuses = ReferenceResource::collection(EventStatus::get());
        $industries = ReferenceResource::collection(Industry::get());
        $regions = ReferenceResource::collection(Region::get());
        $videoTypes = VideoType::get();

        return compact('eventTypes',
            'conflictReasons',
            'conflictResults',
            'eventStatuses',
            'industries',
            'regions',
            'videoTypes');
    }
}
