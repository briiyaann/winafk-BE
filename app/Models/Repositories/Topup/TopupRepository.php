<?php


namespace App\Models\Repositories\Topup;


use App\Models\Core\Topup;
use Carbon\Carbon;

class TopupRepository implements TopupRepositoryInterface
{
    public function getList($id)
    {
        return Topup::where('user_id', $id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
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

    public function getRecentDeposit($filter)
    {
        switch ($filter) {
            case 'day':
                return Topup::whereDate('created_at', date('Y-m-d'))
                    ->where('status', 'approved')
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->get();
                break;
            case 'week':
                return Topup::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                    ->where('status', 'approved')
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->get();
                break;
            case 'month':
                return Topup::whereMonth('created_at', date('m'))
                    ->whereYear('created_at', date('Y'))
                    ->where('status', 'approved')
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->get();
                break;
            default:
                return null;
                break;
        }
    }
}
