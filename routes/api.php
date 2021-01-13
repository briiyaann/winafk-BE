<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'v1'], function() {
    Route::group(['prefix' => 'user', 'middleware' => 'guest'], function() {
       Route::post('register', 'Api\UsersController@register');

       Route::post('verify', 'Api\UsersController@verify');

       Route::post('forgot-password', 'Api\UsersController@forgotPassword');

       Route::get('verify-token/{token}', 'Api\UsersController@verifyToken');

       Route::post('password-reset', 'Api\UsersController@passwordReset');

       Route::post('login', 'Api\UsersController@login');
    });

    Route::group(['middleware' => 'auth:api'], function() {
       Route::get('user/change-password', 'Api\UsersController@changePassword');

       Route::apiResource('teams', 'Api\TeamsController');

       Route::apiResource('game-types', 'Api\GameTypesController');

       Route::apiResource('leagues', 'Api\LeagueController');

       Route::apiResource('matches', 'Api\MatchesController');

       Route::apiResource('sub-matches', 'Api\SubMatchesController');

       Route::get('topups/user/{id}', 'Api\TopupsController@getList');

       Route::get('topups/admin/{status}', 'Api\TopupsController@getAllByStatus');

       Route::post('topups', 'Api\TopupsController@store');

       Route::put('topups/{id}', 'Api\TopupsController@update');

       Route::apiResource('bets', 'Api\BetsController');

       Route::get('logout', 'Api\UsersController@logout');
    });


});
