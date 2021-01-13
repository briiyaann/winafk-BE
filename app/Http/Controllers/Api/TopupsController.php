<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\CommonController;
use App\Services\TopupServices;
use App\Services\UserSerivces;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
class TopupsController
{
    public $topup;

    public $common;

    public $user;

    public function __construct(
        TopupServices $topup,
        CommonController $common,
        UserSerivces $user
    ){
        $this->topup = $topup;
        $this->common = $common;
        $this->user = $user;
    }

    public function getList($id)
    {
        $topups = $this->topup->getList($id);

        return $this->common->returnSuccessWithData($topups);
    }

    public function getAllByStatus($status = 'all')
    {
        if($status == 'all') {
            $topups = $this->topup->getAll();
        } else {
            $topups = $this->topup->getAllByStatus($status);
        }

        return $this->common->returnSuccessWithData($topups);
    }

    public function store(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'amount' => 'required',
            'phone' => 'required_without:email|integer',
            'email' => 'required_without:phone|email',
            'receipt' => 'required|mimes:jpg,jpeg,gif,png'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            $return_err = [];
            foreach ($validator->errors()->toArray() as $key => $value) {
                $return_err[$key] = $value[0];
            }

            return $this->common->returnWithErrors($return_err);
        } else {
            $required_id = true;

            $topups = $this->topup->getList($request->get('user_id'));

            $required_id = count($topups) > 0 ? false : true;

            if($required_id && !$request->file('valid_id'))
                return $this->common->createErrorMsg('valid_id', 'Valid id is required.');

            $valid_id_path = null;

            if($request->file('receipt') || $request->file('valid_id')) {
                if($required_id) {
                    $upload_folder_valid_id = 'topups/valid_id';
                    $valid_id = $request->file('valid_id');
                    $valid_id_path = $valid_id->store($upload_folder_valid_id, 'public');
                }

                $upload_folder_receipt = 'topups/receipt';
                $banner = $request->file('receipt');
                $receipt_path = $banner->store($upload_folder_receipt, 'public');

                $data = [
                    'user_id' => $request->get('user_id'),
                    'amount' => $request->get('amount'),
                    'phone' => $request->get('phone'),
                    'email' => $request->get('email'),
                    'receipt' => $receipt_path,
                    'valid_id' => $valid_id_path,
                    'status' => 'pending'
                ];

                $topup = $this->topup->store($data);

                return $this->common->returnSuccessWithData($topup);
            }
        }
    }

    public function update(Request $request, $id)
    {
        if(!$this->common->hasPermission('permissions.topup.update'))
            return response()->json('Unauthorized', 401);

        $topup = $this->topup->getTopup($id);

        if(!$topup || $topup->status != 'pending') return $this->common->createErrorMsg('not_found', 'No Topup');

        $rules = [
            'status' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            $return_err = [];
            foreach ($validator->errors()->toArray() as $key => $value) {
                $return_err[$key] = $value[0];
            }

            return $this->common->returnWithErrors($return_err);
        } else {
            if(strtolower($request->get('status')) === 'approved') {
                $user_id = $topup->user_id;

                $user_to_approve = $this->user->findUser($user_id);

                $cur_user_coins = $user_to_approve->coins;

                $data = [
                    'coins' => intval($cur_user_coins) + intval($topup->amount)
                ];

                $top_data = [
                    'status' => $request->get('status'),
                    'approved_by' => Auth::user()->id
                ];

                $save_top = $this->topup->update($top_data, $id);

                $save = $this->user->updateUser($user_id, $data);

                // TODO: email the user after approving the topup

            } else {
                $data = [
                    'status' => $request->get('status'),
                    'reason' => $request->get('reason'),
                    'approved_by' => Auth::user()->id
                ];

                $save_top = $this->topup->update($data, $id);

                // TODO: email user after denying the topup;
            }

            return $this->common->returnSuccessWithData($save_top);
        }
    }

}
