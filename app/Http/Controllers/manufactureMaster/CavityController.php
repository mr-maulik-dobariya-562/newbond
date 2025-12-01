<?php

namespace App\Http\Controllers\manufactureMaster;

use App\Http\Controllers\Controller;
use App\Models\Cavity;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CavityController extends Controller implements HasMiddleware
{
    use DataTable;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:cavity-create', only: ['create']),
            new Middleware('permission:cavity-view', only: ['index', "getList"]),
            new Middleware('permission:cavity-edit', only: ['edit', "update"]),
            new Middleware('permission:cavity-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('manufactureMaster.cavity.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name"       => "required|unique:cavities,name",
        ]);

        Cavity::create([
            "name"       => $request->name,
            "created_by" => auth()->id()
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Cavity created successfully");
        }
        return $this->withSuccess("Cavity created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cavity $cavity)
    {
        $request->validate([
            "name"       => "required|unique:cavities,name," . $cavity->id,
        ]);

        $cavity->update([
            "name"       => $request->name,
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Cavity Updated successfully");
        }
        return $this->withSuccess("Cavity Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cavity $cavity)
    {
        $cavity->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Cavity Deleted successfully");
        }
        return $this->withSuccess("Cavity Deleted successfully")->back();
    }

    public function getList(Request $request)
    {
        $searchableColumns = [
            'id',
            'name',
        ];

        $this->model(model: Cavity::class);


        $editPermission   = $this->hasPermission("cavity-edit");
        $deletePermission = $this->hasPermission("cavity-delete");

        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("manufacture-master.cavity.delete", ['cavity' => $row->id]);
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
