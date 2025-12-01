<?php

namespace App\Http\Controllers\Ledger;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Ledger;
use App\Models\PartyType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class LedgerController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ledger-view', only: [
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
        $partyTypes = PartyType::get();
        return view('ledger.index', compact('customers', 'partyTypes'));
    }

    public function getList(Request $request)
    {
        // Sanitize inputs
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $customer_id = $request->input('customer');
        $partyType = $request->input('partyType');
        $master_type = 1;

        // Prepare data
        $data = [];
        // $data['dbh'] = config('database.connections.mysql.database');

        $other = ['master_type' => $master_type];
        $data['customer_check_master_type'] = $master_type;
        $data['data'] = Ledger::getLedgerReport($fromDate, $toDate, $customer_id, $partyType, $other);

        // Return the view with data
        return view('ledger.ajax_report', $data);
    }

    public function customer(Request $request)
    {
        $data = DB::table('customer')
            ->join('cities', 'customer.city_id', '=', 'cities.id')
            ->join('party_types', 'customer.party_type_id', '=', 'party_types.id')
            ->join('party_groups', 'customer.party_group_id', '=', 'party_groups.id')
            ->join('party_categorys', 'customer.bill_type', '=', 'party_categorys.id');
        if ($request->partyType != 'ALL' && $request->partyType != '') {
            $data->where('customer.party_type_id', $request->partyType);
        }
        if (isset($request->search) && $request->search != '') {
            $data->where('customer.name', 'like', '%' . $request->search . '%');
        }
        $data = $data->get([
            'customer.id', // Specify the table for the id
            DB::raw("CONCAT(customer.name, ' -  (', cities.name, ' - ', party_types.name,' ) ') as text"),
        ]);
        return response()->json($data);
    }

    public function ledgerReportByCustomer($customer = 0, $acCat = 0)
    {
        $other = [
            'master_type' => 1
        ];

        // Fetch ledger report data
        $data = Ledger::getLedgerReport('', '', $customer, $acCat, $other);

        // Calculate total opening amount and fine
        $totalOpeningAmt = 0;
        $totalOpeningFine = 0;

        $cust = Customer::find($customer);

        if ($cust?->opening_amount_type == 'JAMA') {
            $totalOpeningAmt -= $cust['opening_amount'];
        } else {
            $totalOpeningAmt += $cust['opening_amount'];
        }

        $total_opening_amt = $totalOpeningAmt;

        return view('ledger.account_ledger_customer', compact('data', 'total_opening_amt', 'customer'));
    }

    public function getLedgerCustomerReport(Request $request)
    {

        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $customer = $request->input('customer');
        $partyType = $request->input('partyType');
        $other = [
            'master_type' => 1
        ];

        // Fetch ledger report data
        $data = Ledger::getLedgerReport($fromDate, $toDate, $customer, $partyType, $other);

        // Calculate total opening amount and fine
        $totalOpeningAmt = 0;

        $total_opening_amt = $totalOpeningAmt;

        return view('ledger.ajax_account_ledger_customer_report', compact('data', 'total_opening_amt'));
    }
}
