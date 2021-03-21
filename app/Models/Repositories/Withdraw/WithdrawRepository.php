<?php


namespace App\Models\Repositories\Withdraw;


use App\Models\Core\Withdraw;

class WithdrawRepository implements WithdrawRepositoryInterface
{
    public function getList($id)
    {
        return Withdraw::where('user_id', $id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function store($data)
    {
        return Withdraw::create($data);
    }

    public function update($data, $id)
    {
        return Withdraw::where('id', $id)->update($data);
    }

    public function getAllByStatus($status)
    {
        return Withdraw::where('status', $status)->with('user')->get();
    }

    public function getAll()
    {
        return Withdraw::with('user')->get();
    }

    public function getWithdraw($id)
    {
        return Withdraw::find($id);
    }
}
