<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\CommonController;
use App\Http\Controllers\Controller;
use App\Models\Core\Team;
use App\Services\BetServices;
use App\Services\MatchServices;
use App\Services\SubMatchServices;
use App\Services\TeamServices;
use App\Services\UserSerivces;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

        $this->middleware('auth:api')->except('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    public function index(Request $request)
    {
        $param = $request->input('type');

        $param = (!$param || $param == 0) ? null : $param;

        $matches = $this->match->getList($param);

        foreach ($matches as $key => $match)
        {
            $teams = [];

            foreach($match['matchTeams'] as $match_team) {
                $mt = $this->team->show($match_team['team_id']);

                array_push($teams, $mt);
            }

            unset($matches[$key]['matchTeams']);
            $matches[$key]['teams'] = $teams;

            $sub_matches = [];

            foreach($match['matchSubmatch'] as $submatch) {
                $sb = $this->submatch->show($submatch['sub_match_id']);

                array_push($sub_matches, $sb);
            }

            unset($matches[$key]['matchSubmatch']);
            $matches[$key]['sub_matches'] = $sub_matches;
        }

        return $this->common->returnSuccessWithData($matches);
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
                'schedule' => Carbon::parse($request->get('schedule'))->format('Y-m-d H:i:s'),
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
                        'round' => $submatch->round
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
