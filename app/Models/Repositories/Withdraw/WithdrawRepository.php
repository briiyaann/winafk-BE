<?php


namespace App\Models\Repositories\Withdraw;


use App\Models\Core\Withdraw;
use Carbon\Carbon;

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

    public function getRecentWithdraw($filter)
    {
        switch ($filter) {
            case 'day':
                return Withdraw::whereDate('created_at', date('Y-m-d'))
                        ->where('status', 'approved')
                        ->with('user')
                        ->orderBy('created_at', 'desc')
                        ->get();
                break;
            case 'week':
                return Withdraw::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                        ->where('status', 'approved')
                        ->with('user')
                        ->orderBy('created_at', 'desc')
                        ->get();
                break;
            case 'month':
                return Withdraw::whereMonth('created_at', date('m'))
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
