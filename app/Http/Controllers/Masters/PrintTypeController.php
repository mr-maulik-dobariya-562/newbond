<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\PrintType;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PrintTypeController extends Controller implements HasMiddleware
{
    use DataTable;
    public static function middleware(): array
    {
        return [
            new Middleware('permission:print_type-create', only: ['create']),
            new Middleware('permission:print_type-view', only: ['index', "getList"]),
            new Middleware('permission:print_type-edit', only: ['edit', "update"]),
            new Middleware('permission:print_type-delete', only: ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("Master::print_type.index");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|unique:print_type,name",
            "short_name" => "required|unique:print_type,short_name",
        ]);

        PrintType::create([
            "name"       => $request->name,
            "short_name"       => $request->short_name,
            "created_by" => auth()->id()
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Print Type created successfully");
        }
        return $this->withSuccess("Print Type created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PrintType $printType)
    {
        $request->validate([
            "name" => "required|unique:print_type,name," . $printType->id,
            "short_name" => "required|unique:print_type,short_name," . $printType->id,
        ]);

        $printType->update([ "name" => $request->name, 'short_name' => $request->short_name ]);

        if ($request->ajax()) {
            return $this->withSuccess("Print Type Updated successfully");
        }
        return $this->withSuccess("Print Type Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PrintType $printType)
    {
        $printType->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Print Type delete successfully");
        }
        return $this->withSuccess("Print Type delete successfully")->back();
    }

    public function getList(Request $request)
    {
        /* Define Searchable */
        $searchableColumns = [
            'id',
            'name',
            'createdBy:name',
        ];

        /* Add Model here with relation */
        $this->model(model: PrintType::class, with: [ "createdBy" ]);

        /* Add Filter here */
        $this->filter([
            // "status" => $request->status,
        ]);

        
        $editPermission   = $this->hasPermission("print_type-edit");
        $deletePermission = $this->hasPermission("print_type-delete");
        /* Add Formatting here */
        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("master.print-type.delete", [ 'print_type' => $row->id ]);
            $action = "";
            if ($editPermission) {
                $action = " <a class='btn edit-btn  btn-action bg-success text-white me-2' 
                                data-id='{$row->id}'
                                data-name='{$row->name}'
                                data-short_name='{$row->short_name}'
                                data-bs-toggle='tooltip' data-bs-placement='top' data-bs-original-title='Edit' href='javascript:void(0);'>
                                <i class='far fa-edit' aria-hidden='true'></i>
                            </a>";
            }
            if ($deletePermission) {
                $action .= " <a class='btn btn-action bg-danger text-white me-2 btn-delete' data-bs-toggle='tooltip'
                                data-bs-placement='top' data-bs-original-title='Delete'
                                href='{$delete}'>
                                <i class='fa-solid fa-trash'></i>
                            </a>";
            }
            return [
                "id"         => $row->id,
                "name"       => $row->name,
                "short_name"       => $row->short_name,
                "action"     => $action,
                "created_by" => $row->createdBy?->displayName(),
                "created_at" => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at" => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }
}
