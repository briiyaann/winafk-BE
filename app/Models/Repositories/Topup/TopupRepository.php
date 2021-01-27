<?php


namespace App\Models\Repositories\Topup;


use App\Models\Core\Topup;

class TopupRepository implements TopupRepositoryInterface
{
    public function getList($id)
    {
        return Topup::where('user_id', $id)->with('user')->get();
    }

    public function store($data)
    {
        return Topup::create($data);
    }

    public function update($data, $id)
    {
        return Topup::where('id', $id)->update($data);
    }

    public function getAllByStatus($status)
    {
        return Topup::where('status', $status)->with('user')->get();
    }

    public function getAll()
    {
        Topup::all();
    }

    public function getTopup($id)
    {
        return Topup::find($id);
    }
}
