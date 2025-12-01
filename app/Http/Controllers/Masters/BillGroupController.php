<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\BillGroup;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class BillGroupController extends Controller implements HasMiddleware
{
    use DataTable;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:bill-group-create', only: ['create']),
            new Middleware('permission:bill-group-view', only: ['index', "getList"]),
            new Middleware('permission:bill-group-edit', only: ['edit', "update"]),
            new Middleware('permission:bill-group-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Master::bill_group.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name"       => "required|unique:bill_groups,name",
        ]);

        BillGroup::create([
            "name"       => $request->name,
            "created_by" => auth()->id()
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Bill Group created successfully");
        }
        return $this->withSuccess("Bill Group created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BillGroup $billGroup)
    {
        $request->validate([
            "name"       => "required|unique:bill_groups,name," . $billGroup->id,
        ]);

        $billGroup->update([
            "name"       => $request->name,
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Bill Group Updated successfully");
        }
        return $this->withSuccess("Bill Group Updated successfully")->back();   
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BillGroup $billGroup)
    {
        $billGroup->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Bill Group Deleted successfully");
        }
        return $this->withSuccess("Bill Group Deleted successfully")->back();
    }

    public function getList(Request $request)
    {
        $searchableColumns = [
            'id',
            'name',
        ];

        $this->model(model: BillGroup::class);

        
        $editPermission   = $this->hasPermission("bill-group-edit");
        $deletePermission = $this->hasPermission("bill-group-delete");

        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("master.bill-group.delete", [ 'bill_group' => $row->id ]);
            $action = "";

            if ($editPermission) {
                $action .= "
                            <a class='btn edit-btn  btn-action bg-success text-white me-2' 
                                data-id='{$row->id}'
                                data-name='{$row->name}'
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
                "action"     => $action,
                "created_by" => $row->createdBy?->displayName(),
                "created_at" => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at" => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }
}
