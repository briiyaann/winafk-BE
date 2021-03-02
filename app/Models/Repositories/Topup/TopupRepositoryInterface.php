<?php


namespace App\Models\Repositories\Topup;


interface TopupRepositoryInterface
{
    public function getList($id);

    public function store($data);

    public function update($data, $id);

    public function getAllByStatus($status);

    public function getAll();

    public function getTopup($id);
}
