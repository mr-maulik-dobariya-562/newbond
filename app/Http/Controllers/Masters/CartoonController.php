<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Cartoon;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CartoonController extends Controller implements HasMiddleware
{
    use DataTable;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:cartoon-create', only: ['create']),
            new Middleware('permission:cartoon-view', only: ['index', "getList"]),
            new Middleware('permission:cartoon-edit', only: ['edit', "update"]),
            new Middleware('permission:cartoon-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("Master::cartoon.index");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "size"       => "required|unique:cartoons,size",
        ]);

        Cartoon::create([
            "size"       => $request->size,
            "created_by" => auth()->id(),
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Cartoon created successfully");
        }
        return $this->withSuccess("Cartoon created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cartoon $cartoon)
    {
        $request->validate([
            "size" => "required|unique:cartoons,size," . $cartoon->id,
        ]);

        $cartoon->update([
            "size"       => $request->size,
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Cartoon Updated successfully");
        }
        return $this->withSuccess("Cartoon Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cartoon $cartoon)
    {
        $cartoon->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Cartoon delete successfully");
        }
        return $this->withSuccess("Cartoon delete successfully")->back();
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
        $this->model(model: Cartoon::class, with: [ "createdBy" ]);

        /* Add Filter here */
        $this->filter([
            // "status" => $request->status,
        ]);


        $editPermission   = $this->hasPermission("cartoon-edit");
        $deletePermission = $this->hasPermission("cartoon-delete");

        /* Add Formatting here */
        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("master.cartoon.delete", [ 'cartoon' => $row->id ]);
            $action = "";
            if ($editPermission) {
                $action .= " <a class='btn edit-btn  btn-action bg-success text-white me-2'
                                    data-id='{$row->id}'
                                    data-size='{$row->size}'
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
                "action"     => $action,
                "size"       => $row->size,
                "created_by" => $row->createdBy?->displayName(),
                "created_at" => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at" => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }
}
