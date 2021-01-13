<?php


namespace App\Models\Repositories\Match;


interface MatchRepositoryInterface
{
    public function store($data);

    public function getList($param);

    public function addMatchTeam($data);

    public function addMatchSubMatch($data);
}
