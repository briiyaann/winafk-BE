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

        Route::get('{id}', 'Api\UsersController@getUser');

        Route::post('verify', 'Api\UsersController@verify');

        Route::post('forgot-password', 'Api\UsersController@forgotPassword');

        Route::get('verify-token/{token}', 'Api\UsersController@verifyToken');

        Route::post('password-reset', 'Api\UsersController@passwordReset');

        Route::post('login', 'Api\UsersController@login');
    });

    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('user/change-password', 'Api\UsersController@changePassword');

        Route::apiResource('teams', 'Api\TeamsController');

        Route::apiResource('leagues', 'Api\LeagueController');

        Route::post('matches/{id}/start', 'Api\MatchesController@startMatch');

        Route::post('matches/{id}/end', 'Api\MatchesController@endMatch');

        Route::post('matches/{id}/end-submatch', 'Api\MatchesController@endSubMatch');

        Route::post('matches/{id}/cancel', 'Api\MatchesController@cancelMatch');

        Route::apiResource('sub-matches', 'Api\SubMatchesController');

        Route::get('topups/user/{id}', 'Api\TopupsController@getList');

        Route::get('topups/admin/{status}', 'Api\TopupsController@getAllByStatus');

        Route::post('topups', 'Api\TopupsController@store');

        Route::put('topups/{id}', 'Api\TopupsController@update');

        Route::apiResource('bets', 'Api\BetsController');

        Route::apiResource('matches', 'Api\MatchesController');

        Route::post('matches/closebet', 'Api\MatchesController@closeBet');

        Route::get('user/{id}/get-coins', 'Api\UsersController@getCoins');

        Route::get('logout', 'Api\UsersController@logout');

        Route::post('withdraw', 'Api\WithdrawsController@store');

        Route::get('withdraw', 'Api\WithdrawsController@index');

        Route::get('withdraws/admin/{status}', 'Api\WithdrawsController@getAllByStatus');

        Route::post('withdraws/{id}/admin', 'Api\WithdrawsController@updateWithdraw');

        Route::get('matches/{user_id}', 'Api\MatchesController@getMatchesByUser');

        Route::get('my-predictions', 'Api\MatchesController@myPredictions');

        Route::get('leagues/{id}/teams', 'Api\LeagueController@getTeamsByLeague');
    });

    Route::apiResource('game-types', 'Api\GameTypesController');

    Route::get('get-matches/{status}', 'Api\MatchesController@getMatches');

    Route::get('matches/{id}/get-submatches', 'Api\MatchesController@getSubmatches');

    Route::get('recent-matches', 'Api\MatchesController@recentMatches');
});
