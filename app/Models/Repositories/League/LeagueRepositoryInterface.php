<?php


namespace App\Models\Repositories\League;


interface LeagueRepositoryInterface
{
    public function store($data);

    public function storeLeagueTeam($data);

    public function getLeagueswithTeam();

    public function getLeaguewithTeam($id);
}
