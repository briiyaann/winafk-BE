<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\CommonController;
use App\Http\Controllers\Controller;
use App\Services\GameTypeServices;
use Illuminate\Http\Request;

use Validator;

class GameTypesController extends Controller
{

    public $gameType;

    public $common;

    public function __construct(
        GameTypeServices $gameType,
        CommonController $common
    ){
        $this->gameType = $gameType;
        $this->common = $common;
    }

    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    public function index()
    {
        $game_types = $this->gameType->index();
        return $this->common->returnSuccessWithData($game_types);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        if(!$this->common->hasPermission('permissions.game_type.create')) {
            return response()->json('Unauthorized', 401);
        }

        $rules = [
            'name' => 'required',
            'banner' => 'required|mimes:jpg,jpeg,gif,png'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            $return_err = [];
            foreach ($validator->errors()->toArray() as $key => $value) {
                $return_err[$key] = $value[0];
            }

            return $this->common->returnWithErrors($return_err);
        } else {
            $path = '';
            if($request->file('banner')) {
                $upload_folder = 'game_types';
                $image = $request->file('banner');
                $path = $image->store($upload_folder, 'public');

            }

            $data = [
                'name' => $request->get('name'),
                'description' => $request->get('description'),
                'banner' => $path
            ];

            $type = $this->gameType->store($data);

            return $type ? $this->common->returnSuccessWithData($type) : $this->common->createErrorMsg('add_error', 'Something went wrong.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return response()->json('Unauthorized', 401);
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
        $type = $this->gameType->getGameType($id);

        if(!$type) return $this->common->createErrorMsg('no_game_type', 'Game type not found');

        $data = $request->all();

        unset($data['_method']);

        $update = $this->gameType->update($id, $data);

        return $update ? $this->common->returnSuccess() : $this->common->createErrorMsg('update_failed', 'Something went wrong.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return array
     */
    public function destroy($id)
    {
        $delete = $this->gameType->delete($id);

        return $delete ? $this->common->returnSuccess() : $this->common->createErrorMsg('delete_failed', 'Something went wrong.');
    }
}
