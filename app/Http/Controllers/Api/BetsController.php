<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\CommonController;
use App\Http\Controllers\Controller;
use App\Services\BetServices;
use App\Services\MatchServices;
use App\Services\SubMatchServices;
use App\Services\UserSerivces;
use Illuminate\Http\Request;
use Validator;

class BetsController extends Controller
{
    public $common;

    public $bets;

    public $user;

    public $match;

    public $submatch;

    public function __construct(
        CommonController $common,
        BetServices $bets,
        UserSerivces $user,
        MatchServices $match,
        SubMatchServices $submatch
    ) {
        $this->common = $common;
        $this->bets = $bets;
        $this->user = $user;
        $this->match = $match;
        $this->submatch = $submatch;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'user_id' => 'required',
            'match_id' => 'required',
            'sub_match' => 'required|array'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            $return_err = [];
            foreach ($validator->errors()->toArray() as $key => $value) {
                $return_err[$key] = $value[0];
            }

            return $this->common->returnWithErrors($return_err);
        } else {
            //check if match existed
            $match_id = $request->get('match_id');
            $match = $this->match->getMatch($match_id);
            if(!$match) return $this->common->createErrorMsg('no_match', 'Game does not exist.');

            $submatches = $request->get('sub_match');
            $user_details = $this->user->findUser($request->get('user_id'));

            $coins = $user_details->coins;
            $did_escape = false;
            $saved_count = 0;
            $has_ongoing_error = false;

            foreach ($submatches as $submatch)
            {
                $_submatch = $this->match->showMatchSubmatch($submatch['submatch'], $match_id);

                if($_submatch->status != 'open') {
                    $has_ongoing_error = true;
                    continue;
                }

                $sub_data = [
                    'user_id' => $request->get('user_id'),
                    'match_id' => $request->get('match_id'),
                    'team_id' => $submatch['team_id'],
                    'sub_match_id' => $submatch['submatch'],
                    'amount' => $submatch['amount']
                ];

                $bet = $submatch['bet_id'];

                if($bet) {
                    $bet = $this->bets->findBet($submatch['bet_id']);

                    $coins = $coins + intval($bet->amount);
                }

                if($submatch['amount'] > $coins) {
                    $did_escape = true;
                    continue;
                }

                $coins -= $submatch['amount'];

                $user_data = [
                    'coins' => $coins
                ];

                $this->user->updateUser($request->get('user_id'), $user_data);

                if(!$bet) {
                    $save = $this->bets->store($sub_data);
                    if($save->id) {
                        $saved_count++;
                    }
                } else {
                    $update = $this->bets->updateBet($bet->id, $sub_data);

                    if($update) {
                        $saved_count++;
                    }
                }

                $odd = $this->submatch->getSingleOdds($submatch['submatch'], $request->get('match_id'), $submatch['team_id']);

                $odd_bet = intval($odd->bets);

                if($bet) {
                    $odd_bet = $odd_bet - $bet->amount;
                }
                //increment bet
                $this->submatch->updateOdds(['bets' => intval($odd_bet) + intval($submatch['amount'])], $odd->id);

                //update odds
                $this->submatch->calculateOdds($sub_data);
            }

            if($did_escape) {
                return $this->common->createErrorMsg(
                    'bets_saved', 'Only ' . $saved_count . ' bet/s where saved due to insufficient afkcoins.'
                );
            } else {
                return $this->common->returnSuccessWithData(['success' => true]);
            }
        }
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
