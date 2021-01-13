<?php


namespace App\Models\Repositories\Bet;


use App\Models\Core\Bet;

class BetRepository implements BetRepositoryInterface
{
    public function store($data)
    {
        return Bet::create($data);
    }

    public function getBetsByMatch($match_id)
    {
        return Bet::where('match_id', $match_id)->get();
    }
}
