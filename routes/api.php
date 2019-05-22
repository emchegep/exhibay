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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//authentication routes
Route::post('login', 'Api\UsersController@login');
Route::post('register', 'Api\UsersController@register');

Route::group(['middleware' => 'auth:api'], function() {

    Route::get('user', 'Api\UsersController@getUser');
    Route::get('user/logout', 'Api\UsersController@logout');
    Route::put('user/update', 'Api\UsersController@update');
    Route::delete('user/delete', 'Api\UsersController@destroy');

    //posters related routes
    Route::apiResource('posters', 'api\PosterController');
    Route::get('user/posters', 'api\PosterController@userPosters');
    Route::post('posters/{id}/comments','api\PosterController@storeComment');
    Route::get('posters/{id}/comments','api\PosterController@getComments');
    Route::put('posters/{posterId}/comments/{commentId}','api\PosterController@updateComment');
    Route::delete('posters/{id}/comment','api\PosterController@deleteComment');

});