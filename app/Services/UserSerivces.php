<?php


namespace App\Services;


use App\Models\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Str;

class UserSerivces
{
    protected $user;

    public function __construct(
        UserRepositoryInterface $user
    ){
        $this->user = $user;
    }

    public function addUser($data)
    {
        return $this->user->addUser($data);
    }

    public function getUserByCode($code)
    {
        return $this->user->getUserByCode($code);
    }

    public function updateUser($id, $data)
    {
        return $this->user->updateUser($id, $data);
    }

    public function hardDeleteUser($id)
    {
        return $this->user->hardDeleteUser($id);
    }

    public function getUserByEmail($email)
    {
        return $this->user->getUserByEmail($email);
    }

    public function createPasswordReset($email)
    {
        $random_str = Str::random(30);

        return $this->user->createPasswordReset($email, $random_str);
    }

    public function getPasswordReset($token)
    {
        return $this->user->getPasswordReset($token);
    }

    public function deletePasswordReset($email)
    {
        return $this->user->deletePasswordReset($email);
    }

    public function findUser($id)
    {
        return $this->user->findUser($id);
    }

    public function findMMUsers() {
        return $this->user->findMMUsers();
    }

    public function checkRefenrence($reference) {
        return $this->user->checkRefenrence($reference);
    }

}
