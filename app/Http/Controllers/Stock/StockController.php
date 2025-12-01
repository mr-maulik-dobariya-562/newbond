<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemGroup;
use App\Models\Stock;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:qty_stock-view', only: ['index', "get-list"]),
            new Middleware('permission:parcel_stock-view', only: ['indexParcel', 'getListParcel']),
        ];
    }

    public function __construct()
    {
        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
    }

    public function index()
    {
        $items  = Item::get();
        $groups = ItemGroup::get();
        $categories = ItemCategory::get();
        return view('stock.qtyStock.index', compact('items', 'groups', 'categories'));
    }

    public function getList(Request $request)
    {
        $item = $request->item;
        $fromDate = $request->fromDate;
        $toDate = $request->toDate;
        $group = $request->group;
        $status = $request->status;
        $is_special = $request->is_special;
        $category = $request->category;

        $ed = "";
        $iw = "";
        $od = "";
        if (!empty($fromDate) && !empty($toDate)) {
            $ed .= " AND E.date BETWEEN '" . $fromDate . "' AND '" . $toDate . "'";
            $iw .= " AND INW.date BETWEEN '" . $fromDate . "' AND '" . $toDate . "'";
            $od .= " AND OD.created_at BETWEEN '" . $fromDate . "' AND '" . $toDate . "'";
        } else if (!empty($fromDate)) {
            $ed .= " AND E.date >= '{$fromDate}'";
            $iw .= " AND INW.date >= '{$fromDate}'";
            $od .= " AND OD.created_at >= '{$fromDate}'";
        } else if (!empty($toDate)) {
            $ed .= " AND E.date <= '{$toDate}'";
            $iw .= " AND INW.date <= '{$toDate}'";
            $od .= " AND OD.created_at <= '{$toDate}'";
        }

        if ($is_special == 'YES' || $is_special == "NO") {
            $is_special = $request->is_special == 'YES' ? 1 : 0;
            $ed .= " AND ED.is_special =  '{$is_special}'";
            $iw .= " AND IW.is_special = '{$is_special}'";
            $od .= " AND OD.is_special = '{$is_special}'";
        }

        $query = DB::table('items as I')
            ->select(
                'I.name AS item_name',
                'I.id',
                'I.packing',
                DB::raw('(SELECT SUM(OD.dispatch_qty) FROM order_details as OD WHERE OD.item_id = I.id ' . $od . ') AS order_dispatch_qty'),
                DB::raw('(SELECT SUM(OD.qty) FROM order_details as OD WHERE OD.item_id = I.id ' . $od . ') AS order_qty'),
                DB::raw('(SELECT SUM(DISTINCT(IW.qty)) FROM inward_details as IW
                        LEFT JOIN inwards as INW ON INW.id = IW.inward_id
                        WHERE IW.item_id = I.id ' . $iw . ') AS inward_qty'),
                DB::raw('(SELECT SUM(DISTINCT(ED.qty)) FROM estimate_details as ED
                        LEFT JOIN estimates as E ON E.id = ED.estimate_id
                        WHERE ED.item_id = I.id ' . $ed . ') AS estimate_qty'),
            )->join('item_categories as C', 'I.categories_id', '=', 'C.id')
            ->where('I.branch_id', '=', session('branch_id'));

        if (!empty($group) && $group != 'ALL') {
            $query->where('C.item_group_id', '=', $group);
        }

        if (!empty($category) && $category != 'ALL') {
            $query->orWhere('C.id', '=', $category);
        }

        if (!empty($item) && $item != 'ALL') {
            $query->where('I.id', '=', $item);
        }

        if (!empty($status) && $status != 'ALL') {
            $query->where('I.active_type', '=', $status);
        }

        $data = $query->groupBy('I.id')->get()->toArray();

        $ed1 = "";
        $iw1 = "";
        $od1 = "";
        if (!empty($fromDate)) {
            $ed1 .= " AND E.date <= '{$fromDate}'";
            $iw1 .= " AND INW.date <= '{$fromDate}'";
            $od1 .= " AND OD.date <= '{$fromDate}'";
        }

        $query = DB::table('items as I')
            ->select(
                'I.name AS item_name',
                'I.id',
                'I.packing',
                DB::raw('(SELECT SUM(DISTINCT(IW.qty)) FROM inward_details as IW
                        LEFT JOIN inwards as INW ON INW.id = IW.inward_id
                        WHERE IW.item_id = I.id ' . $iw1 . ') AS inward_qty'),
                DB::raw('(SELECT SUM(DISTINCT(ED.qty)) FROM estimate_details as ED
                        LEFT JOIN estimates as E ON E.id = ED.estimate_id
                        WHERE ED.item_id = I.id ' . $ed1 . ') AS estimate_qty')
            )->join('item_categories as C', 'I.categories_id', '=', 'C.id')
            ->where('I.branch_id', '=', session('branch_id'));
        if (!empty($group) && $group != 'ALL') {
            // Filter by item group if provided
            if (!empty($group)) {
                $query->where('C.item_group_id', '=', $group);
            }
        }
        if (!empty($category) && $category != 'ALL') {
            // Filter by item group if provided
            if (!empty($category)) {
                $query->orWhere('C.id', '=', $category);
            }
        }
        if (!empty($item) && $item != 'ALL') {
            $query->where('I.id', '=', $item);
        }
        if (!empty($status) && $status != 'ALL') {
            $query->where('I.active_type', '=', $status);
        }

        $opening_stock = $query->groupBy('I.id')->get()->toArray();

        return view('stock.qtyStock.ajax_stock', compact('data', 'opening_stock'));
    }

    public function stockDetails(Item $item)
    {
        $query = DB::table('items as I')
            ->select(
                'od.id as detail_id',
                'od.order_id as reference_id',
                'od.item_id',
                'c.name as customerName',
                'o.date as date',
                'I.packing',
                'od.qty as qty',
                DB::raw('"order" as source')
            )
            ->join('estimate_details as od', 'I.id', '=', 'od.item_id')
            ->join('estimates as o', 'o.id', '=', 'od.estimate_id')
            ->join('customer as c', 'c.id', '=', 'o.customer_id')
            ->where('I.branch_id', '=', session('branch_id'))
            ->where('od.item_id', '=', $item->id)
            ->unionAll(
                DB::table('items as I')
                    ->select(
                        'iwd.id as detail_id',
                        'iwd.inward_id as reference_id',
                        'iwd.item_id',
                        DB::raw('"Inward" as customerName'),
                        'iw.date as date',
                        'I.packing',
                        'iwd.qty as qty',
                        DB::raw('"inward" as source')
                    )
                    ->join('inward_details as iwd', 'I.id', '=', 'iwd.item_id')
                    ->join('inwards as iw', 'iwd.inward_id', '=', 'iw.id')
                    ->where('I.branch_id', '=', session('branch_id'))
                    ->where('iwd.item_id', '=', $item->id)
            );

        $data = $query->get()->toArray();
        return view('stock.qtyStock.report', compact('data', 'item'));
    }

    public function indexParcel()
    {
        $items  = Item::get();
        $groups = ItemGroup::get();
        $categories = ItemCategory::get();
        return view('stock.parcel.index', compact('items', 'groups', 'categories'));
    }

    public function getListParcel(Request $request)
    {
        $item = $request->item;
        $fromDate = $request->fromDate;
        $toDate = $request->toDate;
        $group = $request->group;
        $status = $request->status;
        $is_special = $request->is_special;

        $ed = "";
        $iw = "";
        $od = "";
        if (!empty($fromDate) && !empty($toDate)) {
            $ed .= " AND E.date BETWEEN '" . $fromDate . "' AND '" . $toDate . "'";
            $iw .= " AND INW.date BETWEEN '" . $fromDate . "' AND '" . $toDate . "'";
            $od .= " AND OD.date BETWEEN '" . $fromDate . "' AND '" . $toDate . "'";
        } else if (!empty($fromDate)) {
            $ed .= " AND E.date >= '{$fromDate}'";
            $iw .= " AND INW.date >= '{$fromDate}'";
            $od .= " AND OD.date >= '{$fromDate}'";
        } else if (!empty($toDate)) {
            $ed .= " AND E.date <= '{$toDate}'";
            $iw .= " AND INW.date <= '{$toDate}'";
            $od .= " AND OD.date <= '{$toDate}'";
        }

        if ($is_special == 'YES' || $is_special == "NO") {
            $is_special = $request->is_special == 'YES' ? 1 : 0;
            $ed .= " AND ED.is_special =  '{$is_special}'";
            $iw .= " AND IW.is_special = '{$is_special}'";
            $od .= " AND OD.is_special = '{$is_special}'";
        }

        $query = DB::table('items as I')
            ->select(
                'I.name AS item_name',
                'I.id',
                'I.packing',
                DB::raw('(SELECT SUM(DISTINCT(OD.dispatch_qty)) FROM order_details as OD WHERE OD.item_id = I.id ' . $od . ') AS order_dispatch_qty'),
                DB::raw('(SELECT SUM(DISTINCT(OD.qty)) FROM order_details as OD WHERE OD.item_id = I.id ' . $od . ') AS order_qty'),
                DB::raw('(SELECT SUM(DISTINCT(IW.parcel)) FROM inward_details as IW
                        LEFT JOIN inwards as INW ON INW.id = IW.inward_id
                        WHERE IW.item_id = I.id ' . $iw . ') AS inward_qty'),
                DB::raw('(SELECT SUM(DISTINCT(ED.parcel)) FROM estimate_details as ED
                        LEFT JOIN estimates as E ON E.id = ED.estimate_id
                        WHERE ED.item_id = I.id ' . $ed . ') AS estimate_qty')
            )->join('item_categories as C', 'I.categories_id', '=', 'C.id')
            ->where('I.branch_id', '=', session('branch_id'));
        if (!empty($group) && $group != 'ALL') {
            if (!empty($group)) {
                $query->where('C.item_group_id', '=', $group);
            }
        }
        if (!empty($category) && $category != 'ALL') {
            if (!empty($category)) {
                $query->orWhere('C.id', '=', $category);
            }
        }
        if (!empty($item) && $item != 'ALL') {
            $query->where('I.id', '=', $item);
        }
        if (!empty($status) && $status != 'ALL') {
            $query->where('I.active_type', '=', $status);
        }

        $data = $query->groupBy('I.id')->get()->toArray();

        $ed1 = "";
        $iw1 = "";
        $od1 = "";
        if (!empty($fromDate)) {
            $ed1 .= " AND E.date <= '{$fromDate}'";
            $iw1 .= " AND INW.date <= '{$fromDate}'";
            $od1 .= " AND OD.date <= '{$fromDate}'";
        }

        $query = DB::table('items as I')
            ->select(
                'I.name AS item_name',
                'I.id',
                'I.packing',
                DB::raw('(SELECT SUM(DISTINCT(IW.parcel)) FROM inward_details as IW
                        LEFT JOIN inwards as INW ON INW.id = IW.inward_id
                        WHERE IW.item_id = I.id ' . $iw1 . ') AS inward_qty'),
                DB::raw('(SELECT SUM(DISTINCT(ED.parcel)) FROM estimate_details as ED
                        LEFT JOIN estimates as E ON E.id = ED.estimate_id
                        WHERE ED.item_id = I.id ' . $ed1 . ') AS estimate_qty')
            )->join('item_categories as C', 'I.categories_id', '=', 'C.id')
            ->where('I.branch_id', '=', session('branch_id'));
        if (!empty($group) && $group != 'ALL') {
            // Filter by item group if provided
            if (!empty($group)) {
                $query->where('C.item_group_id', '=', $group);
            }
        }
        if (!empty($category) && $category != 'ALL') {
            // Filter by item group if provided
            if (!empty($category)) {
                $query->orWhere('C.id', '=', $category);
            }
        }
        if (!empty($item) && $item != 'ALL') {
            $query->where('I.id', '=', $item);
        }
        if (!empty($status) && $status != 'ALL') {
            $query->where('I.active_type', '=', $status);
        }

        $opening_stock = $query->groupBy('I.id')->get()->toArray();

        return view('stock.parcel.ajax_stock', compact('data', 'opening_stock'));
    }

    public function stockDetailsParcel(Item $item)
    {
        $query = DB::table('items as I')
            ->select(
                'od.id as detail_id',
                'od.order_id as reference_id',
                'od.item_id',
                'c.name as customerName',
                'o.date as date',
                'I.packing',
                'od.parcel as qty',
                DB::raw('"order" as source')
            )
            ->join('estimate_details as od', 'I.id', '=', 'od.item_id')
            ->join('estimates as o', 'o.id', '=', 'od.estimate_id')
            ->join('customer as c', 'c.id', '=', 'o.customer_id')
            ->where('I.branch_id', '=', session('branch_id'))
            ->where('od.item_id', '=', $item->id)
            ->unionAll(
                DB::table('items as I')
                    ->select(
                        'iwd.id as detail_id',
                        'iwd.inward_id as reference_id',
                        'iwd.item_id',
                        DB::raw('"Inward" as customerName'),
                        'iw.date as date',
                        'I.packing',
                        'iwd.parcel as qty',
                        DB::raw('"inward" as source')
                    )
                    ->join('inward_details as iwd', 'I.id', '=', 'iwd.item_id')
                    ->join('inwards as iw', 'iwd.inward_id', '=', 'iw.id')
                    ->where('I.branch_id', '=', session('branch_id'))
                    ->where('iwd.item_id', '=', $item->id)
            );

        $data = $query->get()->toArray();
        return view('stock.parcel.report', compact('item', 'data'));
    }
}
