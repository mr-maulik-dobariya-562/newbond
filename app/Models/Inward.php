<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Inward extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'inwards';

    protected $fillable = [
        'customer_id',
        'date',
        'is_special',
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

    public function inwardDetail()
    {
        return $this->hasMany(InwardDetail::class, 'inward_id');
    }

    public function getInwardGroupByInward($filterParams)
    {
        $fromDate = $filterParams['from_date'] ?? null;
        $toDate = $filterParams['to_date'] ?? null;
        $item = $filterParams['item'] ?? null;

        // Generate a query to get the sales group by bill
        $query = InwardDetail::select(
            'inwards.id as id',
            'users.name as createdBy',
            DB::raw("DATE_FORMAT(inwards.date, '%d-%m-%Y') as date"),
            DB::raw('SUM(inward_details.qty) AS qty'),
            DB::raw('SUM(inward_details.parcel) AS parcel'),
            DB::raw('MAX(inward_details.remark) as remark'),
            DB::raw('MAX(inward_details.created_at) as createdAt')
        )
            ->leftJoin('inwards', 'inwards.id', '=', 'inward_details.inward_id')
            ->leftJoin('users', 'users.id', '=', 'inwards.created_by');

        // Add conditions based on the filter params
        if (!empty($item) && $item !== "0") {
            $query->where('inward_details.item_id', $item);
        }
        if (!empty($fromDate)) {
            $query->whereDate('inwards.date', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('inwards.date', '<=', $toDate);
        }

        // Group by necessary fields and order by order id descending
        $query->groupBy(
            'inwards.id',
            DB::raw("DATE_FORMAT(inwards.date, '%d-%m-%Y')")
        )
            ->orderByDesc('inwards.id');

        // Execute the query and return the result
        return $query->get()->toArray();
    }

    public function getInwardGroupByVoucher($filterParams)
    {
        // Create the date variables
        $fromDate = $filterParams['from_date'] ?? null;
        $toDate = $filterParams['to_date'] ?? null;
        $item = $filterParams['item'] ?? null;

        // Generate a query to get the inwards group by inward-detail
        $query = DB::table('inward_details as iwd')
            ->select(
                'I.name as item_name',
                'users.name as createdBy',
                'iwd.remark',
                DB::raw("DATE_FORMAT(iw.date, '%d-%m-%Y') as date"),
                DB::raw('SUM(iwd.qty) AS qty'),
                DB::raw('SUM(iwd.parcel) AS parcel'),
                DB::raw('MAX(iwd.remark) as remark'),
                DB::raw('MAX(iwd.created_at) as createdAt')
            )
            ->leftJoin('inwards as iw', 'iw.id', '=', 'iwd.inward_id')
            ->leftJoin('items as I', 'I.id', '=', 'iwd.item_id')
            ->leftJoin('users', 'users.id', '=', 'iw.created_by')
            ->where('iwd.branch_id', session('branchId'));

        // Add conditions based on the filter params
        if (!empty($item) && $item !== "0") {
            $query->where('iwd.item_id', '=', $item);
        }
        if (!empty($fromDate)) {
            $query->whereDate('iw.date', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('iw.date', '<=', $toDate);
        }

        // Group by inward_details id and order by inwards id descending
        $query->groupBy('iw.id', 'I.name', 'iw.date')
            ->orderByDesc('iw.id');

        // Execute the query and return the result
        return $query->get()->toArray();
    }
}
