<?php

namespace App\Models;

use App\Helpers\FileUpload;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Estimate extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $folder = "lrPhoto";
    protected $fillable = [
        'customer_id',
        'estimate_code',
        'address',
        'company_name',
        'discription',
        'bill_type',
        'transport_id',
        'courier_id',
        'parcel',
        'docket',
        'lr_no',
        'lr_date',
        'lr_photo',
        'invoice_id',
        'note',
        'po_no',
        'date',
        'payment_date',
        'payment_amount',
        'comments',
        'is_verified',
        'total_amount',
        'discount',
        'discount_amount',
        'last_closing',
        'redeem_coin',
        'net_amount',
        'other_charge',
        'cash_back_coin',
        'offer_discount',
        'bill_generated',
        'is_special',
        'estimate_type',
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

    public function estimateDetail()
    {
        return $this->hasMany(EstimateDetail::class, 'estimate_id');
    }

    public function billGroup()
    {
        return $this->belongsTo(BillGroup::class, 'bill_group_id');
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class, 'courier_id');
    }

    public function transport()
    {
        return $this->belongsTo(Transport::class, 'transport_id');
    }

    public function invoiceType()
    {
        return $this->belongsTo(InvoiceType::class, 'invoice_id');
    }

    public function viewUrl(): Attribute
    {

        return Attribute::make(

            get: function ($value) {

                return FileUpload::url($this->lr_photo, "$this->folder");
            }

        );
    }

    public function ListingImage()
    {

        return FileUpload::url($this->lr_photo, "$this->folder", "_200");
    }

    public function getSalesGroupByItem($filterParams)
    {
        // Create the date variables
        $fromDate = $filterParams['from_date'] ?? null;
        $toDate = $filterParams['to_date'] ?? null;
        $customer = $filterParams['customer'] ?? null;
        $item = $filterParams['item'] ?? null;
        $print = $filterParams['print_id'] ?? null;
        $partyType = $filterParams['party_type'] ?? null;
        $partyGroup = $filterParams['party_group'] ?? null;
        $billGenerated = $filterParams['bill_generated'] ?? null;
        $invoice = $filterParams['invoice'] ?? null;
        $parentUser = $filterParams['parentUser'] ?? null;
        $transport = $filterParams['transports'] ?? null;
        $search = $filterParams['search'] ?? null;

        // Generate a query to get the sales group by item
        $query = DB::table('estimate_details as od')
            ->select(
                'sl.name AS item',
                DB::raw("DATE_FORMAT(od.date, '%d-%m-%Y') as date"),
                DB::raw('SUM(DISTINCT(od.rate)) AS rate'),
                DB::raw('SUM(DISTINCT(od.discount)) AS discount'),
                DB::raw('SUM(DISTINCT(od.amount)) AS amount'),
                DB::raw('SUM(DISTINCT(od.qty)) AS qty'),
                DB::raw('MAX(od.remark) as remark'),
                DB::raw('MAX(od.created_at) as createdAt'),
                DB::raw('MAX(od.updated_at) as updatedAt')
            )
            ->leftJoin('estimates as ol', 'ol.id', '=', 'od.estimate_id')
            ->leftJoin('customer as CUS', 'CUS.id', '=', 'ol.customer_id')
            ->leftJoin('items as sl', 'sl.id', '=', 'od.item_id')
            ->where('od.branch_id', '=', session('branch_id'));

        // Add conditions based on the filter params
        if (!empty($customer) && $customer !== "0") {
            $query->whereIn('ol.customer_id', $customer);
        }
        if (!empty($parentUser) && $parentUser !== "0") {
            $query->where('CUS.parent_id', '=', $parentUser);
        }
        if (!empty($partyType)) {
            $query->where('CUS.party_type_id', '=', $partyType);
        }
        if (!empty($partyGroup)) {
            $query->whereIn('CUS.party_group_id', $partyGroup);
        }
        if (!empty($billGenerated)) {
            $query->where('ol.bill_generated', '=', $billGenerated);
        }
        if (!empty($invoice)) {
            $query->where('ol.invoice_id', '=', $invoice);
        }
        if (!empty($item) && $item !== "0") {
            $query->whereIn('od.item_id', $item);
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
        if(!empty($transport)){
            $query->where('ol.transport_id', '=', $transport);
        }
        if(!empty($search)){
            $query->where('sl.name', 'LIKE', "%{$search}%");
        }

        // Group by item_id and order by pl.id descending
        $query->groupBy('od.item_id', 'od.date', 'sl.name')
            ->orderByDesc('od.id');

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
        $partyType = $filterParams['party_type'] ?? null;
        $partyGroup = $filterParams['party_group'] ?? null;
        $billGenerated = $filterParams['bill_generated'] ?? null;
        $invoice = $filterParams['invoice'] ?? null;
        $parentUser = $filterParams['parentUser'] ?? null;
        $transport = $filterParams['transports'] ?? null;
        $search = $filterParams['search'] ?? null;

        // Generate a query to get the sales group by bill
        $query = EstimateDetail::select(
            'estimates.id as id',
            'estimates.po_no',
            'estimates.lr_photo',
            'estimates.estimate_code',
            'estimates.discount',
            'estimates.total_amount',
            'estimates.discount_amount',
            'estimates.net_amount',
            'estimates.parcel',
            'estimates.lr_date',
            'estimates.lr_no',
            'transports.name as transport',
            'estimates.docket',
            'estimates.bill_generated',
            'customer.name as customer',
            'users.name as created_by_name',
            'U.name as updated_by_name',
            'cities.name as city',
            'party_types.name as party_type',
            'party_types.color',
            'estimates.discription',
            'estimates.lr_photo',
            'couriers.name as courier_name',
            DB::raw("DATE_FORMAT(estimates.date, '%d-%m-%Y') as date"),
            DB::raw('SUM(DISTINCT(estimate_details.rate)) AS rate'),
            DB::raw('SUM(DISTINCT(estimate_details.discount)) AS discount'),
            DB::raw('SUM(DISTINCT(estimate_details.amount)) AS amount'),
            DB::raw('SUM(estimate_details.qty) AS qty'),
            DB::raw('MAX(estimate_details.remark) as remark'),
            DB::raw('MAX(estimate_details.created_at) as createdAt'),
            DB::raw('MAX(estimate_details.updated_at) as updatedAt')
        )
            ->leftJoin('estimates', 'estimates.id', '=', 'estimate_details.estimate_id')
            ->leftJoin('customer', 'customer.id', '=', 'estimates.customer_id')
            ->leftJoin('users', 'estimates.created_by', '=', 'users.id')
            ->leftJoin('users as U', 'estimates.updated_by', '=', 'U.id')
            ->leftJoin('party_types', 'party_types.id', '=', 'customer.party_type_id')
            ->leftJoin('transports', 'transports.id', '=', 'estimates.transport_id')
            ->leftJoin('couriers', 'couriers.id', '=', 'estimates.courier_id')
            ->leftJoin('cities', 'cities.id', '=', 'customer.city_id');

        // Add conditions based on the filter params
        if (!empty($customer) && $customer !== "0") {
            $query->whereIn('estimates.customer_id', $customer);
        }
        if (!empty($parentUser) && $parentUser !== "0") {
            $query->where('customer.parent_id', '=', $parentUser);
        }
        if (!empty($partyType)) {
            $query->where('customer.party_type_id', '=', $partyType);
        }
        if (!empty($partyGroup)) {
            $query->whereIn('customer.party_group_id', $partyGroup);
        }
        if (!empty($billGenerated)) {
            $query->where('estimates.bill_generated', '=', $billGenerated);
        }
        if (!empty($invoice)) {
            $query->where('estimates.invoice_id', '=', $invoice);
        }
        if (!empty($item) && $item !== "0") {
            $query->whereIn('estimate_details.item_id', $item);
        }
        if (!empty($print) && $print !== "0") {
            $query->whereIn('estimate_details.print_type_id', $print);
        }
        if (!empty($fromDate)) {
            $query->whereDate('estimates.date', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('estimates.date', '<=', $toDate);
        }
        if (!empty($transport)) {
            $query->where('estimates.transport_id', '=', $transport);
        }
        if (!empty($search)) {
            $query->where('estimate_details.item_name', 'like', '%' . $search . '%');
        }

        // Group by necessary fields and order by order id descending
        $query->groupBy(
            'estimates.id',
            'estimates.po_no',
            'customer.name',
            'cities.name',
            DB::raw("DATE_FORMAT(estimates.date, '%d-%m-%Y')")
        )
            ->orderByDesc('estimates.id');

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
        $partyType = $filterParams['party_type'] ?? null;
        $partyGroup = $filterParams['party_group'] ?? null;
        $billGenerated = $filterParams['bill_generated'] ?? null;
        $invoice = $filterParams['invoice'] ?? null;
        $parentUser = $filterParams['parentUser'] ?? null;
        $transport = $filterParams['transports'] ?? null;
        $search = $filterParams['search'] ?? null;

        // Generate a query to get the sales group by customer
        $query = DB::table('estimate_details as pd')
            ->select(
                'c.name as customer',
                'pt.color',
                DB::raw('SUM(DISTINCT(pd.rate)) AS rate'),
                DB::raw('SUM(DISTINCT(pd.discount)) AS discount'),
                DB::raw('SUM(DISTINCT(pd.amount)) AS amount'),
                DB::raw('SUM(DISTINCT(pd.qty)) AS qty'),
                DB::raw('MAX(pd.remark) as remark'),
                DB::raw('MAX(pd.created_at) as createdAt'),
                DB::raw('MAX(pd.updated_at) as updatedAt')
            )
            ->leftJoin('estimates as pl', 'pl.id', '=', 'pd.estimate_id')
            ->leftJoin('customer as c', 'c.id', '=', 'pl.customer_id')
            ->leftJoin('party_types as pt', 'pt.id', '=', 'c.party_type_id')
            ->where('pd.branch_id', '=', session('branch_id'));

        // Add conditions based on the filter params
        if (!empty($customer) && $customer !== "0") {
            $query->whereIn('pl.customer_id', $customer);
        }
        if (!empty($parentUser) && $parentUser !== "0") {
            $query->where('c.parent_id', '=', $parentUser);
        }
        if (!empty($partyType)) {
            $query->where('c.party_type_id', '=', $partyType);
        }
        if (!empty($partyGroup)) {
            $query->whereIn('c.party_group_id', $partyGroup);
        }
        if (!empty($billGenerated)) {
            $query->where('pl.bill_generated', '=', $billGenerated);
        }
        if (!empty($invoice)) {
            $query->where('pl.invoice_id', '=', $invoice);
        }
        if (!empty($item) && $item !== "0") {
            $query->whereIn('pd.item_id', $item);
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
        if (!empty($transport)) {
            $query->where('pl.transport_id', '=', $transport);
        }
        if (!empty($search)) {
            $query->where('pd.item_name', 'like', '%' . $search . '%');
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
        $partyType = $filterParams['party_type'] ?? null;
        $partyGroup = $filterParams['party_group'] ?? null;
        $billGenerated = $filterParams['bill_generated'] ?? null;
        $invoice = $filterParams['invoice'] ?? null;
        $parentUser = $filterParams['parentUser'] ?? null;
        $transport = $filterParams['transports'] ?? null;
        $search = $filterParams['search'] ?? null;

        // Generate a query to get the sales group by sale-detail
        $query = DB::table('estimate_details as pd')
            ->select(
                'I.name as item_name',
                'CUS.name as customer',
                'users.name as created_by_name',
                'U.name as updated_by_name',
                'party_types.color',
                'pt.name as print_type',
                'CI.name as city',
                't.name as transports',
                'pd.block',
                'pd.remark',
                'pd.narration',
                'pl.lr_photo',
                DB::raw("DATE_FORMAT(pl.date, '%d-%m-%Y') as date"),
                DB::raw('SUM(DISTINCT(pd.rate)) AS rate'),
                DB::raw('SUM(DISTINCT(pd.discount)) AS discount'),
                DB::raw('SUM(DISTINCT(pd.amount)) AS amount'),
                DB::raw('SUM(DISTINCT(pd.qty)) AS qty'),
                DB::raw('MAX(pd.remark) as remark'),
                DB::raw('MAX(pd.created_at) as createdAt'),
                DB::raw('MAX(pd.updated_at) as updatedAt')
            )
            ->leftJoin('estimates as pl', 'pl.id', '=', 'pd.estimate_id')
            ->leftJoin('print_type as pt', 'pd.print_type_id', '=', 'pt.id')
            ->leftJoin('items as I', 'I.id', '=', 'pd.item_id')
            ->leftJoin('customer as CUS', 'CUS.id', '=', 'pl.customer_id')
            ->leftJoin('users', 'pl.created_by', '=', 'users.id')
            ->leftJoin('users as U', 'pl.updated_by', '=', 'U.id')
            ->leftJoin('party_types', 'party_types.id', '=', 'CUS.party_type_id')
            ->leftJoin('transports as t', 't.id', '=', 'pd.transport_id')
            ->leftJoin('cities as CI', 'CI.id', '=', 'CUS.city_id')
            ->where('pd.branch_id', '=', session('branch_id'));

        // Add conditions based on the filter params
        if (!empty($customer) && $customer !== "0") {
            $query->whereIn('pl.customer_id', $customer);
        }
        if (!empty($parentUser) && $parentUser !== "0") {
            $query->where('CUS.parent_id', '=', $parentUser);
        }
        if (!empty($partyType)) {
            $query->where('CUS.party_type_id', '=', $partyType);
        }
        if (!empty($partyGroup)) {
            $query->whereIn('CUS.party_group_id', $partyGroup);
        }
        if (!empty($billGenerated)) {
            $query->where('pl.bill_generated', '=', $billGenerated);
        }
        if (!empty($invoice)) {
            $query->where('pl.invoice_id', '=', $invoice);
        }
        if (!empty($item) && $item !== "0") {
            $query->whereIn('pd.item_id', $item);
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
        if (!empty($transport)) {
            $query->where('pd.transport_id', '=', $transport);
        }
        if (!empty($search)) {
            $query->where('pd.item_name', 'LIKE', "%{$search}%");
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
        $partyType = $filterParams['party_type'] ?? null;
        $partyGroup = $filterParams['party_group'] ?? null;
        $billGenerated = $filterParams['bill_generated'] ?? null;
        $invoice = $filterParams['invoice'] ?? null;
        $parentUser = $filterParams['parentUser'] ?? null;
        $transport = $filterParams['transports'] ?? null;
        $search = $filterParams['search'] ?? null;

        // Generate a query to get the sales group by customer
        $query = DB::table('estimate_details as pd')
            ->select(
                'c.name as customer',
                'pt.color',
                DB::raw('SUM(DISTINCT(pd.rate)) AS rate'),
                DB::raw('SUM(DISTINCT(pd.discount)) AS discount'),
                DB::raw('SUM(DISTINCT(pd.amount)) AS amount'),
                DB::raw('SUM(DISTINCT(pd.qty)) AS qty'),
                DB::raw('MAX(pd.remark) as remark'),
                DB::raw('MAX(pd.created_at) as createdAt'),
                DB::raw('MAX(pd.updated_at) as updatedAt')
            )
            ->leftJoin('estimates as pl', 'pl.id', '=', 'pd.estimate_id')
            ->leftJoin('customer as c', 'c.id', '=', 'pl.customer_id')
            ->leftJoin('party_types as pt', 'pt.id', '=', 'c.party_type_id')
            ->where('pd.branch_id', '=', session('branch_id'));

        // Add conditions based on the filter params
        if (!empty($customer) && $customer !== "0") {
            $query->whereIn('pl.customer_id', $customer);
        }
        if (!empty($parentUser) && $parentUser !== "0") {
            $query->where('c.parent_id', '=', $parentUser);
        }
        if (!empty($partyType)) {
            $query->where('c.party_type_id', '=', $partyType);
        }
        if (!empty($partyGroup)) {
            $query->whereIn('c.party_group_id', $partyGroup);
        }
        if (!empty($billGenerated)) {
            $query->where('pl.bill_generated', '=', $billGenerated);
        }
        if (!empty($invoice)) {
            $query->where('pl.invoice_id', '=', $invoice);
        }
        if (!empty($item) && $item !== "0") {
            $query->whereIn('pd.item_id', $item);
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
        if (!empty($transport)) {
            $query->where('pd.transport_id', '=', $transport);
        }
        if (!empty($search)) {
            $query->where('pd.item_name', 'LIKE', "%{$search}%");
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
        $partyType = $filterParams['party_type'] ?? null;
        $partyGroup = $filterParams['party_group'] ?? null;
        $billGenerated = $filterParams['bill_generated'] ?? null;
        $invoice = $filterParams['invoice'] ?? null;
        $parentUser = $filterParams['parentUser'] ?? null;
        $transport = $filterParams['transports'] ?? null;
        $search = $filterParams['search'] ?? null;

        // Generate a query to get the sales group by customer
        $query = DB::table('estimate_details as pd')
            ->select(
                'pt.name as print_type',
                'pd.print_type_id',
                DB::raw('SUM(DISTINCT(pd.rate)) AS rate'),
                DB::raw('SUM(DISTINCT(pd.discount)) AS discount'),
                DB::raw('SUM(DISTINCT(pd.amount)) AS amount'),
                DB::raw('SUM(DISTINCT(pd.qty)) AS qty'),
                DB::raw('MAX(pd.remark) as remark'),
                DB::raw('MAX(pd.created_at) as createdAt'),
                DB::raw('MAX(pd.updated_at) as updatedAt')
            )
            ->leftJoin('estimates as pl', 'pl.id', '=', 'pd.estimate_id')
            ->leftJoin('customer as CUS', 'CUS.id', '=', 'pl.customer_id')
            ->leftJoin('print_type as pt', 'pt.id', '=', 'pd.print_type_id')
            ->where('pd.branch_id', '=', session('branch_id'));

        // Add conditions based on the filter params
        if (!empty($customer) && $customer !== "0") {
            $query->whereIn('pl.customer_id', $customer);
        }
        if (!empty($parentUser) && $parentUser !== "0") {
            $query->where('CUS.parent_id', '=', $parentUser);
        }
        if (!empty($partyType)) {
            $query->where('CUS.party_type_id', '=', $partyType);
        }
        if (!empty($partyGroup)) {
            $query->whereIn('CUS.party_group_id', $partyGroup);
        }
        if (!empty($billGenerated)) {
            $query->where('pl.bill_generated', '=', $billGenerated);
        }
        if (!empty($invoice)) {
            $query->where('pl.invoice_id', '=', $invoice);
        }
        if (!empty($item) && $item !== "0") {
            $query->whereIn('pd.item_id', $item);
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
        if (!empty($transport)) {
            $query->where('pd.transport_id', '=', $transport);
        }
        if (!empty($search)) {
            $query->where('pd.item_name', 'LIKE', "%{$search}%");
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
        $billGenerated = $filterParams['bill_generated'] ?? null;
        $invoice = $filterParams['invoice'] ?? null;
        $parentUser = $filterParams['parentUser'] ?? null;
        $transport = $filterParams['transports'] ?? null;
        $search = $filterParams['search'] ?? null;

        // Generate a query to get the sales group by customer
        $query = DB::table('estimate_details as pd')
            ->select(
                'customer.party_group_id',
                'pg.name as party_group_name',
                DB::raw('SUM(DISTINCT(pd.rate)) AS rate'),
                DB::raw('SUM(DISTINCT(pd.discount)) AS discount'),
                DB::raw('SUM(DISTINCT(pd.amount)) AS amount'),
                DB::raw('SUM(DISTINCT(pd.qty)) AS qty'),
                DB::raw('MAX(pd.remark) as remark'),
                DB::raw('MAX(pd.created_at) as createdAt'),
                DB::raw('MAX(pd.updated_at) as updatedAt')
            )
            ->leftJoin('estimates as pl', 'pl.id', '=', 'pd.order_id')
            ->leftJoin('customer', 'pl.customer_id', '=', 'customer.id')
            ->leftJoin('party_groups as pg', 'customer.party_group_id', '=', 'pg.id')
            ->where('pd.branch_id', '=', session('branch_id'));

        // Add conditions based on the filter params
        if (!empty($customer) && $customer !== "0") {
            $query->whereIn('pl.customer_id', $customer);
        }
        if (!empty($parentUser) && $parentUser !== "0") {
            $query->where('customer.parent_id', '=', $parentUser);
        }
        if (!empty($partyType)) {
            $query->where('customer.party_type_id', '=', $partyType);
        }
        if (!empty($partyGroup)) {
            $query->whereIn('customer.party_group_id', $partyGroup);
        }
        if (!empty($billGenerated)) {
            $query->where('pl.bill_generated', '=', $billGenerated);
        }
        if (!empty($invoice)) {
            $query->where('pl.invoice_id', '=', $invoice);
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
            $query->where('pd.narration', '=', $detail);
        }
        if (!empty($transport)) {
            $query->where('pd.transport_id', '=', $transport);
        }
        if (!empty($search)) {
            $query->where('pd.item_name', 'LIKE', "%{$search}%");
        }

        if (!empty($status) && $status !== "all") {
            if ($status == "pending") {
                $query->whereColumn('pd.qty', '!=', 'pd.dispatch_qty');
            }
            if ($status == "dispatched") {
                $query->whereColumn('pd.qty', '=', 'pd.dispatch_qty');
            }
        }

        $query->groupBy('customer.party_group_id')
            ->orderByDesc('pl.id');

        return $query->get()->toArray();
    }
}
