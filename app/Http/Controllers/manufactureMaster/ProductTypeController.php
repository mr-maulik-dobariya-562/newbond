<?php

namespace App\Http\Controllers\manufactureMaster;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProductTypeController extends Controller implements HasMiddleware
{
    use DataTable;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:product-type-create', only: ['create']),
            new Middleware('permission:product-type-view', only: ['index', "getList"]),
            new Middleware('permission:product-type-edit', only: ['edit', "update"]),
            new Middleware('permission:product-type-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('manufactureMaster.productType.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name"       => "required|unique:product_types,name",
        ]);

        ProductType::create([
            "name"       => $request->name,
            "created_by" => auth()->id()
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Product Type created successfully");
        }
        return $this->withSuccess("Product Type created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductType $productType)
    {
        $request->validate([
            "name"       => "required|unique:product_types,name," . $productType->id,
        ]);

        $productType->update([
            "name"       => $request->name,
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Product Type Updated successfully");
        }
        return $this->withSuccess("Product Type Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductType $productType)
    {
        $productType->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Product Type Deleted successfully");
        }
        return $this->withSuccess("Product Type Deleted successfully")->back();
    }

    public function getList(Request $request)
    {
        $searchableColumns = [
            'id',
            'name',
        ];

        $this->model(model: ProductType::class);


        $editPermission   = $this->hasPermission("product-type-edit");
        $deletePermission = $this->hasPermission("product-type-delete");

        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("manufacture-master.product-type.delete", ['productType' => $row->id]);
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
