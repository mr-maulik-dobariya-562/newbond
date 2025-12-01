<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Inward;
use App\Models\Location;
use App\Models\Machine;
use App\Models\Molding;
use App\Models\MonthlyReportCheck;
use App\Models\Printing;
use App\Models\PrintType;
use App\Models\User;
use App\Models\WorkingHours;
use App\Traits\DataTable;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class PrintingController extends Controller implements HasMiddleware
{
    use DataTable;
    protected $printing;
    public function __construct(Printing $printing)
    {
        $this->printing = $printing;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:printing-create', only: ['create']),
            new Middleware('permission:printing-view', only: ['index', "getList"]),
            new Middleware('permission:printing-edit', only: ['edit', "update"]),
            new Middleware('permission:printing-delete', only: ['destroy']),
            new Middleware('permission:printing-inward-view', only: ['inward', "inwardList"]),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        if (isset($user->location_id)) {
            $machines = Machine::with('machineType')->where('location_id', 3)->whereIn('location_id', explode(',', $user->location_id))->get();
        } else {
            $machines = Machine::with('machineType')->where('location_id', 3)->get();
        }
        $machineData = Machine::with('machineType')->where('location_id', 3)->get();
        $workingHours = WorkingHours::all();
        $printTypes = PrintType::all();
        $customers = Employee::where('status', 'ACTIVE')->whereIn('type', ['Both', 'Printing'])->get();
        return view('production.printing.index', compact('machines', 'workingHours', 'customers', 'printTypes', 'machineData'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "date" => "required|date",
            "print_type_id" => "required",
            "machine_id" => "required",
            "operator_id" => "required",
            "production_qty" => "required",
            "rection_qty" => "nullable",
            "working_hours_id" => "required",
            "rejection_qty" => "nullable",
            "rejection_reason" => "nullable",
            "remarks" => "nullable",
        ]);

        if ($request->filled("working_hours_id"))
            $working_id = findOrCreate(WorkingHours::class, "name", $request->input("working_hours_id"));

        Printing::create([
            "date" => $request->date,
            "print_type_id" => $request->print_type_id,
            "machine_id" => $request->machine_id,
            "operator_id" => implode(",", $request->operator_id),
            "production_qty" => $request->production_qty ?? 0,
            "rection_qty" => $request->rection_qty ?? 0,
            "working_hours_id" => $working_id ?? null,
            "rejection_qty" => $request->rejection_qty ?? 0,
            "rejection_reason" => $request->rejection_reason,
            "remarks" => $request->remarks,
            "created_by" => auth()->id()
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Printing created successfully");
        }
        return $this->withSuccess("Printing created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Printing $printing)
    {
        $request->validate([
            "date" => "required|date",
            "print_type_id" => "required",
            "machine_id" => "required",
            "operator_id" => "required",
            "production_qty" => "required",
            "rection_qty" => "nullable",
            "working_hours_id" => "required",
            "rejection_qty" => "nullable",
            "rejection_reason" => "nullable",
            "remarks" => "nullable",
        ]);

        if ($request->filled("working_hours_id")) {
            if (is_numeric($request->input("working_hours_id"))) {
                $working_id = WorkingHours::find($request->input("working_hours_id"));
                if (!$working_id) {
                    return $this->withError("Working Hours not found", [], 200);
                } else {
                    $working_id = $working_id->id;
                }
            } else {
                $working_id = findOrCreate(WorkingHours::class, "name", $request->input("working_hours_id"));
            }
        }
        $printing->update([
            "date" => $request->date,
            "print_type_id" => $request->print_type_id,
            "machine_id" => $request->machine_id,
            "operator_id" => implode(",", $request->operator_id),
            "production_qty" => $request->production_qty ?? 0,
            "rection_qty" => $request->rection_qty ?? 0,
            "working_hours_id" => $working_id ?? null,
            "rejection_qty" => $request->rejection_qty ?? 0,
            "rejection_reason" => $request->rejection_reason,
            "remarks" => $request->remarks,
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Printing Updated successfully");
        }
        return $this->withSuccess("Printing Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Printing $printing)
    {
        $printing->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Printing Deleted successfully");
        }
        return $this->withSuccess("Printing Deleted successfully")->back();
    }

    public function getList(Request $request)
    {
        $editPermission = $this->hasPermission("printing-edit");
        $deletePermission = $this->hasPermission("printing-delete");
        $report = $request['group'];
        $url = $request['url'];
        $table = [];
        DB::statement('SET SESSION sql_mode =
        REPLACE(REPLACE(REPLACE(
        @@sql_mode,
        "ONLY_FULL_GROUP_BY,", ""),
        ",ONLY_FULL_GROUP_BY", ""),
        "ONLY_FULL_GROUP_BY", "")');
        switch ($report) {
            case 'operator':
                $data = $this->printing->getSalesGroupByOperator($request);
                $url = $url;
                break;
            case 'machine':
                $data = $this->printing->getSalesGroupByMachine($request);
                $url = $url;
                break;
            case 'voucher':
                $data = $this->printing->getSalesGroupByVoucher($request);
                $url = $url;
                break;
        }
        return view("production.printing.{$report}_ajax", compact('data', 'url', 'editPermission', 'deletePermission', 'report'));
    }

    public function inward()
    {
        return view("production.inward.index");
    }

    public function inwardList(Request $request)
    {
        $request->validate([
            "date" => "required|date",
        ]);

        $date = date('Y-m-d', strtotime($request->date));
        $floors = Machine::select('location_id')->distinct()->get()->pluck('location_id');
        $data = [];
        foreach ($floors as $key => $floor) {
            $location = Location::find($floor)->name;
            $data[$location] = DB::table('moldings')
                ->select(
                    DB::raw('SUM(production_pieces_quantity) as production_pieces_quantity_sum'),
                    DB::raw('SUM(runner_waste) as runner_waste_sum'),
                    DB::raw('SUM(component_rejection) as component_rejection_sum'),
                    DB::raw('SUM(production_weight) as production_weight_sum'),
                )
                ->leftJoin('machines', 'moldings.machine_id', '=', 'machines.id')
                ->where('machines.location_id', $floor)
                ->whereDate('moldings.date', '=', $date)
                ->whereNull('moldings.deleted_at')
                ->where('moldings.branch_id', auth()->user()->branch_id)
                ->groupBy('machines.location_id')
                ->get()->toArray();
        }

        $printTypes = PrintType::all();
        $printTypesData = [];
        $estimate = [];
        $inward = [];
        foreach ($printTypes as $key => $value) {
            $printTypesData[$value->name] = DB::table('printings')
                ->select(
                    DB::raw('SUM(production_qty) as production_qty_sum'),
                )
                ->whereDate('printings.date', '=', $date)
                ->whereNull('printings.deleted_at')
                ->where('printings.branch_id', auth()->user()->branch_id)
                ->where('printings.print_type_id', $value->id)
                ->groupBy('printings.print_type_id')
                ->get()->toArray();

            $estimate[$value->name] = DB::table('estimates')
                ->select(
                    DB::raw('SUM(qty) as qty_sum'),
                )
                ->leftJoin('estimate_details', 'estimates.id', '=', 'estimate_details.estimate_id')
                ->where('estimate_details.print_type_id', $value->id)
                ->whereDate('estimates.date', '=', $date)
                ->whereNull('estimates.deleted_at')
                ->where('estimates.branch_id', auth()->user()->branch_id)
                ->groupBy('estimate_details.print_type_id')
                ->get()->toArray();

            if ($value->name == 'W/P') {
                $inward[$value->name] = DB::table('inwards')
                    ->select(
                        DB::raw('SUM(qty) as qty_sum'),
                    )
                    ->leftJoin('inward_details', 'inwards.id', '=', 'inward_details.inward_id')
                    ->whereDate('inwards.date', '=', $date)
                    ->whereNull('inwards.deleted_at')
                    ->where('inwards.branch_id', auth()->user()->branch_id)
                    ->get()->toArray();
            } else {
                $inward[$value->name] = 0;
            }
        }
        return view("production.inward.list", compact('data', 'date', 'printTypesData', 'estimate', 'inward'));
    }

    public function monthlyInward()
    {
        return view("production.monthly.index");
    }

    public function monthlyList(Request $request)
    {
        $request->validate([
            "fromDate" => "required|date",
            "toDate" => "required|date",
        ]);

        $fromDate = date('Y-m-d', strtotime($request->fromDate)) ?? date('Y-m-d');
        $toDate = date('Y-m-d', strtotime($request->toDate));

        $floors = Location::whereIn('name', ['Ground Floor', 'First Floor'])->get();
        $dates = [];

        // Print types (dispatch categories)
        $printTypes = PrintType::all();
        $estimate = [];
        $inward = [];
        $printTypesData = [];

        for ($date = $fromDate; $date <= $toDate; $date = date('Y-m-d', strtotime($date . ' +1 day'))) {
            $dates[] = date('d-m-Y', strtotime($date));
        }

        $data = [];

        foreach ($dates as $date) {
            $formattedDate = date('Y-m-d', strtotime($date));

            foreach ($floors as $floor) {
                $floorName = $floor->name;
                $floorId = $floor->id;

                $molding = DB::table('moldings')
                    ->select(
                        DB::raw('SUM(production_pieces_quantity) as production_pieces_quantity'),
                        DB::raw('SUM(runner_waste) as runner_waste'),
                        DB::raw('SUM(production_weight) as production_weight')
                    )
                    ->leftJoin('machines', 'moldings.machine_id', '=', 'machines.id')
                    ->where('machines.location_id', $floorId)
                    ->whereDate('moldings.date', $formattedDate)
                    ->whereNull('moldings.deleted_at')
                    ->where('moldings.branch_id', auth()->user()->branch_id)
                    ->first();

                $data[$floorName][$date] = [
                    'production_pieces_quantity' => $molding->production_pieces_quantity ?? 0,
                    'runner_waste' => $molding->runner_waste ?? 0,
                    'production_weight' => $molding->production_weight ?? 0,
                ];
            }

            // Packing data
            $packing = DB::table('inwards')
                ->select(
                    DB::raw('SUM(qty) as qty_sum'),
                )
                ->leftJoin('inward_details', 'inwards.id', '=', 'inward_details.inward_id')
                ->whereDate('inwards.date', '=', $formattedDate)
                ->whereNull('inwards.deleted_at')
                ->where('inwards.branch_id', auth()->user()->branch_id)
                ->first();
            $data['Packing'][$date] = [
                'qty' => $packing->qty_sum ?? 0,
            ];

            foreach ($printTypes as $type) {
                // Estimate data
                $estimateRow = DB::table('estimates')
                    ->select(DB::raw('SUM(qty) as qty_sum'))
                    ->leftJoin('estimate_details', 'estimates.id', '=', 'estimate_details.estimate_id')
                    ->where('estimate_details.print_type_id', $type->id)
                    ->whereDate('estimates.date', $formattedDate)
                    ->whereNull('estimates.deleted_at')
                    ->where('estimates.branch_id', auth()->user()->branch_id)
                    ->first();
                $estimate[$type->name][$date] = [
                    'qty_sum' => $estimateRow->qty_sum ?? 0,
                ];

                // Printing data
                $printRow = DB::table('printings')
                    ->select(DB::raw('SUM(production_qty) as production_qty_sum'))
                    ->where('printings.print_type_id', $type->id)
                    ->whereDate('printings.date', $formattedDate)
                    ->whereNull('printings.deleted_at')
                    ->where('printings.branch_id', auth()->user()->branch_id)
                    ->first();
                $printTypesData[$type->name][$date] = [
                    'production_qty_sum' => $printRow->production_qty_sum ?? 0,
                ];

                // Inward (only for W/P)
                if ($type->name == 'W/P') {
                    $inwardRow = DB::table('inwards')
                        ->select(DB::raw('SUM(qty) as qty_sum'))
                        ->leftJoin('inward_details', 'inwards.id', '=', 'inward_details.inward_id')
                        ->whereDate('inwards.date', $formattedDate)
                        ->whereNull('inwards.deleted_at')
                        ->where('inwards.branch_id', auth()->user()->branch_id)
                        ->first();
                    $inward[$type->name][$date] = [
                        'qty_sum' => $inwardRow->qty_sum ?? 0,
                    ];
                }
            }

            // checked

            $checkId = MonthlyReportCheck::where('date', $formattedDate)
                ->where('branch_id', auth()->user()->branch_id)
                ->value('id') ?? 0;
            $data['check'][$date] = [
                'check' => $checkId,
            ];

            $data['createdBy'][$date] = [
                'created_by' => User::select('users.name')
                    ->leftJoin('monthly_report_checks', 'users.id', '=', 'monthly_report_checks.created_by')
                    ->where('monthly_report_checks.date', $formattedDate)
                    ->where('monthly_report_checks.branch_id', auth()->user()->branch_id)
                    ->value('users.name') ?? '',
            ];
        }

        $floorLocation = $floors; // passing for table headers if needed

        return view("production.monthly.list", compact(
            'data',
            'dates',
            'floorLocation',
            'printTypes',
            'printTypesData',
            'estimate',
            'inward'
        ));
    }

    public function monthlyCheck(Request $request)
    {

        $request->validate([
            "date" => "required|date",
        ]);

        $date = date('Y-m-d', strtotime($request->date));

        $check = MonthlyReportCheck::where('date', $date)->where('branch_id', auth()->user()->branch_id)->first();
        if (!$check) {
            $data = MonthlyReportCheck::create([
                'date' => $date,
                'branch_id' => auth()->user()->branch_id,
                'created_by' => auth()->user()->id,
            ]);
        } else {
            return $this->withError("Monthly Report Check Already exists for this date");
        }

        if ($request->ajax()) {
            return $this->withSuccess("Monthly Report Check created successfully");
        }
        return $this->withSuccess("Monthly Report Check created successfully")->back();

    }
}
