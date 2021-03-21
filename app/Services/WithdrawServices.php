<?php


namespace App\Services;


use App\Models\Repositories\Withdraw\WithdrawRepositoryInterface;

class WithdrawServices
{
    public $withdraw;

    public function __construct(
        WithdrawRepositoryInterface $withdraw
    ) {
        $this->withdraw = $withdraw;
    }

    public function store($data)
    {
        return $this->withdraw->store($data);
    }

    public function getList($id)
    {
        return $this->withdraw->getList($id);
    }

    public function getAllByStatus($status)
    {
        return $this->withdraw->getAllByStatus($status);
    }

    public function getAll()
    {
        return $this->withdraw->getAll();
    }

    public function update($data, $id)
    {
        return $this->withdraw->update($data, $id);
    }

    public function getWithdraw($id)
    {
        return $this->withdraw->getWithdraw($id);
    }
}
