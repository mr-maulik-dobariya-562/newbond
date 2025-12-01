<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
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
            "name"                  => "required|string|max:255|unique:items,name",
            "categories_id"         => "required|string|max:255",
            "extra_retail_discount" => "required|numeric",
            "extra_dealer_discount" => "required|numeric",
            "dealer_current_price"  => "required|numeric",
            "retail_current_price"  => "required|numeric",
            "usd_current_price"  => "required|numeric",
            "dealer_old_price"      => "required|numeric",
            "retail_old_price"      => "required|numeric",
            "usd_old_price"      => "required|numeric",
            "minimum_qty"           => "required|integer",
            "packing"               => "required|string|max:255",
            "type"                  => "required|in:Finish,Raw,Semi-Finished",
            "active_type"           => "required|in:Active,Non Active,Offline",
            "print_type_id"            => "required|array",
            "print_type_id.*"          => "required|integer",
        ]);

        if ($request->hasFile('image')) {
            $file                   = $request->file('image');
            $image = FileUpload::upload($file, 'item', app("storage"));
        }
        $item = Item::create([
            "name"                  => $request->name,
            "categories_id"         => $request->categories_id,
            "extra_retail_discount" => $request->extra_retail_discount,
            "extra_dealer_discount" => $request->extra_dealer_discount,
            "dealer_current_price"  => $request->dealer_current_price,
            "retail_current_price"  => $request->retail_current_price,
            "usd_current_price"     => $request->usd_current_price,
            "dealer_old_price"      => $request->dealer_old_price,
            "retail_old_price"      => $request->retail_old_price,
            "usd_old_price"         => $request->usd_old_price,
            'local_size_id'         => $request->local_size_id,
            'export_size_id'        => $request->export_size_id,
            'export_weight'         => $request->export_weight,
            'local_weight'          => $request->local_weight,
            "minimum_qty"           => $request->minimum_qty,
            "packing"               => $request->packing,
            "type"                  => $request->type,
            "active_type"           => $request->active_type,
            "image"                 => $image ?? null,
            "created_by"            => auth()->id()
        ]);

        $lastInsertedId = $item->id;

        // Handle array inputs
        foreach ($validated['print_type_id'] as $index => $item) {
            // Assuming you have a relationship defined on the ItemGroup model to handle these arrays
            ItemDetail::create([
                'item_id'       => $lastInsertedId,
                'print_type_id' => $item,
                'checkbox'      => isset($request->checkbox[$index]) && $request->checkbox[$index] ? '1' : '0',
                "created_by"    => auth()->id()
            ]);
        }

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
            "extra_retail_discount" => "required|numeric",
            "extra_dealer_discount" => "required|numeric",
            "dealer_current_price"  => "required|numeric",
            "retail_current_price"  => "required|numeric",
            "usd_current_price"     => "required|numeric",
            "dealer_old_price"      => "required|numeric",
            "retail_old_price"      => "required|numeric",
            "usd_old_price"         => "required|numeric",
            "minimum_qty"           => "required|integer",
            "packing"               => "required|string|max:255",
            "type"                  => "required|in:Finish,Raw,Semi-Finished",
            "active_type"           => "required|in:Active,Non Active,Offline",
            "print_type_id"         => "required|array",
            "print_type_id.*"       => "required|integer",
        ]);

        if ($request->hasFile('image')) {
            $file                   = $request->file('image');
            $image = FileUpload::upload($file, 'item', app("storage"));
        }
        $item->update([
            "name"                  => $request->name,
            "categories_id"         => $request->categories_id,
            "extra_retail_discount" => $request->extra_retail_discount,
            "extra_dealer_discount" => $request->extra_dealer_discount,
            "dealer_current_price"  => $request->dealer_current_price,
            "retail_current_price"  => $request->retail_current_price,
            "usd_current_price"     => $request->usd_current_price,
            "dealer_old_price"      => $request->dealer_old_price,
            "retail_old_price"      => $request->retail_old_price,
            "usd_old_price"         => $request->usd_old_price,
            'local_size_id'         => $request->local_size_id,
            'export_size_id'        => $request->export_size_id,
            'export_weight'           => $request->export_weight,
            'local_weight'            => $request->local_weight,
            "minimum_qty"           => $request->minimum_qty,
            "packing"               => $request->packing,
            "type"                  => $request->type,
            "active_type"           => $request->active_type,
            "image"                 => $image ?? $item->image,
            "created_by"            => auth()->id()
        ]);

        ItemDetail::where('item_id', $item->id)->delete();

        // Handle array inputs
        foreach ($validated['print_type_id'] as $index => $itemdata) {
            // Assuming you have a relationship defined on the ItemGroup model to handle these arrays
            ItemDetail::create([
                'item_id'       => $item->id,
                'print_type_id' => $itemdata,
                'checkbox'      => isset($request->checkbox[$index]) && $request->checkbox[$index] ? '1' : '0',
                "created_by"    => auth()->id()
            ]);
        }

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
        $item->itemDetails()->delete();
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
            "extra_retail_discount",
            'extra_dealer_discount',
            'createdBy:name',
            "categories:name",
            "type",
            "active_type",
            "packing",
            "minimum_qty",
            "dealer_current_price",
            "retail_current_price",
            "usd_current_price",
            "dealer_old_price",
            "retail_old_price",
            "usd_old_price",
        ];

        /* Add Model here with relation */
        $this->model(model: Item::class, with: ["createdBy","categories"]);

        /* Add Filter here */
        $this->filter([
            "active_type" => $request->activeType,
            "categories_id" => $request->categories,
            "type" => $request->itemType,
        ]);


        $editPermission   = $this->hasPermission("item-edit");
        $deletePermission = $this->hasPermission("item-delete");


        /* Add Formatting here */
        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("item-master.item.delete", ['item' => $row->id]);
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
                "extra_retail_discount" => $row->extra_retail_discount,
                "extra_dealer_discount" => $row->extra_dealer_discount,
                "type"                  => $row->type,
                "active_type"           => $row->active_type,
                "packing"               => $row->packing,
                "minimum_qty"           => $row->minimum_qty,
                "dealer_current_price"  => $row->dealer_current_price,
                "retail_current_price"  => $row->retail_current_price,
                "usd_current_price"     => $row->usd_current_price,
                "dealer_old_price"      => $row->dealer_old_price,
                "retail_old_price"      => $row->retail_old_price,
                "usd_old_price"         => $row->usd_old_price,
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
            $printTypes  = PrintType::get();
            $categories  = ItemCategory::get();
            $item        = Item::where("id", $request->id)->first();
            $cartoons = Cartoon::get();
            foreach ($printTypes as $key => $value) {
                $itemDetails = ItemDetail::where("item_id", $request->id)->where("print_type_id", $value->id)->first();
                $value->checkbox = !empty($itemDetails->checkbox) && $itemDetails->checkbox == '1' ? '1' : '0';
            }
            return view("Master::item.model", compact("printTypes", "item", "itemDetails", "categories", "cartoons"));
        } else {
            $printTypes = PrintType::get();
            $categories  = ItemCategory::get();
            $cartoons = Cartoon::get();
            return view("Master::item.model", compact("printTypes", "categories", "cartoons"));
        }
        return false;
    }
}
