<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\CommonController;
use App\Http\Controllers\Controller;
use App\Services\TeamServices;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Validator;

class TeamsController extends Controller
{
    public $team;

    public  $common;

    public function __construct(
        TeamServices $team,
        CommonController $common
    ){
        $this->team = $team;
        $this->common = $common;
    }

    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    public function index()
    {
        $team = $this->team->getTeamsPaginate();

        return $this->common->returnSuccessWithData($team);
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
            'shortname' => 'required',
            'game_type_id' => 'required',
            'banner' => 'mimes:jpg,jpeg,gif,png',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            $return_err = [];
            foreach ($validator->errors()->toArray() as $key => $value) {
                $return_err[$key] = $value[0];
            }

            return $this->common->returnWithErrors($return_err);
        } else {
            if($request->file('banner')) {
                $upload_folder = 'teams';
                $image = $request->file('banner');
                $path = $image->store($upload_folder, 'public');

                $data = [
                    'name' => $request->get('name'),
                    'shortname' => $request->get('shortname'),
                    'banner' => $path,
                    'game_type_id' => $request->get('game_type_id')
                ];

                $team = $this->team->store($data);

                if($team) {
                    return $this->common->returnSuccessWithData($team);
                }
            }
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
        $team = $this->team->show($id);

        return $team
            ? $this->common->returnSuccessWithData($team)
            : $this->common->createErrorMsg('no_team', 'Team not found');
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
        $team = $this->team->show($id);

        if(!$team) return $this->common->createErrorMsg('no_team', 'Team not found');

        $data = $request->all();

        unset($data['_method']);

        if($request->file('banner')){
            $upload_folder = 'teams';
            $image = $request->file('banner');
            $path = $image->store($upload_folder, 'public');

            $data['banner'] = $path;
        }

        $update = $this->team->update($id, $data);

        return $update ? $this->common->returnSuccess() : $this->common->createErrorMsg('update_error', 'Something went wrong.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return array
     */
    public function destroy($id)
    {
        $team = $this->team->show($id);

        if($team) {
            $this->team->delete($id);

            return $this->common->returnSuccess();
        } else {
            return $this->common->createErrorMsg('no_team', 'Team not found');
        }
    }
}
