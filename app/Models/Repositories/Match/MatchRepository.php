<?php


namespace App\Models\Repositories\Match;


use App\Models\Core\Game;
use App\Models\Core\MatchWinner;
use App\Models\Core\MatchSubmatch;
use App\Models\Core\MatchTeam;

class MatchRepository implements MatchRepositoryInterface
{
    public function store($data)
    {
        return Game::create($data);
    }

    public function getList()
    {

        return Game::with('matchSubmatch')->with('league')->with('matchTeams')->get();

    }

    public function getActiveMatches()
    {
        return Game::whereIn('status', ['ongoing', 'upcoming'])->with('matchSubmatch')->with('league')->with('matchTeams')->get();
    }

    public function getListByUser($user_id)
    {
//        return Game::where('')
    }

    public function findMatch($id)
    {
        return Game::where('id', $id)
            ->with('league')
            ->with('matchSubmatch')
            ->with('matchTeams')
            ->first();
    }

    public function getListByStatus($param, $status)
    {
        if($param) {
            return Game::where('game_type_id', $param)
                ->where('status', $status)
                ->with('league')
                ->with('matchSubmatch')
                ->with('matchTeams')
                ->orderBy('schedule', 'asc')
                ->get();
        } else {
            return Game::with('matchSubmatch')
                ->where('status', $status)
                ->with('league')
                ->with('matchTeams')
                ->orderBy('schedule', 'asc')
                ->get();
        }
    }

    public function showMatchSubmatch($id, $match_id)
    {
        return MatchSubGame::where('sub_match_id', $id)
                ->where('match_id', $match_id)
                ->first();
    }

    public function addMatchTeam($data)
    {
        return MatchTeam::create($data);
    }

    public function addMatchSubMatch($data)
    {
        return MatchSubGame::create($data);
    }

    public function getMatch($id)
    {
        return Game::where('id', $id)->first();
    }

    public function getSubmatches($match_id)
    {
        return MatchSubGame::where('match_id', $match_id)->get();
    }

    public function getSubmatchByMatchidSubmatchid($match_id, $sub_match_id)
    {
        return MatchSubGame::where('match_id', $match_id)
                ->where('sub_match_id', $sub_match_id)
                ->first();
    }

    public function updateMatchSubmatch($id, $data)
    {
        return MatchSubGame::where('id', $id)->update($data);
    }

    public function updateMatch($id, $data)
    {
        return Game::where('id', $id)->update($data);
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
        return Game::whereIn('id', $match_ids)
            ->with('league')
            ->with('matchSubmatch')
            ->with('matchTeams')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function getSettledMatches()
    {
        return Game::where('status', 'settled')
                ->orWhere('status', 'draw')
                ->with('game_type')
                ->with('matchSubmatch')
                ->orderBy('updated_at', 'desc')
                ->limit(15)
                ->get();
    }

    public function getMatchWithSubmatch() {
        return Game::with('matchSubmatch')->orderBy('created_at', 'desc')->get();
    }
}
