<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Quotation extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'address',
        'company_name',
        'discription',
        'po_no',
        'quotation_code',
        'date',
        'payment_date',
        'payment_amount',
        'comments',
        'is_verified',
        'total_amount',
        'discount',
        'redeem_coin',
        'net_amount',
        'cash_back_coin',
        'block_find',
        'print_type_id',
        'delivery_date',
        'discount_amount',
        'created_by',
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

    public function quotationDetail()
    {
        return $this->hasMany(QuotationDetail::class, 'quotation_id');
    }

    public function getSalesGroupByItem($filterParams)
    {
        // Create the date variables
        $fromDate = $filterParams['from_date'] ?? null;
        $toDate = $filterParams['to_date'] ?? null;
        $customer = $filterParams['customer'] ?? null;
        $item = $filterParams['item'] ?? null;
        $print = $filterParams['print_id'] ?? null;

        // Generate a query to get the sales group by item
        $query = DB::table('quotation_details as od')
            ->select(
                'sl.name AS item',
                'od.item_id',
                'od.id',
                DB::raw("DATE_FORMAT(od.date, '%d-%m-%Y') as date"),
                DB::raw('SUM(od.rate) AS rate'),
                DB::raw('SUM(od.discount) AS discount'),
                DB::raw('SUM(od.amount) AS amount'),
                DB::raw('SUM(od.qty) AS qty'),
                DB::raw('MAX(od.remark) as remark'),
                DB::raw('MAX(od.created_at) as createdAt')
            )
            ->leftJoin('quotations as ol', 'ol.id', '=', 'od.quotation_id')
            ->leftJoin('items as sl', 'sl.id', '=', 'od.item_id')
            ->where('od.branch_id', '=', session('branch_id'));

        // Add conditions based on the filter params
        if (!empty($customer)) {
            $query->where('ol.customer_id', '=', $customer);
        }
        if (!empty($item)) {
            $query->where('od.item_id', '=', $item);
        }
        if (!empty($print) && $print !== "0") {
            $query->where('od.print_type_id', '=', $print);
        }
        if (!empty($fromDate)) {
            $query->whereDate('ol.date', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('ol.date', '<=', $toDate);
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

        // Generate a query to get the sales group by bill
        $query = QuotationDetail::select(
            'quotations.id as id',
            'quotations.po_no',
            'quotations.block_find',
            'customer.name as customer',
            'cities.name as city',
            'quotations.quotation_code',
            DB::raw("DATE_FORMAT(quotations.date, '%d-%m-%Y') as date"),
            DB::raw('SUM(quotation_details.rate) AS rate'),
            DB::raw('SUM(quotation_details.discount) AS discount'),
            DB::raw('SUM(quotation_details.amount) AS amount'),
            DB::raw('SUM(quotation_details.qty) AS qty'),
            DB::raw('MAX(quotation_details.remark) as remark'),
            DB::raw('MAX(quotation_details.created_at) as createdAt')
        )
            ->leftJoin('quotations', 'quotations.id', '=', 'quotation_details.quotation_id')
            ->leftJoin('customer', 'customer.id', '=', 'quotations.customer_id')
            ->leftJoin('cities', 'cities.id', '=', 'customer.city_id');

        // Add conditions based on the filter params
        if (!empty($customer) && $customer !== "0") {
            $query->where('quotations.customer_id', $customer);
        }
        if (!empty($item) && $item !== "0") {
            $query->where('quotation_details.item_id', $item);
        }
        if (!empty($print) && $print !== "0") {
            $query->where('quotation_details.print_type_id', '=', $print);
        }
        if (!empty($fromDate)) {
            $query->whereDate('quotations.date', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('quotations.date', '<=', $toDate);
        }

        // Group by necessary fields and order by order id descending
        $query->groupBy(
            'quotations.id',
            'quotations.po_no',
            'customer.name',
            'cities.name',
            DB::raw("DATE_FORMAT(quotations.date, '%d-%m-%Y')")
        )
            ->orderByDesc('quotations.id');

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

        // Generate a query to get the sales group by customer
        $query = DB::table('quotation_details as pd')
            ->select(
                'c.name as customer',
                DB::raw('SUM(pd.rate) AS rate'),
                DB::raw('SUM(pd.discount) AS discount'),
                DB::raw('SUM(pd.amount) AS amount'),
                DB::raw('SUM(pd.qty) AS qty'),
                DB::raw('MAX(pd.remark) as remark'),
                DB::raw('MAX(pd.created_at) as createdAt')
            )
            ->leftJoin('quotations as pl', 'pl.id', '=', 'pd.quotation_id')
            ->leftJoin('customer as c', 'c.id', '=', 'pl.customer_id')
            ->where('pd.branch_id', '=', session('branch_id'));

        // Add conditions based on the filter params
        if (!empty($customer) && $customer !== "0") {
            $query->where('pl.customer_id', '=', $customer);
        }
        if (!empty($item) && $item !== "0") {
            $query->where('pd.item_id', '=', $item);
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

        // Generate a query to get the sales group by sale-detail
        $query = DB::table('quotation_details as pd')
            ->select(
                'I.name as item_name',
                'CUS.name as customer',
                'CI.name as city',
                'pt.name as print_type',
                't.name as transports',
                'pd.block',
                'pd.remark',
                'pd.narration',
                DB::raw("DATE_FORMAT(pl.date, '%d-%m-%Y') as date"),
                DB::raw('SUM(pd.rate) AS rate'),
                DB::raw('SUM(pd.discount) AS discount'),
                DB::raw('SUM(pd.amount) AS amount'),
                DB::raw('SUM(pd.qty) AS qty'),
                DB::raw('MAX(pd.remark) as remark'),
                DB::raw('MAX(pd.created_at) as createdAt')
            )
            ->leftJoin('quotations as pl', 'pl.id', '=', 'pd.quotation_id')
            ->leftJoin('items as I', 'I.id', '=', 'pd.item_id')
            ->leftJoin('print_type as pt', 'pt.id', '=', 'pd.print_type_id')
            ->leftJoin('transports as t', 't.id', '=', 'pd.transport_id')
            ->leftJoin('customer as CUS', 'CUS.id', '=', 'pl.customer_id')
            ->leftJoin('cities as CI', 'CI.id', '=', 'CUS.city_id')
            ->where('pd.branch_id', '=', session('branch_id'));

        // Add conditions based on the filter params
        if (!empty($customer) && $customer !== "0") {
            $query->where('pl.customer_id', '=', $customer);
        }
        if (!empty($item) && $item !== "0") {
            $query->where('pd.item_id', '=', $item);
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

        // Generate a query to get the sales group by customer
        $query = DB::table('quotation_details as pd')
            ->select(
                'c.name as customer',
                DB::raw('SUM(pd.rate) AS rate'),
                DB::raw('SUM(pd.discount) AS discount'),
                DB::raw('SUM(pd.amount) AS amount'),
                DB::raw('SUM(pd.qty) AS qty'),
                DB::raw('MAX(pd.remark) as remark'),
                DB::raw('MAX(pd.created_at) as createdAt')
            )
            ->leftJoin('quotations as pl', 'pl.id', '=', 'pd.quotation_id')
            ->leftJoin('customer as c', 'c.id', '=', 'pl.customer_id')
            ->where('pd.branch_id', '=', session('branch_id'));

        // Add conditions based on the filter params
        if (!empty($customer) && $customer !== "0") {
            $query->where('pl.customer_id', '=', $customer);
        }
        if (!empty($item) && $item !== "0") {
            $query->where('pd.item_id', '=', $item);
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

        // Generate a query to get the sales group by customer
        $query = DB::table('quotation_details as pd')
            ->select(
                'pt.name as print_type',
                'pd.print_type_id',
                DB::raw('SUM(pd.rate) AS rate'),
                DB::raw('SUM(pd.discount) AS discount'),
                DB::raw('SUM(pd.amount) AS amount'),
                DB::raw('SUM(pd.qty) AS qty'),
                DB::raw('MAX(pd.remark) as remark'),
                DB::raw('MAX(pd.created_at) as createdAt')
            )
            ->leftJoin('quotations as pl', 'pl.id', '=', 'pd.quotation_id')
            ->leftJoin('print_type as pt', 'pt.id', '=', 'pd.print_type_id')
            ->where('pd.branch_id', '=', session('branch_id'));

        // Add conditions based on the filter params
        if (!empty($customer) && $customer !== "0") {
            $query->where('pl.customer_id', '=', $customer);
        }
        if (!empty($item) && $item !== "0") {
            $query->where('pd.item_id', '=', $item);
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

        $query->groupBy('pd.print_type_id')
            ->orderByDesc('pl.id');

        return $query->get()->toArray();
    }
}
