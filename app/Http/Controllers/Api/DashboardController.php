<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\CommonController;
use App\Http\Controllers\Controller;
use App\Services\TopupServices;
use App\Services\WithdrawServices;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public $common;

    public $withdraw;

    public $deposit;

    public function __construct(
        CommonController $common,
        WithdrawServices $withdraw,
        TopupServices $deposit
    ) {
        $this->common = $common;
        $this->withdraw = $withdraw;
        $this->deposit = $deposit;
    }

    public function report()
    {
        if(!$this->common->hasPermission('permissions.report.get')) {
            return response()->json('Unauthorized', 401);
        }

        $withdraws = $this->withdraw->getRecentWithdraw('week');

        $deposits = $this->deposit->getRecentDeposit('week');

        return $this->common->returnSuccessWithData(['withdraws' => $withdraws, 'deposits' => $deposits]);
    }

    public function filterTransactions($filter)
    {
        if(!$this->common->hasPermission('permissions.report.get')) {
            return response()->json('Unauthorized', 401);
        }

        $withdraws = $this->withdraw->getRecentWithdraw($filter);

        $deposits = $this->deposit->getRecentDeposit($filter);

        return $this->common->returnSuccessWithData(['withdraws' => $withdraws, 'deposits' => $deposits]);
    }
}
