<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Order extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'address',
        'company_name',
        'discription',
        'po_no',
        'order_code',
        'date',
        'payment_date',
        'payment_amount',
        'comments',
        'is_verified',
        'total_amount',
        'discount',
        'redeem_coin',
        'last_closing',
        'net_amount',
        'cash_back_coin',
        'block_find',
        'print_type_id',
        'print_type_extra_id',
        'delivery_date',
        'discount_amount',
        'is_special',
        'order_type',
        'created_by',
        'updated_by',
        "branch_id",
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function orderDetail()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    public function printTypeExtra()
    {
        return $this->belongsTo(PrintTypeExtra::class, 'print_type_extra_id');
    }

    public function getSalesGroupByItem($filterParams)
    {
        // Create the date variables
        $fromDate = $filterParams['from_date'] ?? null;
        $toDate = $filterParams['to_date'] ?? null;
        $customer = $filterParams['customer'] ?? null;
        $item = $filterParams['item'] ?? null;
        $print = $filterParams['print_id'] ?? null;
        $block = $filterParams['block_find'] ?? null;
        $detail = $filterParams['printing_detail'] ?? null;
        $status = $filterParams['status'] ?? null;
        $partyType = $filterParams['party_type'] ?? null;
        $partyGroup = $filterParams['party_group'] ?? null;
        $parentUser = $filterParams['parentUser'] ?? null;
        $search = $filterParams['search'] ?? null;

        // Generate a query to get the sales group by item
        $query = DB::table('order_details as od')
            ->select(
                'sl.name AS item',
                'od.item_id',
                'od.id',
                DB::raw("DATE_FORMAT(od.date, '%d-%m-%Y') as date"),
                DB::raw('SUM(DISTINCT(od.rate)) AS rate'),
                DB::raw('SUM(DISTINCT(od.discount)) AS discount'),
                DB::raw('SUM(DISTINCT(od.amount)) AS amount'),
                DB::raw('SUM(DISTINCT(od.qty)) AS qty'),
                DB::raw('SUM(DISTINCT(od.cancel_qty)) AS cancel_qty'),
                DB::raw('MAX(od.remark) as remark'),
                DB::raw('MAX(od.created_at) as createdAt'),
                DB::raw('MAX(od.updated_at) as updatedAt')
            )
            ->leftJoin('orders as ol', 'ol.id', '=', 'od.order_id')
            ->leftJoin('customer as c', 'c.id', '=', 'ol.customer_id')
            ->leftJoin('items as sl', 'sl.id', '=', 'od.item_id')
            ->where('od.branch_id', '=', session('branch_id'));

        // Add conditions based on the filter params
        if (!empty($customer)) {
            $query->whereIn('ol.customer_id', $customer);
        }
        if (!empty($parentUser)) {
            $query->where('c.parent_id', '=', $parentUser);
        }
        $query->where('od.branch_id', '=', session('branch_id'));
        if (!empty($partyType)) {
            $query->where('c.party_type_id', '=', $partyType);
        }
        if (!empty($partyGroup)) {
            $query->whereIn('c.party_group_id', $partyGroup);
        }
        if (!empty($item)) {
            $query->whereIn('od.item_id', $item);
        }
        if (!empty($block)) {
            $query->where('ol.block_find', '=', $block);
        }
        if (!empty($print) && $print !== "0") {
            $query->whereIn('od.print_type_id', $print);
        }
        if (!empty($fromDate)) {
            $query->whereDate('ol.date', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('ol.date', '<=', $toDate);
        }
        if (!empty($detail)) {
            $query->where('od.narration', 'LIKE', "%{$detail}%");
        }
        if (!empty($search)) {
            $query->where('sl.name', 'LIKE', "%{$search}%");
        }
        if (!empty($status) && $status !== "all") {
            if ($status == "pending") {
                $query->havingRaw('SUM(od.qty) != SUM(od.dispatch_qty) + SUM(od.cancel_qty)');
            }
            if ($status == "dispatched") {
                $query->havingRaw('SUM(od.qty) = SUM(od.dispatch_qty)');
            }
            if ($status == "cancelled") {
                $query->havingRaw('SUM(od.qty) > 0');
            }
        }

        $query->groupBy('od.item_id')->orderByDesc('od.id');

        // Execute the query and return the result
        return $query->get()->toArray();
    }

    public function getSalesGroupByBill($filterParams)
    {
        $fromDate = $filterParams['from_date'] ?? null;
        $toDate = $filterParams['to_date'] ?? null;
        $customer = $filterParams['customer'] ?? null;
        $item = $filterParams['item'] ?? null;
        $print = $filterParams['print_id'] ?? null;
        $block = $filterParams['block_find'] ?? null;
        $detail = $filterParams['printing_detail'] ?? null;
        $status = $filterParams['status'] ?? null;
        $partyType = $filterParams['party_type'] ?? null;
        $partyGroup = $filterParams['party_group'] ?? null;
        $parentUser = $filterParams['parentUser'] ?? null;
        $search = $filterParams['search'] ?? null;

        // Generate a query to get the sales group by bill
        $query = OrderDetail::select(
            'orders.id as id',
            'orders.order_type',
            'orders.po_no',
            'orders.block_find',
            'customer.name as customer',
            'users.name as created_by_name',
            'U.name as updated_by_name',
            'cities.name as city',
            'orders.order_code',
            'party_types.name as party_type',
            'party_types.color',
            'orders.discription',
            DB::raw("DATE_FORMAT(orders.date, '%d-%m-%Y') as date"),
            DB::raw('SUM(order_details.rate) AS rate'),
            DB::raw('SUM(order_details.discount) AS discount'),
            DB::raw('SUM(order_details.amount) AS amount'),
            DB::raw('SUM(order_details.cancel_qty) AS cancel_qty'),
            DB::raw('SUM(order_details.qty) AS qty'),
            DB::raw('SUM(order_details.qty - order_details.dispatch_qty) AS pending_qty'),
            DB::raw('SUM(order_details.dispatch_qty) AS dispatch_qty'),
            DB::raw('MAX(order_details.created_at) as createdAt')
        )
            ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
            ->leftJoin('customer', 'customer.id', '=', 'orders.customer_id')
            ->leftJoin('users', 'orders.created_by', '=', 'users.id')
            ->leftJoin('users as U', 'orders.updated_by', '=', 'U.id')
            ->leftJoin('party_types', 'party_types.id', '=', 'customer.party_type_id')
            ->leftJoin('cities', 'cities.id', '=', 'customer.city_id');

        // Add conditions based on the filter params
        if (!empty($customer) && $customer !== "0") {
            $query->whereIn('orders.customer_id', $customer);
        }
        if (!empty($parentUser)) {
            $query->where('customer.parent_id', '=', $parentUser);
        }
        if (!empty($partyType)) {
            $query->where('customer.party_type_id', '=', $partyType);
        }
        if (!empty($partyGroup)) {
            $query->whereIn('customer.party_group_id', $partyGroup);
        }
        if (!empty($item) && $item !== "0") {
            $query->whereIn('order_details.item_id', $item);
        }
        if (!empty($block)) {
            $query->where('orders.block_find', '=', $block);
        }
        if (!empty($print) && $print !== "0") {
            $query->whereIn('order_details.print_type_id', $print);
        }
        if (!empty($fromDate)) {
            $query->whereDate('orders.date', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('orders.date', '<=', $toDate);
        }
        if (!empty($detail)) {
            $query->where('order_details.narration', 'LIKE', "%{$detail}%");
        }
        if (!empty($search)) {
            $query->where('order_details.item_name', 'LIKE', "%{$search}%");
        }

        if (!empty($status) && $status !== "all") {
            if ($status == "pending") {
                $query->havingRaw('SUM(order_details.qty) != SUM(order_details.dispatch_qty) + SUM(order_details.cancel_qty)');
            }
            if ($status == "dispatched") {
                $query->havingRaw('SUM(order_details.qty) = SUM(order_details.dispatch_qty)');
            }
            if ($status == "cancelled") {
                $query->havingRaw('SUM(order_details.qty) > 0');
            }
        }

        // Group by necessary fields and order by order id descending
        $query->groupBy(
            'orders.id',
            // 'orders.po_no',
            'customer.name',
            'cities.name',
            DB::raw("DATE_FORMAT(orders.date, '%d-%m-%Y')")
        )
            ->orderByDesc('orders.id');

        // Execute the query and return the result
        return $query->get()->toArray();
    }

    public function getSalesGroupByCustomer($filterParams)
    {
        // Create the date variables
        $fromDate = $filterParams['from_date'] ?? null;
        $toDate = $filterParams['to_date'] ?? null;
        $customer = $filterParams['customer'] ?? null;
        $item = $filterParams['item'] ?? null;
        $print = $filterParams['print_id'] ?? null;
        $block = $filterParams['block_find'] ?? null;
        $detail = $filterParams['printing_detail'] ?? null;
        $status = $filterParams['status'] ?? null;
        $partyType = $filterParams['party_type'] ?? null;
        $partyGroup = $filterParams['party_group'] ?? null;
        $parentUser = $filterParams['parentUser'] ?? null;
        $search = $filterParams['search'] ?? null;

        // Generate a query to get the sales group by customer
        $query = DB::table('order_details as pd')
            ->select(
                'c.name as customer',
                'pt.color',
                DB::raw('SUM(DISTINCT(pd.rate)) AS rate'),
                DB::raw('SUM(DISTINCT(pd.discount)) AS discount'),
                DB::raw('SUM(DISTINCT(pd.amount)) AS amount'),
                DB::raw('SUM(DISTINCT(pd.qty)) AS qty'),
                DB::raw('SUM(DISTINCT(pd.cancel_qty)) AS cancel_qty'),
                DB::raw('MAX(pd.remark) as remark'),
                DB::raw('MAX(pd.created_at) as createdAt'),
                DB::raw('MAX(pd.updated_at) as updatedAt')
            )
            ->leftJoin('orders as pl', 'pl.id', '=', 'pd.order_id')
            ->leftJoin('customer as c', 'c.id', '=', 'pl.customer_id')
            ->leftJoin('party_types as pt', 'pt.id', '=', 'c.party_type_id')
            ->where('pd.branch_id', '=', session('branch_id'));

        // Add conditions based on the filter params
        if (!empty($customer) && $customer !== "0") {
            $query->where('pl.customer_id', '=', $customer);
        }
        if (!empty($parentUser)) {
            $query->where('c.parent_id', '=', $parentUser);
        }
        if (!empty($partyType)) {
            $query->where('c.party_type_id', '=', $partyType);
        }
        if (!empty($partyGroup)) {
            $query->where('c.party_group_id', '=', $partyGroup);
        }
        if (!empty($item) && $item !== "0") {
            $query->where('pd.item_id', '=', $item);
        }
        if (!empty($block)) {
            $query->where('pl.block_find', '=', $block);
        }
        if (!empty($print) && $print !== "0") {
            $query->where('pd.print_type_id', '=', $print);
        }
        if (!empty($fromDate)) {
            $query->whereDate('pl.date', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('pl.date', '<=', $toDate);
        }
        if (!empty($detail)) {
            $query->where('pd.narration', 'LIKE', "%{$detail}%");
        }
        if (!empty($search)) {
            $query->where('pd.item_name', 'LIKE', "%{$search}%");
        }

        if (!empty($status) && $status !== "all") {
            if ($status == "pending") {
                $query->havingRaw('SUM(pd.qty) != SUM(pd.dispatch_qty) + SUM(pd.cancel_qty)');
            }
            if ($status == "dispatched") {
                $query->havingRaw('SUM(pd.qty) = SUM(pd.dispatch_qty)');
            }
            if ($status == "cancelled") {
                $query->havingRaw('SUM(pd.qty) > 0');
            }
        }

        $query->groupBy('pl.customer_id', 'c.name')
            ->orderByDesc('pl.id');

        // Execute the query and return the result
        return $query->get()->toArray();
    }

    public function getSalesGroupByVoucher($filterParams)
    {
        // Create the date variables
        $fromDate = $filterParams['from_date'] ?? null;
        $toDate = $filterParams['to_date'] ?? null;
        $customer = $filterParams['customer'] ?? null;
        $item = $filterParams['item'] ?? null;
        $print = $filterParams['print_id'] ?? null;
        $block = $filterParams['block_find'] ?? null;
        $detail = $filterParams['printing_detail'] ?? null;
        $status = $filterParams['status'] ?? null;
        $partyType = $filterParams['party_type'] ?? null;
        $partyGroup = $filterParams['party_group'] ?? null;
        $parentUser = $filterParams['parentUser'] ?? null;
        $search = $filterParams['search'] ?? null;

        // Generate a query to get the sales group by sale-detail
        $query = DB::table('order_details as pd')
            ->select(
                'I.name as item_name',
                'CUS.name as customer',
                'users.name as created_by_name',
                'U.name as updated_by_name',
                'pl.order_type',
                'CI.name as city',
                'pt.name as print_type',
                'party_types.color',
                't.name as transports',
                'pd.block',
                'pd.remark',
                'pd.narration',
                'pd.dispatch_qty',
                DB::raw('SUM(pd.qty - pd.dispatch_qty) AS pending_qty'),
                DB::raw("DATE_FORMAT(pl.date, '%d-%m-%Y') as date"),
                DB::raw('SUM(DISTINCT(pd.cancel_qty)) AS cancel_qty'),
                DB::raw('SUM(DISTINCT(pd.rate)) AS rate'),
                DB::raw('SUM(pd.discount) AS discount'),
                DB::raw('SUM(DISTINCT(pd.amount)) AS amount'),
                DB::raw('SUM(DISTINCT(pd.qty)) AS qty'),
                DB::raw('MAX(pd.remark) as remark'),
                DB::raw('MAX(pd.created_at) as createdAt'),
                DB::raw('MAX(pd.updated_at) as updatedAt')
            )
            ->leftJoin('orders as pl', 'pl.id', '=', 'pd.order_id')
            ->leftJoin('items as I', 'I.id', '=', 'pd.item_id')
            ->leftJoin('print_type as pt', 'pt.id', '=', 'pd.print_type_id')
            ->leftJoin('transports as t', 't.id', '=', 'pd.transport_id')
            ->leftJoin('customer as CUS', 'CUS.id', '=', 'pl.customer_id')
            ->leftJoin('users', 'pl.created_by', '=', 'users.id')
            ->leftJoin('users as U', 'pl.updated_by', '=', 'U.id')
            ->leftJoin('party_types', 'party_types.id', '=', 'CUS.party_type_id')
            ->leftJoin('cities as CI', 'CI.id', '=', 'CUS.city_id')
            ->where('pd.branch_id', '=', session('branch_id'));

        // Add conditions based on the filter params
        if (!empty($customer) && $customer !== "0") {
            $query->where('pl.customer_id', '=', $customer);
        }
        if (!empty($parentUser)) {
            $query->where('CUS.parent_id', '=', $parentUser);
        }
        if (!empty($partyType)) {
            $query->where('CUS.party_type_id', '=', $partyType);
        }
        if (!empty($partyGroup)) {
            $query->where('CUS.party_group_id', '=', $partyGroup);
        }
        if (!empty($item) && $item !== "0") {
            $query->where('pd.item_id', '=', $item);
        }
        if (!empty($block)) {
            $query->where('pl.block_find', '=', $block);
        }
        if (!empty($print) && $print !== "0") {
            $query->where('pd.print_type_id', '=', $print);
        }
        if (!empty($fromDate)) {
            $query->whereDate('pl.date', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('pl.date', '<=', $toDate);
        }
        if (!empty($detail)) {
            $query->where('pd.narration', 'LIKE', "%{$detail}%");
        }
        if (!empty($search)) {
            $query->where('pd.item_name', 'LIKE', "%{$search}%");
        }

        if (!empty($status) && $status !== "all") {
            if ($status == "pending") {
                $query->havingRaw('SUM(pd.qty) != SUM(pd.dispatch_qty) + SUM(pd.cancel_qty)');
            }
            if ($status == "dispatched") {
                $query->havingRaw('SUM(pd.qty) = SUM(pd.dispatch_qty)');
            }
            if ($status == "cancelled") {
                $query->havingRaw('SUM(pd.qty) > 0');
            }
        }

        // Group by purchase_detail id and order by purchase id descending
        $query->groupBy('pd.id', 'I.name', 'CUS.name', 'CI.name', 'pl.date')
            ->orderByDesc('pl.id');

        // Execute the query and return the result
        return $query->get()->toArray();
    }

    public function getSalesGroupByCreated($filterParams)
    {
        // Create the date variables
        $fromDate = $filterParams['from_date'] ?? null;
        $toDate = $filterParams['to_date'] ?? null;
        $customer = $filterParams['customer'] ?? null;
        $item = $filterParams['item'] ?? null;
        $print = $filterParams['print_id'] ?? null;
        $block = $filterParams['block_find'] ?? null;
        $detail = $filterParams['printing_detail'] ?? null;
        $status = $filterParams['status'] ?? null;
        $partyType = $filterParams['party_type'] ?? null;
        $partyGroup = $filterParams['party_group'] ?? null;
        $parentUser = $filterParams['parentUser'] ?? null;
        $search = $filterParams['search'] ?? null;

        // Generate a query to get the sales group by customer
        $query = DB::table('order_details as pd')
            ->select(
                'c.name as customer',
                'pt.color',
                DB::raw('SUM(DISTINCT(pd.rate)) AS rate'),
                DB::raw('SUM(DISTINCT(pd.cancel_qty)) AS cancel_qty'),
                DB::raw('SUM(pd.discount) AS discount'),
                DB::raw('SUM(DISTINCT(pd.amount)) AS amount'),
                DB::raw('SUM(DISTINCT(pd.qty)) AS qty'),
                DB::raw('MAX(pd.remark) as remark'),
                DB::raw('MAX(pd.created_at) as createdAt'),
                DB::raw('MAX(pd.updated_at) as updatedAt')
            )
            ->leftJoin('orders as pl', 'pl.id', '=', 'pd.order_id')
            ->leftJoin('customer as c', 'c.id', '=', 'pl.customer_id')
            ->leftJoin('party_types as pt', 'pt.id', '=', 'c.party_type_id')
            ->where('pd.branch_id', '=', session('branch_id'));

        // Add conditions based on the filter params
        if (!empty($customer) && $customer !== "0") {
            $query->whereIn('pl.customer_id', $customer);
        }
        if (!empty($parentUser)) {
            $query->where('c.parent_id', '=', $parentUser);
        }
        if (!empty($partyType)) {
            $query->where('c.party_type_id', '=', $partyType);
        }
        if (!empty($partyGroup)) {
            $query->whereIn('c.party_group_id', $partyGroup);
        }
        if (!empty($item) && $item !== "0") {
            $query->whereIn('pd.item_id', $item);
        }
        if (!empty($block)) {
            $query->where('pl.block_find', '=', $block);
        }
        if (!empty($print) && $print !== "0") {
            $query->whereIn('pd.print_type_id', $print);
        }
        if (!empty($fromDate)) {
            $query->whereDate('pl.date', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('pl.date', '<=', $toDate);
        }
        if (!empty($detail)) {
            $query->where('pd.narration', 'LIKE', "%{$detail}%");
        }
        if (!empty($search)) {
            $query->where('pd.item_name', 'LIKE', "%{$search}%");
        }

        if (!empty($status) && $status !== "all") {
            if ($status == "pending") {
                $query->havingRaw('SUM(pd.qty) != SUM(pd.dispatch_qty) + SUM(pd.cancel_qty)');
            }
            if ($status == "dispatched") {
                $query->havingRaw('SUM(pd.qty) = SUM(pd.dispatch_qty)');
            }
            if ($status == "cancelled") {
                $query->havingRaw('SUM(pd.qty) > 0');
            }
        }

        $query->groupBy('pl.created_by', 'c.name')
            ->orderByDesc('pl.id');

        // Execute the query and return the result
        return $query->get()->toArray();
    }

    public function getSalesGroupByPrintType($filterParams)
    {
        // Create the date variables
        $fromDate = $filterParams['from_date'] ?? null;
        $toDate = $filterParams['to_date'] ?? null;
        $customer = $filterParams['customer'] ?? null;
        $item = $filterParams['item'] ?? null;
        $print = $filterParams['print_id'] ?? null;
        $block = $filterParams['block_find'] ?? null;
        $detail = $filterParams['printing_detail'] ?? null;
        $status = $filterParams['status'] ?? null;
        $partyType = $filterParams['party_type'] ?? null;
        $partyGroup = $filterParams['party_group'] ?? null;
        $parentUser = $filterParams['parentUser'] ?? null;
        $search = $filterParams['search'] ?? null;

        // Generate a query to get the sales group by customer
        $query = DB::table('order_details as pd')
            ->select(
                'pt.name as print_type',
                'pd.print_type_id',
                DB::raw('SUM(DISTINCT(pd.rate)) AS rate'),
                DB::raw('SUM(DISTINCT(pd.cancel_qty)) AS cancel_qty'),
                DB::raw('SUM(pd.discount) AS discount'),
                DB::raw('SUM(DISTINCT(pd.amount)) AS amount'),
                DB::raw('SUM(DISTINCT(pd.qty)) AS qty'),
                DB::raw('MAX(pd.remark) as remark'),
                DB::raw('MAX(pd.created_at) as createdAt'),
                DB::raw('MAX(pd.updated_at) as updatedAt')
            )
            ->leftJoin('orders as pl', 'pl.id', '=', 'pd.order_id')
            ->leftJoin('customer as CUS', 'CUS.id', '=', 'pl.customer_id')
            ->leftJoin('print_type as pt', 'pt.id', '=', 'pd.print_type_id')
            ->where('pd.branch_id', '=', session('branch_id'));

        // Add conditions based on the filter params
        if (!empty($customer) && $customer !== "0") {
            $query->whereIn('pl.customer_id', $customer);
        }
        if (!empty($parentUser)) {
            $query->where('CUS.parent_id', '=', $parentUser);
        }
        if (!empty($partyType)) {
            $query->where('CUS.party_type_id', '=', $partyType);
        }
        if (!empty($partyGroup)) {
            $query->whereIn('CUS.party_group_id', $partyGroup);
        }
        if (!empty($item) && $item !== "0") {
            $query->whereIn('pd.item_id', $item);
        }
        if (!empty($block)) {
            $query->where('pl.block_find', '=', $block);
        }
        if (!empty($print) && $print !== "0") {
            $query->whereIn('pd.print_type_id', $print);
        }
        if (!empty($fromDate)) {
            $query->whereDate('pl.date', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('pl.date', '<=', $toDate);
        }
        if (!empty($detail)) {
            $query->where('pd.narration', 'LIKE', "%{$detail}%");
        }
        if (!empty($search)) {
            $query->where('pd.item_name', 'LIKE', "%{$search}%");
        }

        if (!empty($status) && $status !== "all") {
            if ($status == "pending") {
                $query->havingRaw('SUM(pd.qty) != SUM(pd.dispatch_qty) + SUM(pd.cancel_qty)');
            }
            if ($status == "dispatched") {
                $query->havingRaw('SUM(pd.qty) = SUM(pd.dispatch_qty)');
            }
            if ($status == "cancelled") {
                $query->havingRaw('SUM(pd.qty) > 0');
            }
        }

        $query->groupBy('pd.print_type_id')
            ->orderByDesc('pl.id');

        return $query->get()->toArray();
    }

    public function getSalesGroupByPartyGroup($filterParams)
    {
        // Create the date variables
        $fromDate = $filterParams['from_date'] ?? null;
        $toDate = $filterParams['to_date'] ?? null;
        $customer = $filterParams['customer'] ?? null;
        $item = $filterParams['item'] ?? null;
        $print = $filterParams['print_id'] ?? null;
        $block = $filterParams['block_find'] ?? null;
        $detail = $filterParams['printing_detail'] ?? null;
        $status = $filterParams['status'] ?? null;
        $partyType = $filterParams['party_type'] ?? null;
        $partyGroup = $filterParams['party_group'] ?? null;
        $parentUser = $filterParams['parentUser'] ?? null;
        $search = $filterParams['search'] ?? null;

        // Generate a query to get the sales group by customer
        $query = DB::table('order_details as pd')
            ->select(
                'customer.party_group_id',
                'pg.name as party_group_name',
                DB::raw('SUM(DISTINCT(pd.rate)) AS rate'),
                DB::raw('SUM(DISTINCT(pd.cancel_qty)) AS cancel_qty'),
                DB::raw('SUM(pd.discount) AS discount'),
                DB::raw('SUM(DISTINCT(pd.amount)) AS amount'),
                DB::raw('SUM(DISTINCT(pd.qty)) AS qty'),
                DB::raw('MAX(pd.remark) as remark'),
                DB::raw('MAX(pd.created_at) as createdAt'),
                DB::raw('MAX(pd.updated_at) as updatedAt')
            )
            ->leftJoin('orders as pl', 'pl.id', '=', 'pd.order_id')
            ->leftJoin('customer', 'pl.customer_id', '=', 'customer.id')
            ->leftJoin('party_groups as pg', 'customer.party_group_id', '=', 'pg.id')
            ->where('pd.branch_id', '=', session('branch_id'));

        // Add conditions based on the filter params
        if (!empty($customer) && $customer !== "0") {
            $query->whereIn('pl.customer_id', $customer);
        }
        if (!empty($parentUser)) {
            $query->where('customer.parent_id', '=', $parentUser);
        }
        if (!empty($partyType)) {
            $query->where('customer.party_type_id', '=', $partyType);
        }
        if (!empty($partyGroup)) {
            $query->whereIn('customer.party_group_id', $partyGroup);
        }
        if (!empty($item) && $item !== "0") {
            $query->whereIn('pd.item_id', $item);
        }
        if (!empty($block)) {
            $query->where('pl.block_find', '=', $block);
        }
        if (!empty($print) && $print !== "0") {
            $query->whereIn('pd.print_type_id', $print);
        }
        if (!empty($fromDate)) {
            $query->whereDate('pl.date', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('pl.date', '<=', $toDate);
        }
        if (!empty($detail)) {
            $query->where('pd.narration', 'LIKE', "%{$detail}%");
        }
        if (!empty($search)) {
            $query->where('pd.item_name', 'LIKE', "%{$search}%");
        }

        if (!empty($status) && $status !== "all") {
            if ($status == "pending") {
                $query->havingRaw('SUM(pd.qty) != SUM(pd.dispatch_qty) + SUM(pd.cancel_qty)');
            }
            if ($status == "dispatched") {
                $query->havingRaw('SUM(pd.qty) = SUM(pd.dispatch_qty)');
            }
            if ($status == "cancelled") {
                $query->havingRaw('SUM(pd.qty) > 0');
            }
        }

        $query->groupBy('customer.party_group_id')
            ->orderByDesc('pl.id');

        return $query->get()->toArray();
    }

    public function getSalesGroupByBillPrintGroup($filterParams)
    {
        // Create the date variables
        $fromDate = $filterParams['from_date'] ?? null;
        $toDate = $filterParams['to_date'] ?? null;
        $customer = $filterParams['customer'] ?? null;
        $item = $filterParams['item'] ?? null;
        $print = $filterParams['print_id'] ?? null;
        $block = $filterParams['block_find'] ?? null;
        $detail = $filterParams['printing_detail'] ?? null;
        $status = $filterParams['status'] ?? null;
        $partyType = $filterParams['party_type'] ?? null;
        $partyGroup = $filterParams['party_group'] ?? null;
        $parentUser = $filterParams['parentUser'] ?? null;
        $search = $filterParams['search'] ?? null;

        // Generate a query to get the sales group by customer
        $query = OrderDetail::select(
                'orders.id as id',
                'orders.po_no',
                'orders.status',
                'orders.order_type',
                'orders.block_find',
                'customer.name as customer',
                'users.name as created_by_name',
                'U.name as updated_by_name',
                'cities.name as city',
                'orders.order_code',
                'party_types.name as party_type',
                'party_types.color',
                'orders.discription',
                'pt.name as print_type',
                'order_details.print_type_id',
                DB::raw("DATE_FORMAT(orders.date, '%d-%m-%Y') as date"),
                DB::raw('SUM(order_details.rate) AS rate'),
                DB::raw('SUM(order_details.cancel_qty) AS cancel_qty'),
                DB::raw('SUM(order_details.discount) AS discount'),
                DB::raw('SUM(order_details.amount) AS amount'),
                DB::raw('SUM(order_details.qty) AS qty'),
                DB::raw('SUM(order_details.qty - order_details.dispatch_qty) AS pending_qty'),
                DB::raw('SUM(order_details.dispatch_qty) AS dispatch_qty'),
                DB::raw('MAX(order_details.created_at) as createdAt')
            )
            ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
            ->leftJoin('customer', 'customer.id', '=', 'orders.customer_id')
            ->leftJoin('users', 'orders.created_by', '=', 'users.id')
            ->leftJoin('users as U', 'orders.updated_by', '=', 'U.id')
            ->leftJoin('party_types', 'party_types.id', '=', 'customer.party_type_id')
            ->leftJoin('cities', 'cities.id', '=', 'customer.city_id')
            ->leftJoin('print_type as pt', 'pt.id', '=', 'order_details.print_type_id');

        // Add conditions based on the filter params
        if (!empty($customer) && $customer !== "0") {
            $query->whereIn('orders.customer_id', $customer);
        }
        if (!empty($parentUser)) {
            $query->where('customer.parent_id', '=', $parentUser);
        }
        if (!empty($partyType)) {
            $query->where('customer.party_type_id', '=', $partyType);
        }
        if (!empty($partyGroup)) {
            $query->whereIn('customer.party_group_id', $partyGroup);
        }
        if (!empty($item) && $item !== "0") {
            $query->whereIn('order_details.item_id', $item);
        }
        if (!empty($block)) {
            $query->where('order_details.block_find', '=', $block);
        }
        if (!empty($print) && $print !== "0") {
            $query->whereIn('order_details.print_type_id', $print);
        }
        if (!empty($fromDate)) {
            $query->whereDate('orders.date', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('orders.date', '<=', $toDate);
        }
        if (!empty($detail)) {
            $query->where('order_details.narration', 'LIKE', "%{$detail}%");
        }
        if (!empty($search)) {
            $query->where('order_details.item_name', 'LIKE', "%{$search}%");
        }

        if (!empty($status) && $status !== "all") {
            if ($status == "pending") {
                $query->havingRaw('SUM(order_details.qty) != SUM(order_details.dispatch_qty) + SUM(order_details.cancel_qty)');
            }
            if ($status == "dispatched") {
                $query->havingRaw('SUM(order_details.qty) = SUM(order_details.dispatch_qty)');
            }
            if ($status == "cancelled") {
                $query->havingRaw('SUM(order_details.cancel_qty) > 0');
            }
        }

        $query->groupBy('order_details.print_type_id', 'orders.id')
            ->orderByDesc('order_details.id');

        return $query->get()->toArray();
    }
}
