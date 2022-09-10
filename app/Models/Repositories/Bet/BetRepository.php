<?php

namespace App\Models\Repositories\Bet;

use App\Models\Core\Bet;

class BetRepository implements BetRepositoryInterface
{
    public function store($data)
    {
        return Bet::create($data);
    }

    public function findBet($id)
    {
        return Bet::find($id);
    }

    public function updateBet($id, $data)
    {
        return Bet::find($id)->update($data);
    }

    public function getBetsByMatch($match_id)
    {
        return Bet::where('game_id', $match_id)->get();
    }

    public function getBetsByMatchSubmatch($match_id, $submatch_id)
    {
        return Bet::where('game_id', $match_id)
            ->where('sub_match_id', $submatch_id)
            ->get();
    }

    public function getBetsByMatchSubmatchTeam($match_id, $submatch_id, $team_id)
    {
        return Bet::where('game_id', $match_id)
            ->where('sub_match_id', $submatch_id)
            ->where('team_id', $team_id)
            ->get();
    }

    public function getBetsBySubMatchByUserByMatch($sub_match_id, $user_id, $match_id)
    {
        return Bet::where('sub_match_id', $sub_match_id)
                ->where('user_id', $user_id)
                ->where('game_id', $match_id)
                ->get();
    }

    public function getBettsByUserByMatch($user_id, $match_id)
    {
        return Bet::where('user_id', $user_id)
                ->where('game_id', $match_id)
                ->get();
    }

    public function delete($id)
    {
        return Bet::where('id', $id)->delete();
    }

    public function getBetByUserIdOnly($user_id)
    {
        return Bet::where('user_id', $user_id)
                ->where('amount', '!=', 0)
                ->groupBy('game_id')
                ->get('game_id');
    }
}
