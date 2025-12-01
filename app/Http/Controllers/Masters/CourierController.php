<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CourierController extends Controller implements HasMiddleware
{
    use DataTable;
    public static function middleware(): array
    {
        return [
            new Middleware('permission:courier-create', only: ['create']),
            new Middleware('permission:courier-view', only: ['index', "getList"]),
            new Middleware('permission:courier-edit', only: ['edit', "update"]),
            new Middleware('permission:courier-delete', only: ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("Master::courier.index");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|unique:couriers,name",
        ]);

        Courier::create([
            "name"       => $request->name,
            "created_by" => auth()->id()
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Courier created successfully");
        }
        return $this->withSuccess("Courier created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Courier $courier)
    {
        $request->validate([
            "name" => "required|unique:couriers,name," . $courier->id,
        ]);

        $courier->update([ "name" => $request->name ]);

        if ($request->ajax()) {
            return $this->withSuccess("Courier Updated successfully");
        }
        return $this->withSuccess("Courier Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Courier $courier)
    {
        $courier->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Courier delete successfully");
        }
        return $this->withSuccess("Courier delete successfully")->back();
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
        $this->model(model: Courier::class, with: [ "createdBy" ]);

        /* Add Filter here */
        $this->filter([
            // "status" => $request->status,
        ]);

        $editPermission   = $this->hasPermission("courier-edit");
        $deletePermission = $this->hasPermission("courier-delete");
        /* Add Formatting here */
        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("master.courier.delete", [ 'courier' => $row->id ]);
            
            $action = "";
            if ($editPermission) {
                $action .= " <a class='btn edit-btn  btn-action bg-success text-white me-2' 
                                data-id='{$row->id}'
                                data-name='{$row->name}'
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
                "action"     => $action,
                "created_by" => $row->createdBy?->displayName(),
                "created_at" => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at" => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }
}
