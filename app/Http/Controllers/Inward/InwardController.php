<?php

namespace App\Http\Controllers\Inward;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Inward;
use App\Models\InwardDetail;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Traits\DataTable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InwardController extends Controller implements HasMiddleware
{

    use DataTable;
    public static function middleware(): array
    {
        return [
            new Middleware('permission:inward-create', only: ['create']),
            new Middleware('permission:inward-view', only: ['index', "getList"]),
            new Middleware('permission:inward-edit', only: ['edit', "update"]),
            new Middleware('permission:inward-delete', only: ['destroy']),
        ];
    }

    protected $inward;

    public function __construct(Inward $inward)
    {
        $this->inward = $inward;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $item = Item::all(['id', 'name']);
        return view('inward.index', compact('item'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::where('party_type_id', '!=', '4')->get();
        $items = Item::all(['id', 'name']);

        return view("inward.create", compact("customers", 'items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "detail_date" => "required",

            "item_id"     => "required|array|min:1",
            "item_id.*"   => "required",
            "qty"         => "required|array|min:1",
            "qty.*"       => "required|numeric",
        ]);
        DB::beginTransaction();

        try {
            $inward = Inward::create([
                "customer_id" => $request->customer_id ?? null,
                "date"        => $request->detail_date ?? Carbon::now()->toDateString(),
                "is_special"  => 0,
                "created_by"  => auth()->id()
            ]);

            $lastInsertedId = $inward->id;

            foreach ($request['item_id'] as $index => $item) {
                InwardDetail::create([
                    'inward_id'  => $lastInsertedId,
                    'item_id'    => $item,
                    'is_special' => $request->is_special[$index] == 1 ? 1 : 0,
                    'qty'        => $request->qty[$index],
                    'remark'     => $request->remark[$index],
                    'parcel'     => $request->parcel[$index],
                    'created_by' => auth()->id()
                ]);
            }

            DB::commit();

            if ($request->ajax()) {
                return $this->withSuccess("Inward created successfully");
            }
            return $this->withSuccess("Inward created successfully")->back();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inward $inward)
    {
        $items = Item::all(['id', 'name']);
        $inwardDetail = InwardDetail::with('item')->where('inward_id', $inward->id)->get();

        $quantityTotal = $inwardDetail->sum('qty');

        return view("inward.create", compact('inward', 'inwardDetail', 'items', 'quantityTotal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inward $inward)
    {
        $request->validate([
            "detail_date" => "required",

            "item_id"     => "required|array|min:1",
            "item_id.*"   => "required",
            "qty"         => "required|array|min:1",
            "qty.*"       => "required|numeric",
        ]);
        DB::beginTransaction();

        try {
            $inward->update([
                "customer_id" => $request->customer_id ?? null,
                "date"        => $request->detail_date ?? Carbon::now()->toDateString(),
                "is_special"  => 0,
                "created_by"  => auth()->id()
            ]);

            $deleteQ = InwardDetail::where('inward_id', $inward->id)->get();

            foreach ($deleteQ as $deleteR) {
                if (!in_array($deleteR['id'], $request->inward_detail_id)) {
                    InwardDetail::where(['id' => $deleteR['id']])->delete();
                }
            }

            foreach ($request['item_id'] as $index => $item) {
                $report = InwardDetail::where('inward_id', $inward->id)->where('id', $request->inward_detail_id[$index])->first();
                if ($report) {

                    InwardDetail::where('id', $request->inward_detail_id[$index])->update([
                        'inward_id'  => $inward->id,
                        'item_id'    => $item,
                        'qty'        => $request->qty[$index],
                        'is_special' => $request->is_special[$index] == 1 ? 1 : 0,
                        'parcel'     => $request->parcel[$index],
                        'remark'     => $request->remark[$index]
                    ]);
                } else {
                    InwardDetail::create([
                        'inward_id'  => $inward->id,
                        'item_id'    => $item,
                        'qty'        => $request->qty[$index],
                        'remark'     => $request->remark[$index],
                        'is_special' => $request->is_special[$index] == 1 ? 1 : 0,
                        'parcel'     => $request->parcel[$index],
                        "created_by" => auth()->id()
                    ]);
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return $this->withSuccess("Inward Updated successfully");
            }
            return $this->withSuccess("Inward Updated successfully")->back();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inward $inward)
    {
        $inward->inwardDetail()->delete();
        $inward->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Inward delete successfully");
        }
        return $this->withSuccess("Inward delete successfully")->back();
    }

    public function getItem(Request $request)
    {
        $item = Item::orWhere('name', 'like', '%' . $request->q . '%')->get();

        $items = [];
        $items[] = [
            'id' => '0',
            'text' => 'Select Item'
        ];
        foreach ($item as $value) {
            $items[] = [
                'id' => $value->id,
                'text' => $value->name . ' - ' . $value->packing
            ];
        }
        return response()->json($items);
    }

    public function getItemData(Request $request)
    {
        $item = Item::Where('id', $request->itemId)->first();
        return response()->json($item);
    }

    public function getList(Request $request)
    {
        $report = $request['group'];
        $url = $request['url'];
        $table = [];
        DB::statement('SET SESSION sql_mode =
        REPLACE(REPLACE(REPLACE(
        @@sql_mode,
        "ONLY_FULL_GROUP_BY,", ""),
        ",ONLY_FULL_GROUP_BY", ""),
        "ONLY_FULL_GROUP_BY", "")');
        switch ($report) {
            case 'inward':
                $data = $this->inward->getInwardGroupByInward($request);
                break;
            case 'voucher':
                $data = $this->inward->getInwardGroupByVoucher($request);
                break;
            default:
                break;
        }
        return view("inward.{$report}_ajax", compact('data'));
    }
}
