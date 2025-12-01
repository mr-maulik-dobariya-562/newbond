<?php

namespace App\Http\Controllers\Masters;

use App\Helpers\FileUpload;
use App\Http\Controllers\Controller;
use App\Models\CaseType;
use App\Models\ItemGroup;
use App\Models\ItemGroupDetail;
use App\Models\ItemGroupPrice;
use App\Models\ItemGroupPrintExtra;
use App\Models\PrintGroupImage;
use App\Models\PrintType;
use App\Models\PrintTypeExtra;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ItemGroupController extends Controller implements HasMiddleware
{
    use DataTable;
    public static function middleware(): array
    {
        return [
            new Middleware('permission:item_group-create', only: ['create']),
            new Middleware('permission:item_group-view', only: ['index', "getList"]),
            new Middleware('permission:item_group-edit', only: ['edit', "update"]),
            new Middleware('permission:item_group-delete', only: ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("Master::item_group.index");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "group_name"          => "required|string|max:255",
            "sequence_number"     => "required|integer",
            "gst"                 => "required",
            "retail_wp_available" => "required|in:YES,NO",
            "case_type_id"        => "required|integer",
            "extra_price"         => "required|array",
            "extra_price.*"       => "required|integer",
            "usd_extra_price"     => "required|array",
            "usd_extra_price.*"   => "required|integer",
            "min_dealer"          => "required|array",
            "min_dealer.*"        => "required|integer",
            "total_dealer"        => "required|array",
            "total_dealer.*"      => "required|integer",
            "min_retail"          => "required|array",
            "min_retail.*"        => "required|integer",
            "total_retail"        => "required|array",
            "total_retail.*"      => "required|integer",
        ]);

        $itemGroup = ItemGroup::create([
            "group_name"      => $request->group_name,
            "sequence_number" => $request->sequence_number,
            "bill_title"      => $request->bill_title,
            "gst"             => $request->gst,
            "case_type_id"    => $request->case_type_id,
            "created_by"      => auth()->id()
        ]);

        $lastInsertedId = $itemGroup->id;

        // Handle array inputs
        foreach ($validated['min_dealer'] as $index => $minDealer) {
            // Assuming you have a relationship defined on the ItemGroup model to handle these arrays
            ItemGroupDetail::create([
                'item_group_id' => $lastInsertedId,
                'print_type_id' => $request->print_type_id[$index],
                'min_dealer'    => $minDealer,
                'total_dealer'  => $validated['total_dealer'][$index],
                'min_retail'    => $validated['min_retail'][$index],
                'total_retail'  => $validated['total_retail'][$index],
                "created_by"    => auth()->id()
            ]);
        }
        // Handle array inputs
        foreach ($validated['extra_price'] as $index => $extraPrice) {
            // Assuming you have a relationship defined on the ItemGroup model to handle these arrays
            ItemGroupPrice::create([
                'item_group_id' => $lastInsertedId,
                'print_type_id' => $request->print_type_id[$index],
                'extra_price'   => $extraPrice,
                'usd_extra_price' => $request->usd_extra_price[$index],
            ]);
        }

        foreach ($request->print_extra as $index => $printExtra) {
            ItemGroupPrintExtra::updateOrCreate([
                'item_group_id'  => $itemGroup->id,
                'print_extra_id' => $printExtra,
            ], [
                'amount'         => $request->amount[$index],
                'created_by'     => auth()->id()
            ]);
        }

        if ($request->ajax()) {
            return $this->withSuccess("Item Group created successfully");
        }
        return $this->withSuccess("Item Group created successfully")->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ItemGroup $itemGroup)
    {
        $validated = $request->validate([
            "group_name"          => "required|string|max:255",
            "sequence_number"     => "required|numeric",
            "gst"                 => "required",
            "case_type_id"       => "required|integer",
            "retail_wp_available" => "required|in:YES,NO",
            "extra_price"         => "required|array",
            "extra_price.*"       => "required|numeric",
            "usd_extra_price"     => "required|array",
            "usd_extra_price.*"   => "required|numeric",
            "min_dealer"          => "required|array",
            "min_dealer.*"        => "required|numeric",
            "total_dealer"        => "required|array",
            "total_dealer.*"      => "required|numeric",
            "min_retail"          => "required|array",
            "min_retail.*"        => "required|numeric",
            "total_retail"        => "required|array",
            "total_retail.*"      => "required|numeric",
        ]);

        $itemGroup->update([
            "group_name"          => $request->group_name,
            "sequence_number"     => $request->sequence_number,
            "bill_title"          => $request->bill_title,
            "gst"                 => $request->gst,
            "case_type_id"        => $request->case_type_id,
            "retail_wp_available" => $request->retail_wp_available,
        ]);

        ItemGroupPrice::where('item_group_id', $itemGroup->id)->delete();
        ItemGroupDetail::where('item_group_id', $itemGroup->id)->delete();
        ItemGroupPrintExtra::where('item_group_id', $itemGroup->id)->delete();

        foreach ($validated['min_dealer'] as $index => $minDealer) {
            ItemGroupDetail::create([
                'item_group_id' => $itemGroup->id,
                'print_type_id' => $request->print_type_id[$index],
                'min_dealer'    => $minDealer,
                'total_dealer'  => $validated['total_dealer'][$index],
                'min_retail'    => $validated['min_retail'][$index],
                'total_retail'  => $validated['total_retail'][$index],
                "created_by"    => auth()->id()
            ]);
        }

        foreach ($validated['extra_price'] as $index => $extraPrice) {
            ItemGroupPrice::create([
                'item_group_id' => $itemGroup->id,
                'print_type_id' => $request->print_type_id[$index],
                'extra_price'   => $extraPrice,
                'usd_extra_price' => $request->usd_extra_price[$index],
            ]);
        }

        foreach ($request->print_extra as $index => $printExtra) {
            ItemGroupPrintExtra::updateOrCreate([
                'item_group_id'  => $itemGroup->id,
                'print_extra_id' => $printExtra,
            ], [
                'amount'         => $request->amount[$index],
                'created_by'     => auth()->id()
            ]);
        }

        if ($request->ajax()) {
            return $this->withSuccess("Item Group created successfully");
        }
        return $this->withSuccess("Item Group created successfully")->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ItemGroup $itemGroup)
    {
        $itemGroup->details()->delete();
        $itemGroup->prices()->delete();
        $itemGroup->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Item Group delete successfully");
        }
        return $this->withSuccess("Item Group delete successfully")->back();
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
        $this->model(model: ItemGroup::class, with: ["createdBy"]);

        /* Add Filter here */
        $this->filter([
            // "status" => $request->status,
        ]);

        $editPermission   = $this->hasPermission("item_group-edit");
        $deletePermission = $this->hasPermission("item_group-delete");

        /* Add Formatting here */
        $this->formateArray(function ($row, $index) use ($editPermission, $deletePermission) {
            $delete = route("master.item-group.delete", ['item_group' => $row->id]);
            $action = "";
            $action = " <a class='btn edit-btn  btn-action bg-success text-white me-2'
                            data-id='{$row->id}'
                            data-permission='{$editPermission}'
                            data-bs-toggle='tooltip' data-bs-placement='top' data-bs-original-title='Edit' href='javascript:void(0);'>
                            <i class='far fa-edit' aria-hidden='true'></i>
                        </a>";

            if ($deletePermission) {
                $action .= " <a class='btn btn-action bg-danger text-white me-2 btn-delete' data-bs-toggle='tooltip'
                                data-bs-placement='top' data-bs-original-title='Delete'
                                href='{$delete}'>
                                <i class='fa-solid fa-trash'></i>
                            </a>";
            }

            $image = " <a class='btn image-btn  btn-action bg-success text-white me-2'
                            data-id='{$row->id}'
                            data-bs-toggle='tooltip' data-bs-placement='top' data-bs-original-title='Image' href='javascript:void(0);'>
                            <i class='far fa-image' aria-hidden='true'></i>
                        </a>";

            return [
                "id"                  => $row->id,
                "name"                => $row->group_name,
                "sequence"            => $row->sequence_number,
                "gst"                 => $row->gst,
                "retail_wp_available" => $row->retail_wp_available,
                "action"              => $action,
                "image"               => $image,
                "created_by"          => $row->createdBy?->displayName(),
                "created_at"          => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
                "updated_at"          => $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '',
            ];
        });
        return $this->getListAjax($searchableColumns);
    }

    public function modelForm(Request $request)
    {
        $caseTypes = CaseType::get();
        if ($request->id) {
            $itemGroup  = ItemGroup::where("id", $request->id)->first();
            $printTypes = PrintType::get();
            $printTypeExtras = PrintTypeExtra::get();
            foreach ($printTypes as $printType) {
                $price  = ItemGroupPrice::where("item_group_id", $itemGroup->id)->where("print_type_id", $printType->id)->first();
                $detail = ItemGroupDetail::where("item_group_id", $itemGroup->id)->where("print_type_id", $printType->id)->first();
                if ($price) {
                    $printType->extra_price = $price->extra_price;
                    $printType->usd_extra_price = $price->usd_extra_price;
                }
                if ($detail) {
                    $printType->min_dealer   = $detail->min_dealer;
                    $printType->total_dealer = $detail->total_dealer;
                    $printType->min_retail   = $detail->min_retail;
                    $printType->total_retail = $detail->total_retail;
                }
            }

            $itemGroupPrintExtras = ItemGroupPrintExtra::where("item_group_id", $itemGroup->id)->get();
            return view("Master::item_group.model", compact("printTypes", "itemGroup", "printTypeExtras", "itemGroupPrintExtras", "caseTypes"));
        } else {
            $printTypeExtras = PrintTypeExtra::get();
            $printTypes = PrintType::get();
            return view("Master::item_group.model", compact("printTypes", "printTypeExtras", "caseTypes"));
        }
        return false;
    }

    public function printTypeGet(Request $request)
    {
        if ($request->id) {
            $printTypes = PrintType::get();
            foreach ($printTypes as $printType) {
                $printType->print_type_id  = $printType->id;
                $printType->item_group_id  = $request->id;
                $printType->image          = PrintGroupImage::where("item_group_id", $request->id)->where("print_type_id", $printType->id)->first()->image ? env('APP_URL').'/storage/printGroupImage/'.PrintGroupImage::where("item_group_id", $request->id)->where("print_type_id", $printType->id)->first()->image : null;
            }
            return view("Master::item_group.model_image", compact("printTypes"));
        }
        return false;
    }

    public function printGroupImage(Request $request)
    {
        $request->validate([
            "print_type_id"     => "required|array",
            "print_type_id.*"   => "required|integer",
            "item_group_id"     => "required|array",
            "item_group_id.*"   => "required|integer",
        ]);

        foreach ($request->print_type_id as $index => $printType) {
            if (isset($request->image[$index]) && $request->image[$index] != null) {
                $image = FileUpload::upload($request->image[$index], "printGroupImage", "public");
            }else{
                $image = PrintGroupImage::where("item_group_id", $request->item_group_id[$index])->where("print_type_id", $printType)->first()->image ?? null;
            }
            PrintGroupImage::updateOrCreate([
                'item_group_id'  => $request->item_group_id[$index],
                'print_type_id' => $printType,
            ], [
                'image'         => $image,
                'created_by'     => auth()->id()
            ]);
        }
        if ($request->ajax()) {
            return $this->withSuccess("Print & Group Image created successfully");
        }
        return $this->withSuccess("Print & Group Image created successfully")->back();
    }
}
