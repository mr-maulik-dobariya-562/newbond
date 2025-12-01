<?php

namespace App\Http\Controllers\manufactureMaster;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Employee;
use App\Traits\DataTable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class AnalysisPrintingController extends Controller implements HasMiddleware
{
    use DataTable;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:analysis-printing-create', only: ['create']),
            new Middleware('permission:analysis-printing-view', only: ['index', "getView"]),
            new Middleware('permission:analysis-printing-edit', only: ['edit', "update"]),
            new Middleware('permission:analysis-printing-delete', only: ['destroy']),
        ];
    }

    public function __construct()
    {
        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
    }

    public function index()
    {
        $operators = Employee::where('status', 'ACTIVE')->get();
        return view('manufactureMaster.analysisPrinting.index', compact('operators'));
    }

    public function getView(Request $request)
    {
        $operator = $request->operator;
        $fyYear = $request->fyYear;

        $today = Carbon::now();
        if (empty($fyYear)) {
            for ($i = 0; $i < 12; $i++) {
                $date = $today->copy()->subMonths($i);
                $months[] = $date->format('M Y');
                $monthYear[] = $date->format('Y m');
                $value = $date->format('Y-m');
            }
        } else {
            $months = $fyYear;
            $monthYear = $fyYear;
        }

        $ppq = DB::table('printings')->where('branch_id', session('branch_id'));
        if (!empty($fyYear)) {
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
                $date = explode('-', $fyYear);
                $ppq->whereYear('date', $date[0])
                    ->whereMonth('date', $date[1]);
            }
        }
        if (!empty($operator)) {
            foreach ($operator as $key => $value) {
                if ($key == 0) {
                    $ppq->whereRaw("FIND_IN_SET($value,operator_id)");
                } else {
                    $ppq->orWhereRaw("FIND_IN_SET($value,operator_id)");
                }
            }
        }

        $productionPiecesQuantity = $ppq->sum('production_qty');

        $rectionPiecesQuantity = $ppq->sum('rection_qty');

        $data = [
            'productionPiecesQuantity' => $productionPiecesQuantity,
            'rectionPiecesQuantity' => $rectionPiecesQuantity,
        ];
        $html = view('manufactureMaster.analysisPrinting.analysis_view', compact('data'))->render();

        return response()->json([
            'html'          => $html,
            'months'        => $months,
            'productionQty' => $this->sumOfPoductionQty($operator, $fyYear),
        ]);
    }

    public function weightChart(Request $request)
    {
        $operator = $request->operator;
        $fyYear = $request->fyYear;

        $ppq = DB::table('printings')->where('branch_id', session('branch_id'));
        if (!empty($fyYear)) {
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
                $date = explode('-', $fyYear);
                $ppq->whereYear('date', $date[0])
                    ->whereMonth('date', $date[1]);
            }
        }
        if (!empty($operator)) {
            foreach ($operator as $key => $value) {
                if ($key == 0) {
                    $ppq->whereRaw("FIND_IN_SET($value,operator_id)");
                } else {
                    $ppq->orWhereRaw("FIND_IN_SET($value,operator_id)");
                }
            }
        }
        $ppq->groupBy('date');
        $production = $ppq->select('date', 'production_qty as amount')
            ->get()
            ->toArray();
        $rejection = $ppq->select('date', 'rection_qty as amount')
            ->get()
            ->toArray();

        $machine = DB::table('printings')
            ->selectRaw('m.name, sum(rection_qty) as rectionTotal, sum(production_qty) as productionTotal')
            ->join('machines as m', 'printings.machine_id', '=', 'm.id')
            ->where('printings.branch_id', session('branch_id'))
            ->groupBy('m.name');
        if (!empty($fyYear)) {
            if (is_array($fyYear)) {
                $machine->where(function ($query) use ($fyYear) {
                    foreach ($fyYear as $year) {
                        $date = explode('-', $year);
                        $query->orWhere(function ($subQuery) use ($date) {
                            $subQuery->whereYear('date', $date[0])
                                ->whereMonth('date', $date[1]);
                        });
                    }
                });
            } else {
                $date = explode('-', $fyYear);
                $machine->whereYear('date', $date[0])
                    ->whereMonth('date', $date[1]);
            }
        }
        if (!empty($operator)) {
            foreach ($operator as $key => $value) {
                if ($key == 0) {
                    $machine->whereRaw("FIND_IN_SET($value,operator_id)");
                } else {
                    $machine->orWhereRaw("FIND_IN_SET($value,operator_id)");
                }
            }
        }
        $machineAll = $machine->get()->toArray();

        $machineType = DB::table('printings')
            ->selectRaw('mt.name, sum(rection_qty) as rectionTotal, sum(production_qty) as productionTotal')
            ->join('machines as m', 'printings.machine_id', '=', 'm.id')
            ->join('machine_types as mt', 'm.machine_type_id', '=', 'mt.id')
            ->where('printings.branch_id', session('branch_id'))
            ->groupBy('mt.name');
        if (!empty($fyYear)) {
            if (is_array($fyYear)) {
                $machineType->where(function ($query) use ($fyYear) {
                    foreach ($fyYear as $year) {
                        $date = explode('-', $year);
                        $query->orWhere(function ($subQuery) use ($date) {
                            $subQuery->whereYear('date', $date[0])
                                ->whereMonth('date', $date[1]);
                        });
                    }
                });
            } else {
                $date = explode('-', $fyYear);
                $machineType->whereYear('date', $date[0])
                    ->whereMonth('date', $date[1]);
            }
        }
        if (!empty($operator)) {
            foreach ($operator as $key => $value) {
                if ($key == 0) {
                    $machineType->whereRaw("FIND_IN_SET($value,operator_id)");
                } else {
                    $machineType->orWhereRaw("FIND_IN_SET($value,operator_id)");
                }
            }
        }
        $machineTypeAll = $machineType->get()->toArray();

        $operator = DB::table('printings')
            ->selectRaw('e.name, sum(rection_qty) as rectionTotal, sum(production_qty) as productionTotal')
            ->join('employees as e', 'printings.operator_id', '=', 'e.id')
            ->where('printings.branch_id', session('branch_id'))
            ->groupBy('printings.operator_id');
        if (!empty($fyYear)) {
            if (is_array($fyYear)) {
                $operator->where(function ($query) use ($fyYear) {
                    foreach ($fyYear as $year) {
                        $date = explode('-', $year);
                        $query->orWhere(function ($subQuery) use ($date) {
                            $subQuery->whereYear('date', $date[0])
                                ->whereMonth('date', $date[1]);
                        });
                    }
                });
            } else {
                $date = explode('-', $fyYear);
                $operator->whereYear('date', $date[0])
                    ->whereMonth('date', $date[1]);
            }
        }
        if (!empty($operator)) {
            foreach ($operator as $key => $value) {
                if ($key == 0) {
                    $operator->whereRaw("FIND_IN_SET($value,operator_id)");
                } else {
                    // $operator->orWhereRaw("FIND_IN_SET($value,operator_id)");
                }
            }
        }
        $operatorAll = $operator->get()->toArray();

        $data = [
            'production'  => $production,
            'rejection'   => $rejection,
            'machine'     => $machineAll,
            'machineType' => $machineTypeAll,
            'operator'    => $operatorAll
        ];

        return response()->json($data);
    }

    public function sumOfPoductionQty($operator, $fyYear)
    {
        $ppq = DB::table('printings')
            ->select(
                DB::raw('YEAR(date) as year'),
                DB::raw('MONTH(date) as month'),
                DB::raw('SUM(production_qty) as total_production_qty'),
                DB::raw('SUM(rection_qty) as total_rection_qty')
            )
            ->where('branch_id', session('branch_id'))
            ->groupBy(DB::raw('YEAR(date), MONTH(date)'));

        if (!empty($fyYear)) {
            if (is_array($fyYear)) {
                $ppq->where(function ($query) use ($fyYear) {
                    foreach ($fyYear as $year) {
                        $date = explode('-', $year);
                        if (count($date) == 2) {  // Ensure the format is correct
                            $query->orWhere(function ($subQuery) use ($date) {
                                $subQuery->whereYear('date', $date[0])
                                    ->whereMonth('date', $date[1]);
                            });
                        }
                    }
                });
            } else {
                // If fyYear is a single value (non-array)
                $date = explode('-', $fyYear);
                if (count($date) == 2) { // Ensure the format is correct
                    $ppq->whereYear('date', $date[0])
                        ->whereMonth('date', $date[1]);
                }
            }
        }

        // Handling operator filter
        if (!empty($operator)) {
            $ppq->whereIn('operator_id', $operator);
        }

        // Return the query result
        return $ppq->get();
    }
}
