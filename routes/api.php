<?php

use Illuminate\Support\Facades\Route;

Route::get('references', 'RefController@index');

Route::apiResource('conflict', 'ConflictController');