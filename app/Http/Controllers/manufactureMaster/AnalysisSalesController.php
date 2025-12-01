<?php

namespace App\Http\Controllers\manufactureMaster;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Estimate;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class AnalysisSalesController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:analysis-sales-create', only: ['create']),
            new Middleware('permission:analysis-sales-view', only: ['index', "getView"]),
            new Middleware('permission:analysis-sales-edit', only: ['edit', "update"]),
            new Middleware('permission:analysis-sales-delete', only: ['destroy']),
        ];
    }

    public function __construct()
    {
        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
    }

    public function index()
    {
        return view('manufactureMaster.analysisSales.index');
    }

    public function getView(Request $request)
    {

        function applyFiscalYearFilter($query, $fyYear, $dateColumn = 'date')
        {
            if (!empty($fyYear)) {
                if (is_array($fyYear)) {
                    $query->where(function ($q) use ($fyYear, $dateColumn) {
                        foreach ($fyYear as $year) {
                            $date = explode('-', $year);
                            $q->orWhere(function ($subQuery) use ($date, $dateColumn) {
                                $subQuery->whereYear($dateColumn, $date[0])
                                    ->whereMonth($dateColumn, $date[1]);
                            });
                        }
                    });
                } else {
                    $date = explode('-', $fyYear);
                    $query->whereYear($dateColumn, $date[0])
                        ->whereMonth($dateColumn, $date[1]);
                }
            }

            return $query;
        }



        $fyYear = $request->fyYear;
        $branchId = session('branch_id');

        // Estimate Query
        $estimateQuery = Estimate::query();
        applyFiscalYearFilter($estimateQuery, $fyYear);

        // Top Customers
        $topCustomers = (clone $estimateQuery)
            ->select('customer.id', 'customer.name', DB::raw('COUNT(estimates.id) as total_orders'))
            ->join('customer', 'estimates.customer_id', '=', 'customer.id')
            ->groupBy('customer.id', 'customer.name')
            ->orderBy('total_orders', 'DESC')
            ->take(10)
            ->get();

        // Top Items Sold
        $topItems = DB::table('estimate_details')
            ->select('items.id', 'items.name', DB::raw('SUM(estimate_details.qty) as total_quantity_sold'))
            ->join('items', 'estimate_details.item_id', '=', 'items.id')
            ->join('estimates', 'estimate_details.estimate_id', '=', 'estimates.id')
            ->where('estimate_details.branch_id', $branchId);
        applyFiscalYearFilter($topItems, $fyYear, 'estimates.date');

        $topItems = $topItems
            ->groupBy('items.id', 'items.name')
            ->orderBy('total_quantity_sold', 'DESC')
            ->take(10)
            ->get();

        // Sales per Item
        $sales = DB::table('estimate_details')
            ->select('items.id', 'items.name', DB::raw('SUM(estimate_details.qty) as total_quantity_sold'))
            ->join('items', 'estimate_details.item_id', '=', 'items.id')
            ->join('estimates', 'estimate_details.estimate_id', '=', 'estimates.id')
            ->where('estimate_details.branch_id', $branchId);
        applyFiscalYearFilter($sales, $fyYear, 'estimates.date');

        $sales = $sales
            ->groupBy('items.id', 'items.name')
            ->get();

        // Sales by Party Group
        $saleGroup = DB::table('estimate_details')
            ->select('party_groups.name', DB::raw('SUM(estimate_details.qty) as total_quantity_sold'))
            ->join('estimates', 'estimate_details.estimate_id', '=', 'estimates.id')
            ->join('customer', 'estimates.customer_id', '=', 'customer.id')
            ->join('party_groups', 'customer.party_group_id', '=', 'party_groups.id')
            ->where('estimate_details.branch_id', $branchId);
        applyFiscalYearFilter($saleGroup, $fyYear, 'estimates.date');

        $saleGroup = $saleGroup
            ->groupBy('party_groups.id', 'party_groups.name')
            ->get();


        // Order quantities
        // Orders - applying date filter by joining with the orders table
        $orderQuery = DB::table('order_details')
            ->join('items', 'order_details.item_id', '=', 'items.id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->where('order_details.branch_id', session('branch_id'));

        // Apply the date filter
        applyFiscalYearFilter($orderQuery, $fyYear, 'orders.date');

        $order = $orderQuery
            ->select('items.id', 'items.name', DB::raw('SUM(order_details.qty) as total_quantity_sold'))
            ->groupBy('items.id', 'items.name')
            ->get();

        $ordersPendingQty = DB::table('order_details')
            ->select(DB::raw('SUM(qty - dispatch_qty - cancel_qty) as total_pending_qty'))
            ->whereRaw('qty - dispatch_qty - cancel_qty > 0')
            ->where('order_details.branch_id', '=', session('branch_id'))
            ->first();

        $ordersTotalQty = DB::table('order_details')
            ->select(DB::raw('SUM(qty) as total_qty'))
            ->where('order_details.branch_id', '=', session('branch_id'))
            ->first();

        $data = [
            'ordersPendingQty' => $ordersPendingQty->total_pending_qty ?? 0,
            'ordersTotalQty' => $ordersTotalQty->total_qty ?? 0,
            'totalOrders' => $estimateQuery->count(),
        ];

        return response()->json([
            'html' => view('manufactureMaster.analysisSales.analysis_view', compact('data'))->render(),
            'topCustomers' => $topCustomers,
            'topItems' => $topItems,
            'sales' => $sales,
            'order' => $order,
            'saleGroup' => $saleGroup
        ]);

    }
}
