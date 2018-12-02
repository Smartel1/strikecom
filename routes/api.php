<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::group(['middleware'=>'tokenAuth'],function (){

    Route::get('references', 'RefController@index');

    Route::apiResource('conflict', 'ConflictController');

    Route::apiResource('conflict/{conflict}/comment', 'CommentController');

    Route::get('test', function(){
        return Auth::user();
    });
});


