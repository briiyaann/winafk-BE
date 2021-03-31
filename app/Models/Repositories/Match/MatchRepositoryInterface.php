<?php


namespace App\Models\Repositories\Match;


interface MatchRepositoryInterface
{
    public function store($data);

    public function getList();

    public function findMatch($id);

    public function getListByStatus($param, $status);

    public function addMatchTeam($data);

    public function addMatchSubMatch($data);

    public function getMatch($id);

    public function getSubmatches($match_id);

    public function getSubmatchByMatchidSubmatchid($match_id, $sub_match_id);

    public function updateMatchSubmatch($id, $data);

    public function updateMatch($id, $data);

    public function addMatchRoundWinner($data);

    public function getMatchByMatchIds($match_ids);

    public function getSettledMatches();

    public function getMatchWinner($match_id, $team_id);

    public function updateMatchWinner($id, $data);

    public function getMatchWinnerByMatch($match_id);
}
