<?php

use App\Http\Resources\User\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::group(['middleware'=>['tokenAuth','locale'], 'prefix'=>'{locale}'],function (){

    Route::get('reference', 'RefController@index');

    Route::get('reference-checksum', 'RefController@checkSum');

    Route::resource('client-version', 'ClientVersionController', ['only' => ['index', 'store', 'destroy']]);

    Route::apiResource('conflict', 'ConflictController');

    Route::apiResource('event', 'EventController');
    Route::post('event-list', 'EventController@index'); //запрос списка с параметрами в теле
    Route::post('event/{event}/favourite', 'EventController@setFavourite');

    Route::apiResource('event.comment', 'CommentController');
    Route::resource('event.comment.claim', 'ClaimController', ['only' => 'store']);

    Route::apiResource('news', 'NewsController');
    Route::post('news-list', 'NewsController@index'); //запрос списка с параметрами в теле
    Route::post('news/{news}/favourite', 'NewsController@setFavourite');

    Route::apiResource('news.comment', 'CommentController');
    Route::resource('news.comment.claim', 'ClaimController', ['only' => 'store']);

    Route::get('user', function(){
        return UserResource::make(Auth::user())->toArray(null);
    });
});


