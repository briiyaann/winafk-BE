<?php


namespace App\Services;


use App\Models\Repositories\League\LeagueRepositoryInterface;

class LeagueServices
{
    public $league;

    public function __construct(
        LeagueRepositoryInterface $league
    ){
        $this->league = $league;
    }

    public function store($data)
    {
        return $this->league->store($data);
    }

    public function storeLeagueTeam($data)
    {
        return $this->league->storeLeagueTeam($data);
    }

    public function getLeaguewithTeam($id)
    {
        return $this->league->getLeaguewithTeam($id);
    }

    public function getLeagueswithTeam()
    {
        return $this->league->getLeagueswithTeam();
    }

    public function getLeagueTeamByLeague($league_id)
    {
        return $this->league->getLeagueTeamByLeague($league_id);
    }
}
