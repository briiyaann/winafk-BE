<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\CommonController;
use App\Http\Controllers\Controller;
use App\Services\LeagueServices;
use App\Services\TeamServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class LeagueController extends Controller
{
    public $common;

    public $league;

    public $team;

    public function __construct(
        CommonController $common,
        LeagueServices $league,
        TeamServices $team
    ){
        $this->common = $common;
        $this->league = $league;
        $this->team = $team;
    }

    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    public function index()
    {
        $leagues_team = $this->league->getLeagueswithTeam()->toArray();

        foreach ($leagues_team['data'] as $key => $league_team){
            $teams = [];

            foreach ($league_team['league_team'] as $team) {
                $t = $this->team->show($team['team_id']);
                array_push($teams, $t);
            }

            unset($leagues_team['data'][$key]['league_team']);
            $leagues_team['data'][$key]['teams'] = $teams;
        }

        return $this->common->returnSuccessWithData($leagues_team);
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
            'fee' => 'required',
            'background' => 'required|mimes:jpg,jpeg,gif,png',
            'banner' => 'required|mimes:jpg,jpeg,gif,png',
            'description' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            $return_err = [];
            foreach ($validator->errors()->toArray() as $key => $value) {
                $return_err[$key] = $value[0];
            }

            return $this->common->returnWithErrors($return_err);
        } else {
            $teams = $request->get('teams');

            if(!$teams) return $this->common->createErrorMsg('teams', 'Please select teams.');

            if(count($teams) <= 1) return $this->common->createErrorMsg('teams', 'Pleas select two or more teams.');

            if($request->file('background') && $request->file('banner')) {
                $upload_folder_background = 'leagues/background';
                $upload_folder_banner = 'leagues/banner';
                $background = $request->file('background');
                $banner = $request->file('banner');
                $background_path = $background->store($upload_folder_background, 'public');
                $banner_path = $banner->store($upload_folder_banner, 'public');

                $data = [
                    'name' => $request->get('name'),
                    'fee' => intval($request->get('fee')),
                    'background' => $background_path,
                    'banner' => $banner_path,
                    'betting_status' => 1,
                    'is_active' => 1,
                    'description' => $request->get('description')
                ];

                $league = $this->league->store($data);

                if($league->id) {
                    //add teams
                    foreach ($teams as $team)
                    {
                        $league_team_data = [
                            'league_id' => $league->id,
                            'team_id' => $team
                        ];

                        $league_team = $this->league->storeLeagueTeam($league_team_data);
                    }
                }
            }

            //get league with team
            $get_league_team = $this->league->getLeaguewithTeam($league->id);

            return $this->common->returnSuccessWithData($get_league_team);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return array
     */
    public function show($id)
    {
        $league_team = $this->league->getLeaguewithTeam($id)->toArray();
        $teams = [];
        foreach($league_team['league_team'] as $team) {
            $t = $this->team->show($team['team_id']);
            array_push($teams, $t);
        }

        unset($league_team['league_team']);

        $league_team['teams'] = $teams;

        return $this->common->returnSuccessWithData($league_team);
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
