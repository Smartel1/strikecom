<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::group(['middleware'=>['tokenAuth','locale'], 'prefix'=>'{locale}'],function (){

    Route::get('reference', 'RefController@index');

    Route::get('reference-checksum', 'RefController@checkSum');

    Route::resource('client-version', 'ClientVersionController', ['only' => ['index', 'store', 'destroy']]);

    Route::apiResource('conflict', 'ConflictController');

    Route::apiResource('event', 'EventController');
    Route::post('event-list', 'EventController@index'); //запрос списка с параметрами в теле

    Route::apiResource('event.comment', 'EventCommentController');

    Route::apiResource('news', 'NewsController');
    Route::post('news-list', 'NewsController@index'); //запрос списка с параметрами в теле

    Route::apiResource('news.comment', 'NewsCommentController');

    Route::get('user', function(){
        return Auth::user()->load('favouriteEvents', 'favouriteNews');
    });
});


