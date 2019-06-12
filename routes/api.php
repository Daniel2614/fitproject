<?php

Route::middleware('auth:api')->group(function () {
    Route::get('/test', function () {
    });

    Route::get('/users', 'UserController@index');
});
