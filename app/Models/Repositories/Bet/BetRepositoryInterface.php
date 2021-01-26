<?php


namespace App\Models\Repositories\Bet;


interface BetRepositoryInterface
{
    public function store($data);

    public function getBetsByMatch($match_id);

    public function getBetsByMatchSubmatch($match_id, $submatch_id);

    public function getBetsByMatchSubmatchTeam($match_id, $submatch_id, $team_id);

    public function delete($id);
}
