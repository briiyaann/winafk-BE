<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\CommonController;
use App\Http\Controllers\Controller;
use App\Services\BetServices;
use App\Services\MatchServices;
use App\Services\SubMatchServices;
use App\Services\TeamServices;
use App\Services\UserSerivces;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class MatchesController extends Controller
{
    public $common;

    public $match;

    public $team;

    public $submatch;

    public $bet;

    public $user;

    public function __construct(
        CommonController $common,
        MatchServices $match,
        SubMatchServices $submatch,
        TeamServices $team,
        BetServices $bet,
        UserSerivces $user
    ){
        $this->common = $common;
        $this->match = $match;
        $this->submatch = $submatch;
        $this->team = $team;
        $this->bet = $bet;
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    public function index(Request $request)
    {

        $matches = $this->match->getList();

        foreach ($matches as $key => $match)
        {
            $teams = [];

            foreach($match['matchTeams'] as $match_team) {
                $mt = $this->team->show($match_team['team_id']);

                array_push($teams, $mt);
            }

            unset($matches[$key]['matchTeams']);
            $matches[$key]['teams'] = $teams;


            foreach($match['matchSubmatch'] as $skey => $submatch) {
                $sb = $this->submatch->show($submatch['sub_match_id']);

                $matches[$key]['matchSubmatch'][$skey]['sub_match_detail'] = $sb;
            }

        }

        return $this->common->returnSuccessWithData($matches);
    }

    public function myPredictions()
    {
        $user = auth('api')->user();
        $user_id = $user ? $user->id : null;

        $match_id_from_bets = $this->bet->getBetByUserIdOnly($user_id)->toArray();

        $match_ids = [];
        foreach($match_id_from_bets as $key => $value) {
            array_push($match_ids, $value['match_id']);
        }

        $matches = $this->match->getMatchByMatchIds($match_ids)->toArray();

        foreach ($matches as $key => $match)
        {
            foreach($match['match_submatch'] as $skey => $submatch) {
                //remove bets from odds
                foreach($submatch['odds'] as $okey => $odd) {
                    unset($matches[$key]['match_submatch'][$skey]['odds'][$okey]->bets);

                }
                $sb = $this->submatch->show($submatch['sub_match_id']);
                $bet = $this->bet->getBetsBySubMatchByUserByMatch($submatch['sub_match_id'], $user_id, $match['id']);
                $matches[$key]['match_submatch'][$skey]['bet'] = $bet;
                $matches[$key]['match_submatch'][$skey]['sub_match_detail'] = $sb;
            }

            $teams = [];

            foreach($match['match_teams'] as $match_team) {
                $mt = $this->team->show($match_team['team_id'])->toArray();
                $odd = $this->submatch->getSingleOdds(1, $match['id'], $mt['id'])->toArray();

                $mt['percentage'] = $odd['percentage'];
                array_push($teams, $mt);
            }

            unset($matches[$key]['match_teams']);
            $matches[$key]['teams'] = $teams;
        }

        return $this->common->returnSuccessWithData($matches);
    }

    public function getMatch($id)
    {
        $user = auth('api')->user();
        $match = $this->match->findMatch($id)->toArray();
        $user_id = $user ? $user->id : null;

        foreach($match['match_submatch'] as $skey => $submatch) {
            //remove bets from odds
            foreach($submatch['odds'] as $okey => $odd) {
                if(!$user) {
                    unset($match['match_submatch'][$skey]['odds'][$okey]->bets);
                } else if($user && $user->user_role != 3) {
                    unset($match['match_submatch'][$skey]['odds'][$okey]->bets);
                }
            }

            $sb = $this->submatch->show($submatch['sub_match_id']);
            $bet = $this->bet->getBetsBySubMatchByUserByMatch($submatch['sub_match_id'], $user_id, $match['id']);
            $match['match_submatch'][$skey]['bet'] = $bet;
            $match['match_submatch'][$skey]['sub_match_detail'] = $sb;

        }

        $teams = [];

        foreach($match['match_teams'] as $match_team) {
            $mt = $this->team->show($match_team['team_id'])->toArray();
            $odd = $this->submatch->getSingleOdds(1, $match['id'], $mt['id'])->toArray();

            $mt['percentage'] = $odd['percentage'];
            array_push($teams, $mt);
        }

        unset($match['match_teams']);
        $match['teams'] = $teams;

        return $this->common->returnSuccessWithData($match);
    }

    public function getMatches(Request $request, $status)
    {
        $user = auth('api')->user();
        $user_id = $user ? $user->id : null;

        $param = $request->input('type');

        $param = (!$param || $param == 0) ? null : $param;

        $matches = $this->match->getListByStatus($param, $status)->toArray();

        foreach ($matches as $key => $match)
        {
            foreach($match['match_submatch'] as $skey => $submatch) {
                //remove bets from odds
                foreach($submatch['odds'] as $okey => $odd) {
                    unset($matches[$key]['match_submatch'][$skey]['odds'][$okey]->bets);

                }
                $sb = $this->submatch->show($submatch['sub_match_id']);
                $bet = $this->bet->getBetsBySubMatchByUserByMatch($submatch['sub_match_id'], $user_id, $match['id']);
                $matches[$key]['match_submatch'][$skey]['bet'] = $bet;
                $matches[$key]['match_submatch'][$skey]['sub_match_detail'] = $sb;
            }

            $teams = [];

            foreach($match['match_teams'] as $match_team) {
                $mt = $this->team->show($match_team['team_id'])->toArray();
                $odd = $this->submatch->getSingleOdds(1, $match['id'], $mt['id'])->toArray();

                $mt['percentage'] = $odd['percentage'];
                array_push($teams, $mt);
            }

            unset($matches[$key]['match_teams']);
            $matches[$key]['teams'] = $teams;
        }

        return $this->common->returnSuccessWithData($matches);
    }

    public function getMatchesByUser($user_id)
    {

        $matches = $this->match->getListByUser($user_id)->toArray();

        foreach ($matches as $key => $match)
        {
            foreach($match['match_submatch'] as $skey => $submatch) {
                //remove bets from odds
                foreach($submatch['odds'] as $okey => $odd) {
                    unset($matches[$key]['match_submatch'][$skey]['odds'][$okey]->bets);

                }
                $sb = $this->submatch->show($submatch['sub_match_id']);
                $bet = $this->bet->getBetsBySubMatchByUserByMatch($submatch['sub_match_id'], $user_id, $match['id']);
                $matches[$key]['match_submatch'][$skey]['bet'] = $bet;
                $matches[$key]['match_submatch'][$skey]['sub_match_detail'] = $sb;
            }

            $teams = [];

            foreach($match['match_teams'] as $match_team) {
                $mt = $this->team->show($match_team['team_id'])->toArray();
                $odd = $this->submatch->getSingleOdds(1, $match['id'], $mt['id'])->toArray();

                $mt['percentage'] = $odd['percentage'];
                array_push($teams, $mt);
            }

            unset($matches[$key]['match_teams']);
            $matches[$key]['teams'] = $teams;
        }

        return $this->common->returnSuccessWithData($matches);
    }

    public function getSubmatches($id)
    {
        $match = $this->match->getMatch($id);
        $sub_matches = $this->match->getSubmatches($id);
        foreach ($sub_matches as $key => $sub_match) {
            $sub = $this->submatch->show($sub_match->sub_match_id);

            $sub_matches[$key]['data'] = $sub;
        }
        $return_data = [
            'match' => $match,
            'sub_match' => $sub_matches
        ];

        return $this->common->returnSuccessWithData($return_data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'game_type_id' => 'required',
            'league_id' => 'required',
            'schedule' => 'required',
            'label' => 'required',
            'fee' => 'required',
            'match_count' => 'required',
            'teams' => 'required|array|min:2',
            'sub_matches' => 'required|array|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            $return_err = [];
            foreach ($validator->errors()->toArray() as $key => $value) {
                $return_err[$key] = $value[0];
            }

            return $this->common->returnWithErrors($return_err);
        } else {
            $match_data = [
                'name' => $request->get('name'),
                'game_type_id' => $request->get('game_type_id'),
                'league_id' => $request->get('league_id'),
                'schedule' => \DateTime::createFromFormat('D M d Y H:i:s e+', $request->get('schedule')),
                'fee' => $request->get('fee'),
                'match_count' => $request->get('match_count'),
                'label' => $request->get('label'),
                'status' => 'upcoming'
            ];

            $match = $this->match->store($match_data);

            if($match) {
                $teams = $request->get('teams');
                foreach ($teams as $team) {
                    $team_data = [
                        'match_id' => $match->id,
                        'team_id' => $team
                    ];
                    $this->match->addMatchTeam($team_data);
                }

                $sub_matches = $request->get('sub_matches');

                foreach ($sub_matches as $sub_match) {
                    $submatch = $this->submatch->show($sub_match);

                    $sub_data = [
                        'match_id' => $match->id,
                        'sub_match_id' => $sub_match,
                        'round' => $submatch->round,
                        'status' => 'open'
                    ];

                    //add odds per team
                    $odds_data = [
                        'sub_match_id' => $sub_match,
                        'match_id' => $match->id,
                        'bets' => 0,
                        'percentage' => 0,
                        'odds' => 0
                    ];

                    $this->addSubmatchOdds($odds_data, $teams);

                    $this->match->addMatchSubMatch($sub_data);
                }

                $new_match = $this->match->getMatch($match->id);

                return $this->common->returnSuccessWithData($new_match);
            } else {
                return $this->common->createErrorMsg('general', 'Something is Wrong.');
            }
        }
    }

    public function addSubmatchOdds($data, $teams)
    {
        foreach($teams as $team)
        {
            $data['team_id'] = $team;

            $save = $this->submatch->addSubmatchOdds($data);
        }

        return true;
    }

    public function removeAdminBets($user, $match, $cur_round)
    {
        $coins = $user->coins;

        $bets = $this->bet->getBettsByUserByMatch($user->id, $match->id);

        foreach ($bets as $bet) {
            $sub_match = $this->submatch->show($bet->sub_match_id);

            if(!$cur_round && $sub_match->round > 1) {
                continue;
            } elseif($cur_round && $sub_match->round != intval($cur_round)) {
                continue;
            }

            $bet = $this->bet->findBet($bet->id);

            $coins = $coins + intval($bet->amount);

            $user_data = [
                'coins' => $coins
            ];

            $this->user->updateUser($user->id, $user_data);

            $sub_data = [
                'amount' => 0,
                'sub_match_id' => $bet->sub_match_id,
                'match_id' => $match->id
            ];

            $update = $this->bet->updateBet($bet->id, $sub_data);

            if($update) {
                $odd = $this->submatch->getSingleOdds($bet->sub_match_id, $match->id , $bet->team_id);

                $odd_bet = intval($odd->bets);

                $odd_bet = $odd_bet - $bet->amount;

                $this->submatch->updateOdds(['bets' => intval($odd_bet)], $odd->id);

                //update odds
                $this->submatch->calculateOdds($sub_data);
            }
        }
    }

    public function startMatch(Request $request, $id)
    {
        $match = $this->match->getMatch($id);

        //require round number before starting the match
        $round = $request->get('round');
        if(!$match) return $this->common->createErrorMsg('no_match', 'Match not found');

        //remove bets from mm
        $mm_users = $this->user->findMMUsers();

        foreach($mm_users as $mm_user) {
            $this->removeAdminBets($mm_user, $match, $round);
        }

        $sub_matches = $this->match->getSubmatches($id);

        if($match->status == 'ongoing')
        {
            if(!$round) return $this->common->createErrorMsg('no_round', 'Round is required');

            foreach ($sub_matches as $sub_match)
            {
                if($sub_match->round == $round) {
                    $this->processStart($sub_match);
                }
            }

            $match_data = [
                'current_round' => $round,
                'status_label' => 'Game ' . $round . ' has started.',
                'ended_round' => null
            ];

            $this->match->updateMatch($id, $match_data);

            return $this->common->returnSuccessWithData(['success' => true]);
        } else {
            //loop over submatches and determine of invalid base on odds
            foreach ($sub_matches as $sub_match)
            {
                if($sub_match->round <= 1) {
                    $this->processStart($sub_match);
                }
            }

            $match_data = [
                'status' => 'ongoing',
                'status_label' => 'Game 1 started.',
                'current_round' => '1'
            ];

            $this->match->updateMatch($id, $match_data);

            return $this->common->returnSuccessWithData(['success' => true]);
        }
    }

    public function processStart($sub_match)
    {
        $is_valid = true;

        foreach ($sub_match->odds as $odd) {
            if(intval($odd->bets) == 0)
                $is_valid = false;
        }

        if(!$is_valid) {
            $refund = $this->refundPlayer($sub_match);

            //invalidate
            $m_submatch = [
                'status' => 'invalid'
            ];

            $this->match->updateMatchSubmatch($sub_match->id, $m_submatch);
        } else {
            //update to ongoing
            $m_submatch = [
                'status' => 'ongoing'
            ];

            $this->match->updateMatchSubmatch($sub_match->id, $m_submatch);
        }
    }

    public function refundPlayer($submatch)
    {
        $bets = $this->bet->getBetsByMatchSubmatch($submatch->match_id, $submatch->sub_match_id);

        foreach($bets as $bet)
        {
            $amount = $bet->amount;
            $user_id = $bet->user_id;

            $user = $this->user->findUser($user_id);

            if($user) {
                $cur_amount = intval($user->coins);
                $update_data = [
                    'coins' => $cur_amount + $amount
                ];

                $this->user->updateUser($user_id, $update_data);
            }

            $this->bet->delete($bet->id);
        }

        return true;
    }

    public function cancelMatch($match_id)
    {
        $sub_matches = $this->match->getSubmatches($match_id);

        foreach ($sub_matches as $sub_match) {
            $this->refundPlayer($sub_match);
        }

        $update_match = [
            'status' => 'cancelled'
        ];

        $this->match->updateMatch($match_id, $update_match);

        return $this->common->returnSuccessWithData(['success' => true]);
    }

    public function endSubMatch(Request $request, $match_id) {
        $winners = $request->get('winner');

        if(count($request->get('is_draw_invalid')) > 0) {
            //process and refund bets
            foreach($request->get('is_draw_invalid') as $idi_submatch) {
                $sub_match = $this->match->getSubmatchByMatchidSubmatchid($match_id, $idi_submatch['sub_match']);

                $this->refundPlayer($sub_match);

                $sub_data = [
                    'status' => $idi_submatch['definition']
                ];

                $this->match->updateMatchSubmatch($sub_match->id, $sub_data);
            }
        }

        //get all bets
        foreach($winners as $winner)
        {
            $this->processWinner($match_id, $winner);
        }

        return $this->common->returnSuccessWithData(['success' => true]);
    }

    public function endMatch(Request $request, $match_id)
    {
        $match_status = 'settled';
        $status = $request->get('status');

        $round = $request->get('round');

        if(!$round) return $this->common->createErrorMsg('round', 'Match round is required.');

        $winners = $request->get('winner');
        $match_winner = $this->match->getMatchWinner($match_id, $request->get('team_winner'));
        // add match winner
        $match_winner_data = [
            'match_id' => $match_id,
            'score' => $match_winner ? intval($match_winner->score) + 1 : 1,
            'team_id' => $request->get('team_winner')
        ];

        if($match_winner) {
            $this->match->updateMatchWinner($match_winner->id, $match_winner_data);
        } else {
            $this->match->addMatchRoundWinner($match_winner_data);
        }

        if(count($request->get('is_draw_invalid')) > 0) {

            //process and refund bets
            foreach($request->get('is_draw_invalid') as $idi_submatch) {
                $sub_match = $this->match->getSubmatchByMatchidSubmatchid($match_id, $idi_submatch['sub_match']);

                $this->refundPlayer($sub_match);

                $sub_data = [
                    'status' => $idi_submatch['definition']
                ];

                if($idi_submatch['sub_match'] == "1") {
                    $match_status = $idi_submatch['definition'];
                }

                $this->match->updateMatchSubmatch($sub_match->id, $sub_data);
            }
        }

        if($status == 'round')
        {
            //get all bets
            foreach($winners as $winner)
            {
                $this->processWinner($match_id, $winner);
            }

            $match_data = [
                'status_label' => 'End of Game ' . $round,
                'ended_round' => $round
            ];

            $this->match->updateMatch($match_id, $match_data);

            return $this->common->returnSuccessWithData(['success' => true]);
        } elseif($status == 'final')
        {
            foreach($winners as $winner)
            {
                $this->processWinner($match_id, $winner);
            }

            $winners = $this->match->getMatchWinnerByMatch($match_id);

            foreach($winners as $winner) {

            }

            $match_data = [
                'status_label' => 'Match ended',
                'status' => $match_status,
                'current_round' => null
            ];

            $this->match->updateMatch($match_id, $match_data);

            return $this->common->returnSuccessWithData(['success' => true]);
        }
    }

    public function processWinner($match_id, $winner)
    {
        $bets = $this->bet->getBetsByMatchSubmatchTeam($match_id, $winner['sub_match_id'], $winner['team_id']);

        foreach ($bets as $bet)
        {
            $amount = $bet->amount;
            $odds = $bet->odds->odds;

            $payout = intval($amount) * intval($odds);

            $user = $this->user->findUser($bet->user_id);

            $user_coin = $user->coins;

            $payout_data = [
                'coins' => $user_coin + $payout
            ];

            $this->user->updateUser($bet->user_id, $payout_data);
        }
        //settle submatch
        $sub_match_data = [
            'status' => 'settled',
            'team_winner' => $winner['team_id']
        ];

        $submatch = $this->match->getSubmatchByMatchidSubmatchid($match_id, $winner['sub_match_id']);

        $this->match->updateMatchSubmatch($submatch->id, $sub_match_data);
    }

    public function closeBet(Request $request) {
        $match_id = $request->get('match_id');
        $match = $this->match->getMatch($match_id);

        $next_round = !$match->cur_round ? 1 : $match->cur_round + 1;

        $sub_matches = $this->match->getSubmatches($match_id);

        foreach ($sub_matches as $sub_match) {
            if($next_round == 1) {
                if($sub_match->round <= 1 && $sub_match->status == 'open') {
                    //update to close
                    $sub_data = [
                        'status' => 'closed'
                    ];
                    $this->match->updateMatchSubmatch($sub_match->id, $sub_data);
                }
            } else {
                if($sub_match->round == 1 && $sub_match->status == 'open') {
                    //update to close
                    $sub_data = [
                        'status' => 'closed'
                    ];
                    $this->match->updateMatchSubmatch($sub_match->id, $sub_data);
                }
            }
        }

        return $this->common->returnSuccess();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return array
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'schedule' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            $return_err = [];
            foreach ($validator->errors()->toArray() as $key => $value) {
                $return_err[$key] = $value[0];
            }

            return $this->common->returnWithErrors($return_err);
        } else {
            $edit_data = [
                'schedule'=> \DateTime::createFromFormat('D M d Y H:i:s e+', $request->get('schedule'))
            ];

            $update = $this->match->updateMatch($id, $edit_data);

            if($update) {
                return $this->common->returnSuccess();
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function recentMatches()
    {
        $matches = $this->match->getSettledMatches()->toArray();

        foreach($matches as $key => $match) {
            $results = $this->match->getMatchWinnerByMatch($match['id']);

            $matches[$key]['scores'] = $results;
        }

        return $this->common->returnSuccessWithData($matches);
    }

    public function adminGetBets()
    {
        $matches = $this->match->getMatchWithSubmatch()->toArray();

        foreach($matches as $m_key => $match) {
            foreach($match['match_submatch'] as $s_key => $submatch) {
                $submatch = $this->submatch->show($submatch['sub_match_id']);

                $matches[$m_key]['match_submatch'][$s_key]['submatch'] = $submatch;
            }
        }

        return $this->common->returnSuccessWithData($matches);
    }
}
