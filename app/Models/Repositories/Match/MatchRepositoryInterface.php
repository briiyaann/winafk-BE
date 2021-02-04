<?php


namespace App\Models\Repositories\Match;


interface MatchRepositoryInterface
{
    public function store($data);

    public function getList();

    public function getListByStatus($param, $status);

    public function addMatchTeam($data);

    public function addMatchSubMatch($data);

    public function getMatch($id);

    public function getSubmatches($match_id);

    public function updateMatchSubmatch($id, $data);

    public function updateMatch($id, $data);

    public function addMatchRoundWinner($data);
}
