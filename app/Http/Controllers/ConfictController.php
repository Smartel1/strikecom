<?php

namespace App\Http\Controllers;

use App\Conflict;
use App\Http\Requests\ConflictStoreRequest;
use Illuminate\Http\Request;

class ConfictController extends Controller
{
    public function index()
    {
        return Conflict::get();
    }

    public function store(ConflictStoreRequest $request)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
