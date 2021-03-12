<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\CommonController;
use App\Http\Controllers\Controller;
use App\Services\WithdrawServices;
use Illuminate\Http\Request;
use Validator;

class WithdrawsController extends Controller
{
    public $withdraw;

    public $common;

    public function __construct(
        CommonController $common,
        WithdrawServices $withdraw
    ) {
        $this->common = $common;
        $this->withdraw = $withdraw;
    }

    public function index()
    {
        $user = auth('api')->user()->id;
        $withdraws = $this->withdraw->getList($user);

        return $this->common->returnSuccessWithData($withdraws);
    }

    public function store(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'amount' => 'required',
            'phone' => 'required_without:email',
            'email' => 'required_without:phone|email'
        ];

        /** @var Request $validator */
        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            $return_err = [];
            foreach ($validator->errors()->toArray() as $key => $value) {
                $return_err[$key] = $value[0];
            }

            return $this->common->returnWithErrors($return_err);
        } else {
            $data = [
                'user_id' => $request->get('user_id'),
                'amount' => $request->get('amount'),
                'phone' => $request->get('phone'),
                'email' => $request->get('email'),
                'status' => 'pending'
            ];


            $add = $this->withdraw->store($data);

            if($add->id) {
                return $this->common->returnSuccess();
            }
        }
    }
}
