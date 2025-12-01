<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Size;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SizeController extends Controller implements HasMiddleware
{
    use DataTable;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:size-create', only: ['create']),
            new Middleware('permission:size-view', only: ['index', "getList"]),
            new Middleware('permission:size-edit', only: ['edit', "update"]),
            new Middleware('permission:size-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Master::size.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name"       => "required|unique:sizes,name",
            "symbol"     => "required|unique:sizes,symbol",
        ]);

        Size::create([
            "name"       => $request->name,
            "symbol"     => $request->symbol,
            "created_by" => auth()->id()
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Size created successfully");
        }
        return $this->withSuccess("Size created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Size $size)
    {
        $request->validate([
            "name"   => "required|unique:sizes,name," . $size->id,
            "symbol" => "required|unique:sizes,symbol," . $size->id,
        ]);

        $size->update([
            "name"   => $request->name,
            "symbol" => $request->symbol,
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Size Updated successfully");
        }
        return $this->withSuccess("Size Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Size $size)
    {
        $size->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Size Deleted successfully");
        }
        return $this->withSuccess("Size Deleted successfully")->back();
    }

    public function getList(Request $request)
    {
        $searchableColumns = [
            'id',
            'name',
            'symbol',
        ];

        $this->model(model: Size::class);


        $editPermission   = $this->hasPermission("size-edit");
        $deletePermission = $this->hasPermission("size-delete");

        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("master.size.delete", [ 'size' => $row->id ]);
            $action = "";

            if ($editPermission) {
                $action .= "
                            <a class='btn edit-btn  btn-action bg-success text-white me-2'
                                data-id='{$row->id}'
                                data-name='{$row->name}'
                                data-symbol='{$row->symbol}'
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
                "symbol"     => $row->symbol,
                "action"     => $action,
                "created_by" => $row->createdBy?->displayName(),
                "created_at" => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at" => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }
}
