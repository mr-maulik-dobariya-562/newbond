<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Cavity;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Item;
use App\Models\Location;
use App\Models\Machine;
use App\Models\Molding;
use App\Models\ProductType;
use App\Models\RowMaterial;
use App\Models\Shift;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class MoldingController extends Controller implements HasMiddleware
{
    use DataTable;
    protected $molding;
    public function __construct(Molding $molding)
    {
        $this->molding = $molding;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:molding-create', only: ['create']),
            new Middleware('permission:molding-view', only: ['index', "getList"]),
            new Middleware('permission:molding-edit', only: ['edit', "update"]),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        if (isset($user->location_id)) {
            $machines = Machine::with('location')->where('location_id', '!=', 3)->whereIn('location_id', explode(',', $user->location_id))->get();
        } else {
            $machines = Machine::with('location')->where('location_id', '!=', 3)->get();
        }
        $machineData = Machine::with('machineType')->where('location_id', '!=', 3)->get();
        $rowMaterials = RowMaterial::all();
        $cavitys = Cavity::all();
        $shifts = Shift::all();
        $productTypes = ProductType::all();
        $items = Item::all();
        $location = Location::all();
        $customers = Employee::where('status', 'ACTIVE')->whereIn('type', ['Both', 'Molding'])->get();
        return view('production.molding.index', compact('machines', 'rowMaterials', 'cavitys', 'shifts', 'productTypes', 'customers', 'items', 'machineData', 'location'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "date" => "required|date",
            "shift_id" => "required",
            "machine_id" => "required",
            "operator_id" => "required",
            "product_type_id" => "required",
            "row_material_id" => "required",
            "cavity_id" => "required",
            "item_id" => "required",
            "machine_counter" => "required",
            "production_weight" => "required",
            "production_pieces_quantity" => "required",
            "runner_waste" => "required",
            "component_rejection" => "required",
            "color_type" => "required",
        ]);

        Molding::create([
            "date" => $request->date,
            "shift_id" => $request->shift_id,
            "machine_id" => $request->machine_id,
            "operator_id" => implode(",", $request->operator_id),
            "product_type_id" => $request->product_type_id,
            "row_material_id" => $request->row_material_id,
            "cavity_id" => $request->cavity_id,
            "item_id" => $request->item_id,
            "machine_counter" => $request->machine_counter,
            "production_weight" => $request->production_weight,
            "production_pieces_quantity" => $request->production_pieces_quantity,
            "runner_waste" => $request->runner_waste,
            "component_rejection" => $request->component_rejection,
            "color_type" => $request->color_type,
            "created_by" => auth()->id()
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Machine created successfully");
        }
        return $this->withSuccess("Machine created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Molding $molding)
    {
        $request->validate([
            "date" => "required|date",
            "shift_id" => "required",
            "machine_id" => "required",
            "operator_id" => "required",
            "product_type_id" => "required",
            "row_material_id" => "required",
            "cavity_id" => "required",
            "item_id" => "required",
            "machine_counter" => "required",
            "production_weight" => "required",
            "production_pieces_quantity" => "required",
            "runner_waste" => "required",
            "component_rejection" => "required",
            "color_type" => "required",
        ]);

        $molding->update([
            "date" => $request->date,
            "shift_id" => $request->shift_id,
            "machine_id" => $request->machine_id,
            "operator_id" => implode(",", $request->operator_id),
            "product_type_id" => $request->product_type_id,
            "row_material_id" => $request->row_material_id,
            "cavity_id" => $request->cavity_id,
            "item_id" => $request->item_id,
            "machine_counter" => $request->machine_counter,
            "production_weight" => $request->production_weight,
            "production_pieces_quantity" => $request->production_pieces_quantity,
            "runner_waste" => $request->runner_waste,
            "component_rejection" => $request->component_rejection,
            "color_type" => $request->color_type,
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Molding Updated successfully");
        }
        return $this->withSuccess("Molding Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Molding $molding)
    {
        $molding->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Molding Deleted successfully");
        }
        return $this->withSuccess("Molding Deleted successfully")->back();
    }

    public function getList(Request $request)
    {
        $editPermission = $this->hasPermission("molding-edit");
        $deletePermission = $this->hasPermission("molding-delete");
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
                $data = $this->molding->getSalesGroupByOperator($request);
                $url = $url;
                break;
            case 'machine':
                $data = $this->molding->getSalesGroupByMachine($request);
                $url = $url;
                break;
            case 'voucher':
                $data = $this->molding->getSalesGroupByVoucher($request);
                $url = $url;
                break;
        }
        return view("production.molding.{$report}_ajax", compact('data', 'url', 'editPermission', 'deletePermission', 'report'));
    }

    public function getoperator(Request $request)
    {
        $location = Machine::where('id', $request->machine_id)->first();
        $operators = Employee::where('status', 'ACTIVE')
            ->whereRaw('FIND_IN_SET(?, location_id)', [$location->location_id ?? ''])
            ->whereIn('type', ['Both', 'Molding'])
            ->select('name', 'id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item['id'] => $item['name']];
            });
        return response()->json($operators);
    }
}
