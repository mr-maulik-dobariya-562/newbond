<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:dashboard-create', only: ['create']),
            new Middleware('permission:dashboard-view', only: ['index', "getView"]),
            new Middleware('permission:dashboard-edit', only: ['edit', "update"]),
            new Middleware('permission:dashboard-delete', only: ['destroy']),
        ];
    }

    public function __construct()
    {
        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
    }

    public function index(Request $request)
    {
        $date = $request->input('date', date('Y-m'));
        return view('dashboard');
    }

    public function PendingOrder(Request $request)
    {
        $month = $request->input('date', date('Y-m'));

        // Convert to start and end of the month
        $startOfMonth = $month . '-01';
        $endOfMonth = date("Y-m-t", strtotime($startOfMonth));

        // Fetch necessary data
        $printTypes = PrintType::all(['id', 'name']);  // Example fetching print types
        $itemGroups = ItemGroup::all(['id', 'group_name']);  // Example fetching item groups
        $query = DB::table('order_details as od')
            ->select(
                'pt.name as print_type',
                'od.print_type_id',
                'item_categories.item_group_id',
                DB::raw('SUM(od.qty) AS qty'),
                DB::raw('SUM(od.dispatch_qty) AS dispatch_qty'),
                DB::raw('SUM(od.cancel_qty) AS cancel_qty')
            )
            ->leftJoin('orders as pl', 'pl.id', '=', 'od.order_id')
            ->leftJoin('customer as CUS', 'CUS.id', '=', 'pl.customer_id')
            ->leftJoin('print_type as pt', 'pt.id', '=', 'od.print_type_id')
            ->leftJoin('items', 'items.id', '=', 'od.item_id')
            ->leftJoin('item_categories', 'item_categories.id', '=', 'items.categories_id')
            ->where('od.branch_id', '=', session('branch_id'))
            ->whereBetween('pl.date', [$startOfMonth, $endOfMonth])
            ->groupBy('od.print_type_id', 'item_categories.item_group_id', 'pt.name')
            ->havingRaw('SUM(od.qty) != SUM(od.dispatch_qty) + SUM(od.cancel_qty)')
            ->orderByDesc('pl.id');


        $orderCodes = $query->get()->keyBy(fn($data) => "{$data->print_type_id}_{$data->item_group_id}")->toArray();


        // Prepare response data
        $response = [
            'printTypes' => $printTypes,
            'itemGroups' => $itemGroups,
            'orderCodes' => $orderCodes,
        ];

        // Return data as JSON
        return response()->json($response);
    }

    public function NumberOfCustomer(Request $request)
    {
        $month = $request->input('date', date('Y-m'));

        // Convert to start and end of the month
        $startOfMonth = $month . '-01';
        $endOfMonth = date("Y-m-t", strtotime($startOfMonth));

        $customerNumber = Customer::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get(['id', 'name', 'status', 'party_type_id', 'created_at']);
        $partyType = PartyType::all(['id', 'name']);


        $response = [
            'customerNumber' => $customerNumber,
            'partyType' => $partyType,

        ];
        return response()->json($response);

    }

    public function NumberOfItem(Request $request)
    {
        $month = $request->input('date', date('Y-m'));

        // Convert to start and end of the month
        $startOfMonth = $month . '-01';
        $endOfMonth = date("Y-m-t", strtotime($startOfMonth));

        $Item = Item::select(['items.active_type', 'items.created_at', 'item_group.id', 'item_group.group_name'])->leftJoin('item_categories', 'item_categories.id', '=', 'items.categories_id')
            ->leftJoin('item_group', 'item_group.id', '=', 'item_categories.item_group_id')
            ->whereBetween('items.created_at', [$startOfMonth, $endOfMonth])
            ->get();

        $ItemGroup = ItemGroup::select(['id', 'group_name'])->get();

        $response = [
            'Item' => $Item,
            'ItemGroup' => $ItemGroup,
        ];

        return response()->json($response);
    }
    public function estimateTable(Request $request)
    {
        $month = $request->input('date', date('Y-m'));

        // Convert to start and end of the month
        $startOfMonth = $month . '-01';
        $endOfMonth = date("Y-m-t", strtotime($startOfMonth));

        $PartyGroup = PartyGroup::all(['id', 'name']);

        $query = DB::table('estimate_details as pd')
            ->select(
                'customer.party_group_id',
                DB::raw('SUM(DISTINCT(pd.qty)) AS qty'),
                DB::raw('CAST(pl.discount_amount AS UNSIGNED) AS discount_amount'),
                DB::raw('SUM(DISTINCT(pd.amount)) AS total_amount'),
                DB::raw('CAST(pl.net_amount AS UNSIGNED) AS net_amount'),
                DB::raw('COUNT(DISTINCT(customer.party_group_id)) AS party_group_count')

            )
            ->leftJoin('estimates as pl', 'pl.id', '=', 'pd.order_id')
            ->leftJoin('customer', 'pl.customer_id', '=', 'customer.id')
            ->leftJoin('party_groups as pg', 'customer.party_group_id', '=', 'pg.id')
            ->where('customer.party_group_id', '!=', null)
            ->whereBetween('pl.date', [$startOfMonth, $endOfMonth])
            ->where('pd.branch_id', '=', session('branch_id'));

        $query->groupBy('customer.party_group_id')
            ->orderByDesc('pl.id');

        $estimate = $query->get()->toArray();

        $response = [
            'PartyGroup' => $PartyGroup,
            'estimate' => $estimate
        ];

        return response()->JSON($response);
    }

    public function getView(Request $request)
    {
        $shift = $request->shift;
        $machine = $request->machine;
        $location = $request->location;
        $product = $request->product;
        $fyYear = $request->fyYear;

        $ppq = DB::table('moldings')->join('machines', 'moldings.machine_id', '=', 'machines.id')
            ->join('locations', 'machines.location_id', '=', 'locations.id')
            ->where('moldings.branch_id', session('branch_id'));
        if (!empty($fyYear)) {
            $date = explode('-', $fyYear);
            $ppq->whereYear('date', $date[0]);
            $ppq->whereMonth('date', $date[1]);
        }
        if (!empty($shift)) {
            $ppq->where('shift_id', $shift);
        }
        if (!empty($machine)) {
            $ppq->where('machine_id', $machine);
        }
        if (!empty($location)) {
            $ppq->where('machines.location_id', $location);
        }
        if (!empty($product)) {
            $ppq->where('product_type_id', $product);
        }
        $productionPiecesQuantity = $ppq->sum('production_pieces_quantity');

        $productionweight = $ppq->sum('production_weight');

        $componentRejection = $ppq->sum('component_rejection');

        $componentRejectionAvg = $ppq->avg('component_rejection');

        $runnerWaste = $ppq->sum('runner_waste');

        $runnerWasteAvg = $ppq->avg('runner_waste');

        $locationPiecesWiseData = $ppq->select('machines.location_id', 'locations.name as location_name', DB::raw('SUM(production_pieces_quantity) as location_total'))
            ->groupBy('machines.location_id')
            ->get()
            ->map(function ($item) use ($productionPiecesQuantity) {
                $item->percentage = round($productionPiecesQuantity > 0 ? ($item->location_total / $productionPiecesQuantity) * 100 : 0, 2);
                return $item;
            });

        $locationweightWiseData = $ppq->select('machines.location_id', 'locations.name as location_name', DB::raw('SUM(production_weight) as location_total'))
            ->groupBy('machines.location_id')
            ->get()
            ->map(function ($item) use ($productionPiecesQuantity) {
                $item->percentage = round($productionPiecesQuantity > 0 ? ($item->location_total / $productionPiecesQuantity) * 100 : 0, 2);
                return $item;
            });

        $today = Carbon::now();
        if (empty($fyYear)) {
            for ($i = 0; $i < 12; $i++) {
                // Create a Carbon instance for the date
                $date = $today->copy()->subMonths($i);

                // Format month as short name and year (e.g., "Sep 2024")
                $monthYear[] = $date->format('Y m');
                $months[] = $date->format('M Y');

                // Format value as "YYYY-MM" (e.g., "2024-09")
                $value = $date->format('Y-m');
            }
        } else {
            $months[] = $fyYear;
            $monthYear[] = $fyYear;
        }

        $production_pieces_quantity = [];
        $productionweightChart = [];

        foreach ($monthYear as $key => $value) {
            if (!empty($fyYear)) {
                $dates = explode('-', $value);
            } else {
                $dates = explode(' ', $value);
            }
            $productionPiecesQtyChart[] = $ppq->whereMonth('date', $dates[1])->whereYear('date', $dates[0])->sum('production_pieces_quantity');
            $productionweightChart[] = $ppq->whereMonth('date', $dates[1])->whereYear('date', $dates[0])->sum('production_weight');
            $componentRejectionChart[] = $ppq->whereMonth('date', $dates[1])->whereYear('date', $dates[0])->sum('component_rejection');
            $runnerWasteChart[] = $ppq->whereMonth('date', $dates[1])->whereYear('date', $dates[0])->sum('runner_waste');
        }

        $data = [
            'productionPiecesQuantity' => $productionPiecesQuantity,
            'productionweight' => $productionweight,
            'locationPiecesWiseData' => $locationPiecesWiseData,
            'locationweightWiseData' => $locationweightWiseData,
            'componentRejection' => round($componentRejection, 2),
            'componentRejectionAvg' => round($componentRejectionAvg, 2),
            'runnerWaste' => round($runnerWaste, 2),
            'runnerWasteAvg' => round($runnerWasteAvg, 2),
        ];
        $html = view('dashboard_view', compact('data'))->render();

        return response()->json([
            'html' => $html,
            'months' => $months,
            'productionPiecesQtyChart' => $productionPiecesQtyChart,
            'productionweightChart' => $productionweightChart,
            'componentRejectionChart' => $componentRejectionChart,
            'runnerWasteChart' => $runnerWasteChart,

        ]);
    }

}
