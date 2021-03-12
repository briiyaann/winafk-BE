<?php


namespace App\Services;


use App\Models\Repositories\Bet\BetRepositoryInterface;

class BetServices
{
    public $bet;

    public function __construct(
        BetRepositoryInterface $bet
    ) {
        $this->bet = $bet;
    }

    public function store($data)
    {
        return $this->bet->store($data);
    }

    public function findBet($id)
    {
        return $this->bet->findBet($id);
    }

    public function updateBet($id, $data)
    {
        return $this->bet->updateBet($id, $data);
    }

    public function getBetsByMatch($match_id)
    {
        return $this->bet->getBetsByMatch($match_id);
    }

    public function getBetsByMatchSubmatch($match_id, $submatch_id)
    {
        return $this->bet->getBetsByMatchSubmatch($match_id, $submatch_id);
    }

    public function getBetsByMatchSubmatchTeam($match_id, $submatch_id, $team_id)
    {
        return $this->bet->getBetsByMatchSubmatchTeam($match_id, $submatch_id, $team_id);
    }

    public function getBetsBySubMatchByUserByMatch($sub_match_id, $user_id, $match_id)
    {
        return $this->bet->getBetsBySubMatchByUserByMatch($sub_match_id, $user_id, $match_id);
    }

    public function getBettsByUserByMatch($user_id, $match_id)
    {
        return $this->bet->getBettsByUserByMatch($user_id, $match_id);
    }

    public function delete($id)
    {
        return $this->bet->delete($id);
    }

    public function getBetByUserIdOnly($user_id)
    {
        return $this->bet->getBetByUserIdOnly($user_id);
    }
}
