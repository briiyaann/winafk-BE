<?php


namespace App\Models\Repositories\Bet;


interface BetRepositoryInterface
{
    public function store($data);

    public function getBetsByMatch($match_id);
}
