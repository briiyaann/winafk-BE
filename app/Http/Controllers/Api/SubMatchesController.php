<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\CommonController;
use App\Http\Controllers\Controller;
use App\Services\SubMatchServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubMatchesController extends Controller
{
    public $common;

    public $sub_match;

    public function __construct(
        CommonController $common,
        SubMatchServices $sub_match
    ){
        $this->common = $common;
        $this->sub_match = $sub_match;
    }

    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    public function index()
    {
        $sub_matches = $this->sub_match->index();

        return $this->common->returnSuccessWithData($sub_matches);
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
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            $return_err = [];
            foreach ($validator->errors()->toArray() as $key => $value) {
                $return_err[$key] = $value[0];
            }

            return $this->common->returnWithErrors($return_err);
        } else {
            $data = $request->only(['name', 'round', 'points']);

            $data['round'] = isset($data['round']) ? $data['round'] : 0;

            $data['points'] = isset($data['points']) ? $data['points'] : 0;

            $add = $this->sub_match->store($data);

            return $this->common->returnSuccessWithData($add);
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
