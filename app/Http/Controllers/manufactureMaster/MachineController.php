<?php

namespace App\Http\Controllers\manufactureMaster;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Machine;
use App\Models\MachineType;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MachineController extends Controller implements HasMiddleware
{
    use DataTable;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:machine-create', only: ['create']),
            new Middleware('permission:machine-view', only: ['index', "getList"]),
            new Middleware('permission:machine-edit', only: ['edit', "update"]),
            new Middleware('permission:machine-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $machineTypes = MachineType::all();
        $locations = Location::all();
        return view('manufactureMaster.machine.index', compact('machineTypes', 'locations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name"       => "required|unique:machines,name",
            "machine_type_id" => "required",
            "location_id" => "required",
        ]);

        Machine::create([
            "name"       => $request->name,
            "machine_type_id" => $request->machine_type_id,
            "location_id" => $request->location_id,
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
    public function update(Request $request, Machine $machine)
    {
        $request->validate([
            "name"       => "required|unique:machines,name," . $machine->id,
            "machine_type_id" => "required",
            "location_id" => "required",
        ]);

        $machine->update([
            "name"       => $request->name,
            "machine_type_id" => $request->machine_type_id,
            "location_id" => $request->location_id
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Machine Updated successfully");
        }
        return $this->withSuccess("Machine Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Machine $machine)
    {
        $machine->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Machine Deleted successfully");
        }
        return $this->withSuccess("Machine Deleted successfully")->back();
    }

    public function getList(Request $request)
    {
        $searchableColumns = [
            'id',
            'name',
        ];

        $this->model(model: Machine::class);


        $editPermission   = $this->hasPermission("machine-edit");
        $deletePermission = $this->hasPermission("machine-delete");

        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("manufacture-master.machine.delete", ['machine' => $row->id]);
            $action = "";

            if ($editPermission) {
                $action .= "
                            <a class='btn edit-btn  btn-action bg-success text-white me-2'
                                data-id='{$row->id}'
                                data-name='{$row->name}'
                                data-machine_type_id='{$row->machine_type_id}'
                                data-location_id='{$row->location_id}'
                                data-bs-toggle='tooltip' data-bs-placement='top' data-bs-original-title='Edit' href='javascript:void(0);'>
                                <i class='far fa-edit' aria-hidden='true'></i>
                            </a>
                        ";
            }
            if ($deletePermission) {
                $action .= "
                            <a class='btn btn-action bg-danger text-white me-2 btn-delete'
                                data-id='{$row->id}'
                                data-bs-toggle='tooltip'
                                data-bs-placement='top' data-bs-original-title='Delete'
                                href='{$delete}'>
                                <i class='fa-solid fa-trash'></i>
                            </a>
                        ";
            }

            return [
                "id"            => $row->id,
                "name"          => $row->name,
                "location"      => $row->location->name,
                "machine_type"  => $row->machineType->name,
                "action"        => $action,
                "created_by"    => $row->createdBy?->displayName(),
                "created_at"    => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at"    => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }
}
