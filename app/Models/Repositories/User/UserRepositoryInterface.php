<?php


namespace App\Models\Repositories\User;


interface UserRepositoryInterface
{
    public function addUser($data);

    public function getUserByCode($data);

    public function updateUser($id, $data);

    public function hardDeleteUser($id);

    public function deleteUser($id);

    public function getUserByEmail($email);

    public function createPasswordReset($email, $hash);

    public function getPasswordReset($token);

    public function deletePasswordReset($email);

    public function findUser($id);

    public function findMMUsers();
}
