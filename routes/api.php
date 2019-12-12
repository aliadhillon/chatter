<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('auth:api')->group(function() {
    Route::get('/messages', 'API\MessageController@index')->name('messages.index');
    Route::post('/messages', 'API\MessageController@store')->name('messages.store');
    Route::get('/poke/{user}', 'API\PokeController@poke')->name('poke');
});
