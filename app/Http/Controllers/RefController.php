<?php

namespace App\Http\Controllers;

use App\ConflictReason;
use App\ConflictResult;
use App\EventStatus;
use App\EventType;
use App\Http\Requests\Reference\ReferenceIndexRequest;
use App\Industry;
use App\Region;
use App\VideoType;

class RefController extends Controller
{
    public function index(ReferenceIndexRequest $request)
    {
        $eventTypes = EventType::get();
        $conflictReasons = ConflictReason::get();
        $conflictResults = ConflictResult::get();
        $eventStatuses = EventStatus::get();
        $industries = Industry::get();
        $regions = Region::get();
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
