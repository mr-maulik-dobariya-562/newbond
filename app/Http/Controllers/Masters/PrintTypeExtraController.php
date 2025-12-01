<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\PrintTypeExtra;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PrintTypeExtraController extends Controller implements HasMiddleware
{
    use DataTable;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:print_type_extra-create', only: ['create']),
            new Middleware('permission:print_type_extra-view', only: ['index', "getList"]),
            new Middleware('permission:print_type_extra-edit', only: ['edit', "update"]),
            new Middleware('permission:print_type_extra-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Master::print_type_extra.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name"       => "required|unique:print_type_extras,name",
            "code"       => "nullable",
        ]);

        PrintTypeExtra::create([
            "name"       => $request->name,
            "code"       => $request->code,
            "created_by" => auth()->id()
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Print Type Extra created successfully");
        }
        return $this->withSuccess("Print Type Extra created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PrintTypeExtra $printTypeExtra)
    {
        $request->validate([
            "name"       => "required|unique:print_type_extras,name," . $printTypeExtra->id,
            "code"       => "nullable",
        ]);

        $printTypeExtra->update([
            "name"       => $request->name,
            "code"       => $request->code,
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Print Type Extra Updated successfully");
        }
        return $this->withSuccess("Print Type Extra Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PrintTypeExtra $printTypeExtra)
    {
        $printTypeExtra->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Print Type Extra Deleted successfully");
        }
        return $this->withSuccess("Print Type Extra Deleted successfully")->back();
    }

    public function getList(Request $request)
    {
        $searchableColumns = [
            'id',
            'name',
            'code',
        ];

        $this->model(model: PrintTypeExtra::class);


        $editPermission   = $this->hasPermission("print_type_extra-edit");
        $deletePermission = $this->hasPermission("print_type_extra-delete");

        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("master.print-type-extra.delete", [ 'print_type_extra' => $row->id ]);
            $action = "";

            if ($editPermission) {
                $action .= "
                            <a class='btn edit-btn  btn-action bg-success text-white me-2'
                                data-id='{$row->id}'
                                data-name='{$row->name}'
                                data-code='{$row->code}'
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
                "code"       => $row->code,
                "action"     => $action,
                "created_by" => $row->createdBy?->displayName(),
                "created_at" => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at" => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }
}
