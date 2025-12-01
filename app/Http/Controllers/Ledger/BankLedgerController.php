<?php

namespace App\Http\Controllers\Ledger;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\BankLedger;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class BankLedgerController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:bank-ledger-view', only: [
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
        $customers  = Customer::get();
        $banks  = Bank::get();
        return view('ledger.bank.index', compact('customers', 'banks'));
    }

    public function getList(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $customer_id = $request->input('customer');
        $bank = $request->input('bank');
        $data = [];
        $data['customer_check_master_type'] = 1;
        $data['data'] = BankLedger::getLedgerReport($fromDate, $toDate, $customer_id, $bank, ['master_type' => 1]);
        return view('ledger.bank.ajax_report', $data);
    }

    public function ledgerReportByCustomer($bank = 0, $acCat = 0)
    {
        $data = BankLedger::getLedgerReport('', '', '', $bank, ['master_type' => 1]);
        $totalOpeningAmt = 0;
        $totalOpeningFine = 0;
        return view('ledger.bank.account_ledger_customer', compact('data', 'bank'));
    }

    public function getLedgerCustomerReport(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $customer = $request->input('customer');
        $bank = $request->input('bank');
        $data = BankLedger::getLedgerReport($fromDate, $toDate, $customer, $bank, ['master_type' => 1]);
        return view('ledger.bank.ajax_account_ledger_customer_report', compact('data'));
    }
}
