<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\CommonController;
use App\Http\Controllers\Controller;
use App\Mail\ForgotPassword;
use App\Mail\VerifyUser;
use App\Services\UserSerivces;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Validator;
use Carbon\Carbon;

class UsersController extends Controller
{
    protected $user;

    protected $common;

    public function __construct(
        UserSerivces $user,
        CommonController $common
    ){
        $this->user = $user;
        $this->common = $common;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function register(Request $request)
    {
        $rules = [
            'firstname' => 'required',
            'lastname' => 'required',
            'birthday' => 'required',
            'username' => 'required|min:6|unique:users',
            'email' => 'required|unique:users|email',
            'password' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            $return_err = [];
            foreach ($validator->errors()->toArray() as $key => $value) {
                $return_err[$key] = $value[0];
            }

            return $this->common->returnWithErrors($return_err);
        } else {
            $data = [
                'firstname' => $request->get('firstname'),
                'lastname' => $request->get('lastname'),
                'username' => $request->get('username'),
                'email' => $request->get('email'),
                'password' => bcrypt($request->get('password')),
                'birthday' => Carbon::parse($request->get('birthday'))->format('Y-m-d H:i:s'),
                'user_role' => 1,
                'coins' => 0,
                'verification_code' => Str::random(30),
                'verification_created' => Carbon::now()
            ];

            $user = $this->user->addUser($data);

            if($user->id) {

                Mail::to($user->email)->send(new VerifyUser($user));

                return $this->common->returnSuccessWithData($user);
            }
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function verify(Request $request)
    {
        $code = $request->get('code');

        if(!$code) {
            return $this->common->createErrorMsg('code_error', 'Error code not found');
        } else {
            $user = $this->user->getUserByCode($code);
            if($user) {
                $verification_timestamp = $user->verification_created;

                $is_expired = Carbon::now()->diff(Carbon::parse($verification_timestamp));

                if($is_expired->days == 0) {
                    $update_user = [
                        'verification_code' => null,
                        'verification_created' => null,
                        'email_verified_at' => Carbon::now()
                    ];

                    $this->user->updateUser($user->id, $update_user);

                    return $this->common->returnSuccess('200');
                } else {
                    $this->user->hardDeleteUser($user->id);

                    return $this->common->createErrorMsg('code_error', 'Verification link expired.');
                }
            } else {
                return $this->common->createErrorMsg('code_error', 'Invalid Error Code.');
            }
        }

    }

    public function forgotPassword(Request $request)
    {
        $email = $request->get('email');

        if(!$email) return $this->common->createErrorMsg('no_email', 'Email not found');

        $user = $this->user->getUserByEmail($email);

        if($user) {
            $password_reset = $this->user->createPasswordReset($email);

            if($password_reset) {
                Mail::to($email)->send(new ForgotPassword($user, $password_reset));

                return $this->common->returnSuccess();
            }
        } else {
            return $this->common->createErrorMsg('no_email', 'Email not yet registered.');
        }
    }

    public function verifyToken($token)
    {
        $password_reset = $this->user->getPasswordReset($token);

        if($password_reset) {
            return $this->common->returnSuccessWithData($password_reset);
        } else {
            return $this->common->createErrorMsg('invalid_token', 'Token invalid');
        }
    }

    public function passwordReset(Request $request)
    {
        $rules = [
            'token' => 'required',
            'password' => 'required|confirmed'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            $return_err = [];
            foreach ($validator->errors()->toArray() as $key => $value) {
                $return_err[$key] = $value[0];
            }

            return $this->common->returnWithErrors($return_err);
        } else {
            $password_reset =  $this->user->getPasswordReset($request->get('token'));

            if($password_reset){
                $user = $this->user->getUserByEmail($password_reset->email);

                $new_password = bcrypt($request->get('password'));

                $update = $this->user->updateUser($user->id, ['password' => $new_password]);
                //remove item from reset password
                $this->user->deletePasswordReset($user->email);
                return $this->common->returnSuccess();
            } else {
                return $this->common->createErrorMsg('reset_error', 'Token not found');
            }
        }
    }

    public function login(Request $request)
    {
        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            $return_err = [];
            foreach ($validator->errors()->toArray() as $key => $value) {
                $return_err[$key] = $value[0];
            }

            return $this->common->returnWithErrors($return_err);
        } else {
            if(Auth::attempt(['username' => $request->get('username'), 'password' => $request->get('password')])) {
                $user = Auth::user();

                if(!$user->email_verified_at) return $this->common->createErrorMsg('invalid', 'Please verify your account first.');

                $user_arr = [
                    'email' => $user->email,
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'avatar' => $user->avatar,
                    'birthday' => $user->birthday,
                    'type' => $user->user_role,
                    'username' => $user->username
                ];
                $return = [
                    'token' => $user->createToken('winAFK')->accessToken,
                    'user' => $user_arr
                ];

                return $this->common->returnSuccessWithData($return);
            } else {
                return $this->common->createErrorMsg('invalid', 'Invalid username and password');
            }
        }

    }

    public function changePassword(Request $request)
    {
        $rules = [
            'email' => 'required',
            'password' => 'required|confirmed'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            $return_err = [];
            foreach ($validator->errors()->toArray() as $key => $value) {
                $return_err[$key] = $value[0];
            }

            return $this->common->returnWithErrors($return_err);
        } else {
            $user = $this->user->getUserByEmail($request->get('email'));
            if($user){
                $new_password = bcrypt($request->get('password'));

                $update = $this->user->updateUser($user->id, ['password' => $new_password]);

                return $this->common->returnSuccess();
            } else {
                return $this->common->createErrorMsg('email_not_found', 'Email not found');
            }
        }
    }

    public function logout()
    {
        if(Auth::check()) {
            Auth::user()->token()->revoke();
            return $this->common->returnSuccess();
        }
    }
}
