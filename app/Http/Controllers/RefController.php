<?php

namespace App\Http\Controllers;

use App\ConflictReason;
use App\ConflictResult;
use App\ConflictStatus;
use App\ConflictType;
use App\Industry;
use App\Region;

class RefController extends Controller
{
    public function index()
    {
        $conflictTypes = ConflictType::get();
        $conflictReasons = ConflictReason::get();
        $conflictResults = ConflictResult::get();
        $conflictStatuses = ConflictStatus::get();
        $industries = Industry::get();
        $regions = Region::get();

        return compact('conflictTypes',
            'conflictReasons',
            'conflictResults',
            'conflictStatuses',
            'industries',
            'regions');
    }
}
