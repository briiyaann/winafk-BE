<?php


namespace App\Services;


use App\Models\Repositories\SubMatch\SubMatchRepositoryInterface;

class SubMatchServices
{
    public $sub_match;

    public $match;

    public function __construct(
        SubMatchRepositoryInterface $sub_match,
        MatchServices $match
    ){
         $this->sub_match = $sub_match;
         $this->match = $match;
    }

    public function store($data)
    {
        return $this->sub_match->store($data);
    }

    public function show($id)
    {
        return $this->sub_match->show($id);
    }

    public function index()
    {
        return $this->sub_match->index();
    }

    public function addSubmatchOdds($data)
    {
        return $this->sub_match->addSubmatchOdds($data);
    }

    public function calculateOdds($data)
    {
        $submatch_odds = $this->sub_match->getOddsByTeam($data['sub_match_id'], $data['match_id'])->toArray();
        $match = $this->match->getMatch($data['match_id']);
        $match_fee = round(1 - $match->fee/100, 2);

        if(count($submatch_odds) == 2)
        {
            $total_bets = array_sum(array_map(function($data){
                return $data['bets'];
            }, $submatch_odds));

            $team_a = $submatch_odds[0];
            $team_b = $submatch_odds[1];

            $team_a_id = $team_a['team_id'];
            $team_b_id = $team_b['team_id'];

            $team_a_odds = intval($team_a['bets']) == 0 ? 0 : (round($team_b['bets']/$team_a['bets'], 2) * $match_fee) + 1;
            $team_b_odds = intval($team_b['bets']) == 0 ? 0 : (round($team_a['bets']/$team_b['bets'], 2) * $match_fee) + 1;

            $team_a_percentage = round((intval($team_a['bets'])/$total_bets) * 100, 2);
            $team_b_percentage = round((intval($team_b['bets'])/$total_bets) * 100, 2);

            foreach ($submatch_odds as $submatch_odd)
            {
                //for team A
                if($submatch_odd['team_id'] == $team_a_id) {
                    //update odds
                    $odds_data = [
                        'odds'          => $team_a_odds,
                        'percentage'    => $team_a_percentage
                    ];

                    $this->updateOdds($odds_data, $submatch_odd['id']);
                }

                //for team B
                if($submatch_odd['team_id'] == $team_b_id) {
                    //update odds
                    $odds_data = [
                        'odds'          => $team_b_odds,
                        'percentage'    => $team_b_percentage
                    ];
                    $this->updateOdds($odds_data, $submatch_odd['id']);
                }
            }
        }
    }

    public function updateOdds($data, $id)
    {
        return $this->sub_match->updateOdds($data, $id);
    }

    public function getSingleOdds($sub_match_id, $match_id, $team_id) {
        return $this->sub_match->getSingleOdds($sub_match_id, $match_id, $team_id);
    }
}
