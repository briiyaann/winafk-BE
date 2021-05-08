<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\CommonController;
use App\Http\Controllers\Controller;
use App\Services\UserSerivces;
use App\Services\WithdrawServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class WithdrawsController extends Controller
{
    public $withdraw;

    public $common;

    public $user;

    public function __construct(
        CommonController $common,
        WithdrawServices $withdraw,
        UserSerivces $user
    ) {
        $this->common = $common;
        $this->withdraw = $withdraw;
        $this->user = $user;
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

    public function getAllByStatus($status)
    {
        if($status == 'all') {
            $withdraws = $this->withdraw->getAll();
        } else {
            $withdraws = $this->withdraw->getAllByStatus($status);
        }

        return $this->common->returnSuccessWithData($withdraws);
    }

    public function updateWithdraw(Request $request, $id)
    {
        $withdraw = $this->withdraw->getWithdraw($id);
        if(!$withdraw) return $this->common->createErrorMsg('api_arror', 'Withdrawal information not found');

        $user = $this->user->findUser($withdraw->user_id);

        if($request->get('status') === 'approved') {
            if(!$request->file('receipt')) return $this->common->createErrorMsg('api_error', 'Receipt is required');

            $upload_folder_receipt = 'withdraws/receipt';
            $receipt = $request->file('receipt');
            $receipt_path = $receipt->store($upload_folder_receipt, 'public');

            $data = [
                'status' => $request->get('status'),
                'approved_by' => Auth::user()->id,
                'receipt' => $receipt_path
            ];

            $update = $this->withdraw->update($data, $id);

            if($update) {
                $user_coins = $user->coins;
                $updated_coins = floatval($user_coins) - floatval($withdraw->amount);

                $user_data = ['coins' => $updated_coins];
                $this->user->updateUser($user->id , $user_data);

                return $this->common->returnSuccess();
            }
        } else {
            if(!$request->get('reason')) return $this->common->createErrorMsg('api_error', 'Declining reason is required');
            $data = [
                'status' => $request->get('status'),
                'approved_by' => Auth::user()->id,
                'reason' => $request->get('reason')
            ];

            $update = $this->withdraw->update($data, $id);

            if($update) {
                return $this->common->returnSuccess();
            }
        }
    }
}
