<?php

namespace App\Http\Controllers\manufactureMaster;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Location;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class EmployeeController extends Controller implements HasMiddleware
{
    use DataTable;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:employee-create', only: ['create']),
            new Middleware('permission:employee-view', only: ['index', "getList"]),
            new Middleware('permission:employee-edit', only: ['edit', "update"]),
            new Middleware('permission:employee-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = Location::all();
        return view('manufactureMaster.employee.index', compact('locations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name"       => "required|unique:employees,name",
            'mobile'     => 'required|unique:employees,mobile|nullable',
            'email'      => 'required|unique:employees,email|nullable',
            'status'     => 'required|in:ACTIVE,INACTIVE',
            'address'    => 'required|nullable',
            'type'       => 'required|in:Printing,Molding,Both',
            'location'   => 'required'
        ]);

        Employee::create([
            "name"       => $request->name,
            "email"      => $request->email,
            "mobile"     => $request->mobile,
            "address"    => $request->address,
            "status"     => $request->status,
            "type"       => $request->type,
            "location_id" => implode(",", $request->location),
            "created_by" => auth()->id()
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Employee created successfully");
        }
        return $this->withSuccess("Employee created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            "name"       => "required|unique:employees,name," . $employee->id,
            'mobile'     => 'required|unique:employees,mobile,' . $employee->id . '|nullable',
            'email'      => 'required|unique:employees,email,' . $employee->id . '|nullable',
            'status'     => 'required|in:ACTIVE,INACTIVE',
            'address'    => 'required|nullable',
            'type'       => 'required|in:Printing,Molding,Both',
            'location'   => 'required'
        ]);

        $employee->update([
            "name"       => $request->name,
            "email"      => $request->email,
            "mobile"     => $request->mobile,
            "address"    => $request->address,
            "status"     => $request->status,
            "type"       => $request->type,
            "location_id" => implode(",", $request->location),
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Employee Updated successfully");
        }
        return $this->withSuccess("Employee Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Employee Deleted successfully");
        }
        return $this->withSuccess("Employee Deleted successfully")->back();
    }

    public function getList(Request $request)
    {
        $searchableColumns = [
            'id',
            'name',
        ];

        $this->model(model: Employee::class);

        /* Add Filter here */
        $this->filter([
            "location_id" => $request->location_id,
        ]);

        $editPermission   = $this->hasPermission("employee-edit");
        $deletePermission = $this->hasPermission("employee-delete");

        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("manufacture-master.employee.delete", ['employee' => $row->id]);
            $action = "";

            if ($editPermission) {
                $action .= "
                            <a class='btn edit-btn  btn-action bg-success text-white me-2'
                                data-id='{$row->id}'
                                data-name='{$row->name}'
                                data-email='{$row->email}'
                                data-mobile='{$row->mobile}'
                                data-address='{$row->address}'
                                data-status='{$row->status}'
                                data-type='{$row->type}'
                                data-location='{$row->location_id}'
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
                "id"         => $row->id,
                "name"       => $row->name,
                "email"      => $row->email,
                "mobile"     => $row->mobile,
                "address"    => $row->address,
                "status"     => $row->status,
                "action"     => $action,
                "location"   => Location::whereIn('id', explode(',', $row->location_id ?? ''))->pluck('name')->implode(", "),
                "created_by" => $row->createdBy?->displayName(),
                "created_at" => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at" => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }
}
