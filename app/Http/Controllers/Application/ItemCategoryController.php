<?php

namespace App\Http\Controllers\Application;

use App\Helpers\FileUpload;
use App\Http\Controllers\Controller;
use App\Models\ItemCategory;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ItemCategoryController extends Controller implements HasMiddleware
{
    use DataTable;
    public static function middleware(): array
    {
        return [
            new Middleware('permission:item_category-create', only: ['create']),
            new Middleware('permission:item_category-view', only: ['index', "getList"]),
            new Middleware('permission:item_category-edit', only: ['edit', "update"]),
            new Middleware('permission:item_category-delete', only: ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("Master::item_category.index");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
        ]);

        $validatedData['created_by'] = auth()->id();

        // Save other form data
        ItemCategory::create($validatedData);

        if ($request->ajax()) {
            return $this->withSuccess("Item Category created successfully");
        }
        return $this->withSuccess("Item Category created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ItemCategory $itemCategory)
    {
        $validatedData = $request->validate([
            'name' => 'required'
        ]);

        // Update other form data
        $itemCategory->update($validatedData);

        if ($request->ajax()) {
            return $this->withSuccess("Item Category Updated successfully");
        }
        return $this->withSuccess("Item Category Updated successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ItemCategory $itemCategory)
    {
        $itemCategory->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Item Category delete successfully");
        }
        return $this->withSuccess("Item Category delete successfully")->back();
    }

    public function getList(Request $request)
    {
        $searchableColumns = [
            'id',
            'name',
            "itemGroup:group_name",
        ];

        $this->model(model: ItemCategory::class, with: ["itemGroup", "createdBy"]);

        // $this->filter([ "status" => $request->status ]);

        $editPermission   = $this->hasPermission("item_category-edit");
        $deletePermission = $this->hasPermission("item_category-delete");
        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("master.item-category.delete", ['itemCategory' => $row->id]);
            $action = "";
            $image = $row->image ? $row->viewUrl : "";
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
                        <a class='btn btn-action bg-danger text-white me-2 btn-delete' data-bs-toggle='tooltip'
                            data-bs-placement='top' data-bs-original-title='Delete'
                            href='{$delete}'>
                            <i class='fa-solid fa-trash'></i>
                        </a>
                    ";
            }
            return [
                "id"              => $row->id,
                "action"          => $action,
                "name"            => $row->name,
                "created_by"      => $row->createdBy?->displayName(),
                "created_at"      => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at"      => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }
}
