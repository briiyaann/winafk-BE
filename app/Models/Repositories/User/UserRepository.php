<?php


namespace App\Models\Repositories\User;

use App\Models\Core\PasswordReset;
use App\Models\Core\User;

class UserRepository implements UserRepositoryInterface
{
    public function addUser($data)
    {
        return User::create($data);
    }

    public function getUserByCode($code)
    {
        return User::where('verification_code', $code)->first();
    }

    public function updateUser($id, $data)
    {
        return User::where('id', $id)->update($data);
    }

    public function hardDeleteUser($id)
    {
        return User::where('id', $id)->forceDelete();
    }

    public function deleteUser($id)
    {
        return User::Where('id', $id)->delete();
    }

    public function getUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function createPasswordReset($email, $hash)
    {
        return PasswordReset::create(['email' => $email, 'token' => $hash]);
    }

    public function getPasswordReset($token)
    {
        return PasswordReset::where('token', $token)->first();
    }

    public function deletePasswordReset($email)
    {
        return PasswordReset::where('email', $email)->delete();
    }
}
