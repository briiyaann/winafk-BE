<?php


namespace App\Models\Repositories\SubMatch;


use App\Models\Core\SubMatch;
use App\Models\Core\SubmatchOdd;

class SubMatchRepository implements SubMatchRepositoryInterface
{
    public function store($data)
    {
        return SubMatch::create($data);
    }

    public function show($id)
    {
        return SubMatch::where('id', $id)->first();
    }

    public function index()
    {
        return SubMatch::all();
    }

    public function addSubmatchOdds($data)
    {
        return SubmatchOdd::create($data);
    }

    public function getOddsByTeam($submatch_id, $match_id)
    {
        return SubmatchOdd::where('sub_match_id', $submatch_id)
                ->where('match_id', $match_id)
                ->get();
    }

    public function getSingleOdds($sub_match_id, $match_id, $team_id)
    {
        return SubmatchOdd::where('sub_match_id', $sub_match_id)
            ->where('match_id', $match_id)
            ->where('team_id', $team_id)
            ->first();
    }

    public function updateOdds($data, $id)
    {
        return SubmatchOdd::where('id', $id)->update($data);
    }
}
