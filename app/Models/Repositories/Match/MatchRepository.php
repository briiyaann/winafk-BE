<?php


namespace App\Models\Repositories\Match;


use App\Models\Core\Match;
use App\Models\Core\MatchWinner;
use App\Models\Core\MatchSubmatch;
use App\Models\Core\MatchTeam;

class MatchRepository implements MatchRepositoryInterface
{
    public function store($data)
    {
        return Match::create($data);
    }

    public function getList()
    {

        return Match::with('matchSubmatch')->with('league')->with('matchTeams')->get();

    }

    public function getListByUser($user_id)
    {
//        return Match::where('')
    }

    public function findMatch($id)
    {
        return Match::where('id', $id)
            ->with('league')
            ->with('matchSubmatch')
            ->with('matchTeams')
            ->first();
    }

    public function getListByStatus($param, $status)
    {
        if($param) {
            return Match::where('game_type_id', $param)
                ->where('status', $status)
                ->with('league')
                ->with('matchSubmatch')
                ->with('matchTeams')
                ->orderBy('updated_at', 'desc')
                ->get();
        } else {
            return Match::with('matchSubmatch')
                ->where('status', $status)
                ->with('league')
                ->with('matchTeams')
                ->orderBy('updated_at', 'desc')
                ->get();
        }
    }

    public function addMatchTeam($data)
    {
        return MatchTeam::create($data);
    }

    public function addMatchSubMatch($data)
    {
        return MatchSubmatch::create($data);
    }

    public function getMatch($id)
    {
        return Match::where('id', $id)->first();
    }

    public function getSubmatches($match_id)
    {
        return MatchSubmatch::where('match_id', $match_id)->get();
    }

    public function getSubmatchByMatchidSubmatchid($match_id, $sub_match_id)
    {
        return MatchSubmatch::where('match_id', $match_id)
                ->where('sub_match_id', $sub_match_id)
                ->first();
    }

    public function updateMatchSubmatch($id, $data)
    {
        return MatchSubmatch::where('id', $id)->update($data);
    }

    public function updateMatch($id, $data)
    {
        return Match::where('id', $id)->update($data);
    }

    public function addMatchRoundWinner($data)
    {
        return MatchWinner::create($data);
    }

    public function getMatchWinnerByMatch($match_id)
    {
        return MatchWinner::where('match_id', $match_id)
                ->with('team')
                ->get();
    }

    public function updateMatchWinner($id, $data)
    {
        return MatchWinner::where('id', $id)
                ->update($data);
    }

    public function getMatchWinner($match_id, $team_id)
    {
        return MatchWinner::where('match_id', $match_id)
                    ->where('team_id', $team_id)
                    ->first();
    }

    public function getMatchByMatchIds($match_ids)
    {
        return Match::whereIn('id', $match_ids)
            ->with('league')
            ->with('matchSubmatch')
            ->with('matchTeams')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function getSettledMatches()
    {
        return Match::where('status', 'settled')
                ->with('game_type')
                ->orderBy('updated_at', 'desc')
                ->limit(15)
                ->get();
    }
}
