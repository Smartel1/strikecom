<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::group(['middleware'=>'tokenAuth'],function (){

    Route::get('references', 'RefController@index');

    Route::apiResource('conflict', 'ConflictController');

    Route::apiResource('conflict.event', 'EventController');

    Route::apiResource('conflict.event.comment', 'EventCommentController');

    Route::apiResource('news', 'NewsController');

    Route::apiResource('news.comment', 'NewsCommentController');

    Route::get('test', function(){
        return Auth::user();
    });
});


