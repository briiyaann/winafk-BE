<?php


namespace App\Http\Controllers;

use App\Services\UserSerivces;
use Validator;
use Illuminate\Support\Facades\Auth;

class CommonController
{
    public $user;

    public function __construct(
        UserSerivces $user
    ){
        $this->user = $user;
    }

    /**
     * @param int $status
     * @return array
     */
    public function returnSuccess($status = 200)
    {
        $return = [
            'status' => $status,
            'data'   => [],
        ];
        return $return;
    }

    /**
     * @param $data
     * @param int $status
     * @return array
     */
    public function returnSuccessWithData($data, $status = 200)
    {
        $return = [
            'status' => $status,
            'data'   => $data,
        ];
        return $return;
    }

    /**
     * @param $errors
     * @param int $status
     * @return array
     */
    public function returnWithErrors($errors, $status = 200)
    {
        $return = [
            'success' => $status,
            'errors'  => $errors,
        ];
        return $return;
    }

    /**
     * @param $key
     * @param $message
     * @return array
     */
    public function createErrorMsg($key, $message)
    {
        $return = [
            $key => $message,
        ];
        return $this->returnWithErrors($return);
    }

    /**
     * @param $request
     * @param $rules
     * @return array
     */
    public function validateInput($request, $rules)
    {
        $validator = Validator::make($request, $rules);
        if ($validator->fails()) {
            $return_err = [];
            foreach ($validator->errors()->toArray() as $key => $value) {
                $return_err[$key] = $value[0];
            }

            return $this->returnWithErrors($return_err);
        } else {

            return [];
        }
    }

    public function hasPermission($method)
    {
        $permission = config($method);

        if(!$permission) return true;

        $user_role = Auth::user()->user_role;

        return in_array($user_role, $permission) ? true : false;
    }
}
