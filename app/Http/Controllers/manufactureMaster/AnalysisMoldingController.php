<?php

namespace App\Http\Controllers\manufactureMaster;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Machine;
use App\Models\ProductType;
use App\Models\Shift;
use App\Traits\DataTable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class AnalysisMoldingController extends Controller implements HasMiddleware
{
    use DataTable;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:analysis-molding-create', only: ['create']),
            new Middleware('permission:analysis-molding-view', only: ['index', "getView"]),
            new Middleware('permission:analysis-molding-edit', only: ['edit', "update"]),
            new Middleware('permission:analysis-molding-delete', only: ['destroy']),
        ];
    }

    public function __construct()
    {
        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
    }

    public function index()
    {
        $machines = Machine::all();
        $locations = Location::all();
        $shifts = Shift::all();
        $productTypes = ProductType::all();
        return view('manufactureMaster.analysisMolding.index', compact('machines', 'locations', 'shifts', 'productTypes'));
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
            // $date = explode('-', $fyYear);
            // $ppq->whereYear('date', $date[0]);
            // $ppq->whereMonth('date', $date[1]);
            if (is_array($fyYear)) {
                $ppq->where(function ($query) use ($fyYear) {
                    foreach ($fyYear as $year) {
                        $date = explode('-', $year);
                        $query->orWhere(function ($subQuery) use ($date) {
                            $subQuery->whereYear('date', $date[0])
                                ->whereMonth('date', $date[1]);
                        });
                    }
                });
            } else {
                // If fyYear is a single value
                $date = explode('-', $fyYear);
                $ppq->whereYear('date', $date[0])
                    ->whereMonth('date', $date[1]);
            }
        }
        if (!empty($shift)) {
            $ppq->whereIn('shift_id', $shift);
        }
        if (!empty($machine)) {
            $ppq->whereIn('machine_id', $machine);
        }
        if (!empty($location)) {
            $ppq->whereIn('machines.location_id', $location);
        }
        if (!empty($product)) {
            $ppq->whereIn('product_type_id', $product);
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
            $months = $fyYear;
            $monthYear = $fyYear;
        }

        $comlocationAll = [];
        $compercentage = [];
        $compReje = $ppq->selectRaw('machines.location_id, locations.name as location_name, SUM(component_rejection) as component_rejection_sum')
            ->groupBy('machines.location_id')
            ->get()
            ->map(function ($item) use ($productionPiecesQuantity) {
                $item->percentage = round($productionPiecesQuantity > 0 ? ($item->component_rejection_sum / $productionPiecesQuantity) * 100 : 0, 2);
                return $item;
            });
        foreach ($compReje as $key => $value) {
            $comlocationAll[] = $value->location_name;
            $compercentage[] = $value->percentage;
        }

        $runnerWasteLocation = [];
        $runnerpercentage = [];
        $runner = $ppq->selectRaw('machines.location_id, locations.name as location_name, SUM(runner_waste) as runner_waste_sum')
            ->groupBy('machines.location_id')
            ->get()
            ->map(function ($item) use ($productionPiecesQuantity) {
                $item->percentage = round($productionPiecesQuantity > 0 ? ($item->runner_waste_sum / $productionPiecesQuantity) * 100 : 0, 2);
                return $item;
            });
        foreach ($runner as $key => $value) {
            $runnerWasteLocation[] = $value->location_name;
            $runnerpercentage[] = $value->percentage;
        }

        $production_pieces_quantity = [];
        $productionweightChart = [];
        foreach ($monthYear as $key => $value) {
            if (!empty($fyYear)) {
                $dates = explode('-', $value);
            } else {
                $dates = explode(' ', $value);
            }
            $productionPiecesQtyChart[] = (clone $ppq)->whereMonth('date', $dates[1])->whereYear('date', $dates[0])->sum('production_pieces_quantity');
            $productionweightChart[] = (clone $ppq)->whereMonth('date', $dates[1])->whereYear('date', $dates[0])->sum('production_weight');
            $componentRejectionChart[] = (clone $ppq)->whereMonth('date', $dates[1])->whereYear('date', $dates[0])->sum('component_rejection');
            $runnerWasteChart[] = (clone $ppq)->whereMonth('date', $dates[1])->whereYear('date', $dates[0])->sum('runner_waste');
        }

        $machine = $this->getMachine($request);
        $machineName = [];
        $machineTotal = [];
        foreach ($machine as $key => $value) {
            $machineName[] = $value->name;
            $machineTotal[] = $value->total;
        }
        $getDaily = $this->getDaily($request);
        $dateAll = [];
        $productionWeightAll = [];
        $productionQtytAll = [];
        foreach ($getDaily as $key => $value) {
            $dateAll[] = $value->date;
            $productionWeightAll[] = $value->production_weight_total;
            $productionQtytAll[] = $value->production_pieces_quantity_total;
        }

        $data = [
            'productionPiecesQuantity' => round($productionPiecesQuantity),
            'productionweight' => round($productionweight),
            'locationPiecesWiseData' => $locationPiecesWiseData,
            'locationweightWiseData' => $locationweightWiseData,
            'componentRejection' => round($componentRejection),
            'componentRejectionAvg' => round($componentRejectionAvg),
            'runnerWaste' => round($runnerWaste),
            'runnerWasteAvg' => round($runnerWasteAvg),
        ];
        $html = view('manufactureMaster.analysisMolding.analysis_view', compact('data'))->render();

        return response()->json([
            'html' => $html,
            'months' => $months,
            'productionPiecesQtyChart' => $productionPiecesQtyChart,
            'productionweightChart' => $productionweightChart,
            'componentRejectionChart' => $componentRejectionChart,
            'runnerWasteChart' => $runnerWasteChart,
            'comlocationAll' => $comlocationAll,
            'compercentage' => $compercentage,
            'machineName' => $machineName,
            'machineTotal' => $machineTotal,
            'runnerWasteLocation' => $runnerWasteLocation,
            'runnerpercentage' => $runnerpercentage,
            'dateAll' => $dateAll,
            'productionWeightAll' => $productionWeightAll,
            'productionQtytAll' => $productionQtytAll,
        ]);
    }

    public function getMachine($request)
    {
        $shift = $request->shift;
        $machine = $request->machine;
        $location = $request->location;
        $product = $request->product;
        $fyYear = $request->fyYear;

        $query = DB::table('moldings')->join('machines', 'moldings.machine_id', '=', 'machines.id')
            ->where('machines.branch_id', session('branch_id'));
        if (!empty($fyYear)) {
            if (is_array($fyYear)) {
                $query->where(function ($query) use ($fyYear) {
                    foreach ($fyYear as $year) {
                        $date = explode('-', $year);
                        $query->orWhere(function ($subQuery) use ($date) {
                            $subQuery->whereYear('date', $date[0])
                                ->whereMonth('date', $date[1]);
                        });
                    }
                });
            } else {
                // If fyYear is a single value
                $date = explode('-', $fyYear);
                $query->whereYear('date', $date[0])
                    ->whereMonth('date', $date[1]);
            }
        }
        if (!empty($shift)) {
            $query->whereIn('shift_id', $shift);
        }
        if (!empty($machine)) {
            $query->whereIn('machine_id', $machine);
        }
        if (!empty($location)) {
            $query->whereIn('machines.location_id', $location);
        }
        if (!empty($product)) {
            $query->whereIn('product_type_id', $product);
        }
        $machine = $query->select('machines.name', DB::raw('SUM(production_weight) as total'))
            ->groupBy('machines.name')
            ->get();
        return $machine;
    }

    public function getDaily($request)
    {
        $shift = $request->shift;
        $machine = $request->machine;
        $location = $request->location;
        $product = $request->product;
        $fyYear = $request->fyYear;

        $query = DB::table('moldings')->join('machines', 'moldings.machine_id', '=', 'machines.id')
            ->where('machines.branch_id', session('branch_id'));
        if (!empty($fyYear)) {
            if (is_array($fyYear)) {
                $query->where(function ($query) use ($fyYear) {
                    foreach ($fyYear as $year) {
                        $date = explode('-', $year);
                        $query->orWhere(function ($subQuery) use ($date) {
                            $subQuery->whereYear('date', $date[0])
                                ->whereMonth('date', $date[1]);
                        });
                    }
                });
            } else {
                // If fyYear is a single value
                $date = explode('-', $fyYear);
                $query->whereYear('date', $date[0])
                    ->whereMonth('date', $date[1]);
            }
        }
        if (!empty($shift)) {
            $query->whereIn('shift_id', $shift);
        }
        if (!empty($machine)) {
            $query->whereIn('machine_id', $machine);
        }
        if (!empty($location)) {
            $query->whereIn('machines.location_id', $location);
        }
        if (!empty($product)) {
            $query->whereIn('product_type_id', $product);
        }
        $machine = $query->select('moldings.date', DB::raw('SUM(production_weight) as production_weight_total'), DB::raw('SUM(production_pieces_quantity) as production_pieces_quantity_total'))
            ->groupBy('moldings.date')
            ->get();
        return $machine;
    }
}
