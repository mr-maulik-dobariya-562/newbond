<?php

namespace App\Http\Controllers\Ledger;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\WalletLedger;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class WalletLedgerController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:wallet-ledger-view', only: [
                'index',
                "get-list",
                "customer",
                "getLedgerCustomerReport",
                "ledgerReportByCustomer"
            ]),
        ];
    }

    public function __construct()
    {
        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::get();
        return view('ledger.wallet.index', compact('customers'));
    }

    public function getList(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $customer_id = $request->input('customer');
        $bank = '';

        $data = [];
        $data['customer_check_master_type'] = 1;
        $data['data'] = WalletLedger::getLedgerReport($fromDate, $toDate, $customer_id, $bank, ['master_type' => 1]);
        return view('ledger.wallet.ajax_report', $data);
    }

    public function ledgerReportByCustomer($customer = 0, $acCat = 0)
    {
        $data = WalletLedger::getLedgerReport('', '', $customer, '', ['master_type' => 1]);
        $totalOpeningAmt = 0;
        $totalOpeningFine = 0;
        return view('ledger.wallet.account_ledger_customer', compact('data', 'customer'));
    }

    public function getLedgerCustomerReport(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $customer = $request->input('customer');
        $bank = '';
        $data = WalletLedger::getLedgerReport($fromDate, $toDate, $customer, '', ['master_type' => 1]);
        return view('ledger.wallet.ajax_account_ledger_customer_report', compact('data'));
    }
}
