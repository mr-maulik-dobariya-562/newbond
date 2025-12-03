<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ItemController extends Controller implements HasMiddleware
{
    use DataTable;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:item-create', only: ['create']),
            new Middleware('permission:item-view', only: ['index', "getList"]),
            new Middleware('permission:item-edit', only: ['edit', "update"]),
            new Middleware('permission:item-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories  = ItemCategory::get();
        return view("Master::item.index", compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "name"                  => "required|string|max:255",
            "categories_id"         => "required|string|max:255",
            "price"                 => "nullable|numeric",
        ]);

        if ($request->hasFile('image')) {
            $file                   = $request->file('image');
            $image = FileUpload::upload($file, 'item', app("storage"));
        }
        $item = Item::create([
            "name"                  => $request->name,
            "categories_id"         => $request->categories_id,
            "price"                 => $request->price,
            "image"                 => $image ?? null,
            "created_by"            => auth()->id()
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Item created successfully");
        }
        return $this->withSuccess("Item created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            "name"                  => "required|string|max:255|unique:items,name," . $item->id,
            "categories_id"         => "required|string|max:255",
            "price"                 => "nullable|numeric",
        ]);

        if ($request->hasFile('image')) {
            $file                   = $request->file('image');
            $image = FileUpload::upload($file, 'item', app("storage"));
        }
        $item->update([
            "name"                  => $request->name,
            "categories_id"         => $request->categories_id,
            "price"                 => $request->price,
            "image"                 => $image ?? $item->image,
            "created_by"            => auth()->id()
        ]);

        if ($request->ajax()) {
            return $this->withSuccess("Item created successfully");
        }
        return $this->withSuccess("Item created successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        $item->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Item delete successfully");
        }
        return $this->withSuccess("Item delete successfully")->back();
    }

    public function getList(Request $request)
    {
        /* Define Searchable */
        $searchableColumns = [
            'id',
            'name',
            "price",
            'createdBy:name',
            "categories:name",
        ];

        /* Add Model here with relation */
        $this->model(model: Item::class, with: ["createdBy","categories"]);

        /* Add Filter here */
        $this->filter([
            "categories_id" => $request->categories,
        ]);


        $editPermission   = $this->hasPermission("item-edit");
        $deletePermission = $this->hasPermission("item-delete");


        /* Add Formatting here */
        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("master.item.delete", ['item' => $row->id]);
            $action = "";
            $action = " <a class='btn edit-btn  btn-action bg-success text-white mb-1'
                            data-id='{$row->id}'
                            data-permission='{$editPermission}'
                            data-bs-toggle='tooltip' data-bs-placement='top' data-bs-original-title='Edit' href='javascript:void(0);'>
                            <i class='far fa-edit' aria-hidden='true'></i>
                        </a>
                        ";

            if ($deletePermission) {
                $action .= " <a class='btn btn-action bg-danger text-white mb-1 btn-delete' data-bs-toggle='tooltip'
                            data-bs-placement='top' data-bs-original-title='Delete'
                            href='{$delete}'>
                            <i class='fa-solid fa-trash'></i>
                        </a>";
            }

            $image = "";
            if ($row->image) {
                $url = FileUpload::url($row->image, "item");
                $image = $url ? "<img src='{$url}' style='width:70px; height:50px;'/>" : "<img src='https://via.placeholder.com/50' style='width:70px; height:50px;'/>";
            } else {
                $image = "";
            }

            return [
                "id"                    => $row->id,
                "name"                  => $row->name,
                "categories"            => $row->categories->name ?? '',
                "price"                 => $row->price,
                "action"                => $action,
                "image"                 => $image,
                "created_by"            => $row->createdBy?->displayName(),
                "created_at"            => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at"            => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }

    public function modelForm(Request $request)
    {
        if ($request->id) {
            $categories  = ItemCategory::get();
            $item        = Item::where("id", $request->id)->first();
            return view("Master::item.model", compact("item", "categories"));
        }else {
            $categories  = ItemCategory::get();
            return view("Master::item.model", compact("categories"));
        }
        return false;
    }
}
