<?php

namespace App\Http\Controllers;

use App\ConflictReason;
use App\ConflictResult;
use App\EventStatus;
use App\EventType;
use App\Industry;
use App\Region;

class RefController extends Controller
{
    public function index()
    {
        $eventTypes = EventType::get();
        $conflictReasons = ConflictReason::get();
        $conflictResults = ConflictResult::get();
        $eventStatuses = EventStatus::get();
        $industries = Industry::get();
        $regions = Region::get();

        return compact('eventTypes',
            'conflictReasons',
            'conflictResults',
            'eventStatuses',
            'industries',
            'regions');
    }
}
