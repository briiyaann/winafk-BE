<?php


namespace App\Models\Repositories\League;


use App\Models\Core\League;
use App\Models\Core\LeagueTeam;

class LeagueRepository implements LeagueRepositoryInterface
{
    public function store($data)
    {
        return League::create($data);
    }

    public function storeLeagueTeam($data)
    {
        return LeagueTeam::create($data);
    }

    public function getLeagueswithTeam()
    {
        return League::with('leagueTeam')->paginate();
    }

    public function getLeaguewithTeam($id)
    {
        return League::where('id', $id)->with('leagueTeam')->first();
    }
}
