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

    public function getMatches(Request $request, $status)
    {
        $user = auth('api')->user();
        $user_id = $user ? $user->id : null;

        $param = $request->input('type');

        $param = (!$param || $param == 0) ? null : $param;

        $matches = $this->match->getListByStatus($param, $status);

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
                $bet = $this->bet->getBetsBySubMatchByUserByMatch($submatch['sub_match_id'], $user_id, $match['id']);
                $matches[$key]['matchSubmatch'][$skey]['bet'] = $bet;
                $matches[$key]['matchSubmatch'][$skey]['sub_match_detail'] = $sb;
            }

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

    public function startMatch(Request $request, $id)
    {
        $match = $this->match->getMatch($id);

        if($match->status == 'ongoing')
        {
            //require round number before starting the match
            $round = $request->get('round');

            if(!$round) return $this->common->createErrorMsg('no_round', 'Round is required');

            $match_data = [
                'current_round' => $round,
                'status_label' => 'Round ' . $round . 'has started.',
                'ended_round' => null
            ];

            $this->match->updateMatch($id, $match_data);

            return $this->common->returnSuccessWithData(['success' => true]);
        }

        if(!$match) return $this->common->createErrorMsg('no_match', 'Match not found');

        $sub_matches = $this->match->getSubmatches($id);

        //loop over submatches and determine of invalid base on odds
        foreach ($sub_matches as $sub_match)
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

        $match_data = [
            'status' => 'ongoing',
            'status_label' => 'round 1 started.',
            'current_round' => '1'
        ];

        $this->match->updateMatch($id, $match_data);

        return $this->common->returnSuccessWithData(['success' => true]);
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

        return $this->common->returnSuccessWithData(['success' => true]);
    }

    public function endMatch(Request $request, $match_id)
    {
        $status = $request->get('status');

        $round = $request->get('round');

        if(!$round) return $this->common->createErrorMsg('round', 'Match round is required.');

        $winners = $request->get('winner');

        // add match winner
        $match_winner_data = [
            'match_id' => $match_id,
            'round' => $round,
            'team_winner' => $request->get('team_winner')
        ];

        $this->match->addMatchRoundWinner($match_winner_data);

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

        if($status == 'round')
        {
            //get all bets
            foreach($winners as $winner)
            {
                $this->processWinner($match_id, $winner);
            }

            $match_data = [
                'status_label' => 'End of round ' . $round,
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

            $match_data = [
                'status_label' => 'Match ended',
                'status' => 'settled',
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
}
