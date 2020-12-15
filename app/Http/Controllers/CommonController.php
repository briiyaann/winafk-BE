<?php


namespace App\Http\Controllers;

use Validator;

class CommonController
{

    public function __construct(
    ){
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
}
