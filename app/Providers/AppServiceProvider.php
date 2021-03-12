<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(
            'App\Models\Repositories\User\UserRepositoryInterface',
            'App\Models\Repositories\User\UserRepository'
        );

        $this->app->bind(
            'App\Models\Repositories\Team\TeamRepositoryInterface',
            'App\Models\Repositories\Team\TeamRepository'
        );

        $this->app->bind(
            'App\Models\Repositories\GameType\GameTypeRepositoryInterface',
            'App\Models\Repositories\GameType\GameTypeRepository'
        );

        $this->app->bind(
            'App\Models\Repositories\League\LeagueRepositoryInterface',
            'App\Models\Repositories\League\LeagueRepository'
        );

        $this->app->bind(
            'App\Models\Repositories\Match\MatchRepositoryInterface',
            'App\Models\Repositories\Match\MatchRepository'
        );

        $this->app->bind(
            'App\Models\Repositories\SubMatch\SubMatchRepositoryInterface',
            'App\Models\Repositories\SubMatch\SubMatchRepository'
        );

        $this->app->bind(
            'App\Models\Repositories\Topup\TopupRepositoryInterface',
            'App\Models\Repositories\Topup\TopupRepository'
        );

        $this->app->bind(
            'App\Models\Repositories\Bet\BetRepositoryInterface',
            'App\Models\Repositories\Bet\BetRepository'
        );

        $this->app->bind(
            'App\Models\Repositories\Withdraw\WithdrawRepositoryInterface',
            'App\Models\Repositories\Withdraw\WithdrawRepository'
        );
    }
}
