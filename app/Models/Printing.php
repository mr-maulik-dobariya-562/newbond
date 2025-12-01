<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Printing extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date',
        'print_type_id',
        'machine_id',
        'operator_id',
        'production_qty',
        'rection_qty',
        'working_hours_id',
        'rejection_qty',
        'rejection_reason',
        'remarks',
        'created_by',
        "branch_id",
    ];

    public function printType()
    {
        return $this->belongsTo(PrintType::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function operator()
    {
        return $this->belongsTo(Customer::class, 'operator_id');
    }

    public function workingHours()
    {
        return $this->belongsTo(WorkingHours::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getSalesGroupByOperator($filterParams)
    {
        // Create the date variables
        $fromDate = $filterParams['from_date'] ?? null;
        $toDate = $filterParams['to_date'] ?? null;
        $operator_id = $filterParams['operator_id'];
        $print_type_id = $filterParams['printing_type_id'];
        $machine_id = $filterParams['machine_id'];
        $working_hours_id = $filterParams['working_hours_id'];

        // Generate a query to get the sales group by item
        $query = Printing::with('machine', 'printType')
            ->select(
                'operator_id',
                DB::raw('SUM(production_qty) as production_qty'),
                DB::raw('SUM(rection_qty) as rection_qty'),
                DB::raw('SUM(rejection_qty) as rejection_qty'),
            );

        // Add conditions based on the filter params
        if (!empty($fromDate)) {
            $query->whereDate('date', '>=', $fromDate);
        }

        if (!empty($toDate)) {
            $query->whereDate('date', '<=', $toDate);
        }

        if (!empty($operator_id)) {
            $query->where('operator_id', '=', $operator_id);
        }

        if (!empty($print_type_id)) {
            $query->where('print_type_id', '=', $print_type_id);
        }

        if (!empty($machine_id)) {
            $query->where('machine_id', '=', $machine_id);
        }

        if (!empty($working_hours_id)) {
            $query->where('working_hours_id', '=', $working_hours_id);
        }
        $query->groupBy('operator_id')->orderByDesc('id');

        $results = $query->get();
        foreach ($results as $key => $value) {
            if (is_string($value->operator_id)) {
                $value->operator_name = implode(' , ', Employee::whereIn('id', explode(',', $value->operator_id))->pluck('name')->toArray());
            }
        }
        return $results;
    }

    public function getSalesGroupByMachine($filterParams)
    {
        // Create the date variables
        $fromDate = $filterParams['from_date'] ?? null;
        $toDate = $filterParams['to_date'] ?? null;
        $operator_id = $filterParams['operator_id'];
        $print_type_id = $filterParams['printing_type_id'];
        $machine_id = $filterParams['machine_id'];
        $working_hours_id = $filterParams['working_hours_id'];

        // Generate a query to get the sales group by item
        $query = Printing::with('operator', 'machine', 'printType', 'createdBy')
            ->select(
                'machine_id',
                DB::raw('SUM(production_qty) as production_qty'),
                DB::raw('SUM(rection_qty) as rection_qty'),
                DB::raw('SUM(rejection_qty) as rejection_qty'),
            );

        // Add conditions based on the filter params
        if (!empty($fromDate)) {
            $query->whereDate('date', '>=', $fromDate);
        }

        if (!empty($toDate)) {
            $query->whereDate('date', '<=', $toDate);
        }

        if (!empty($operator_id)) {
            $query->where('operator_id', '=', $operator_id);
        }

        if (!empty($print_type_id)) {
            $query->where('print_type_id', '=', $print_type_id);
        }

        if (!empty($machine_id)) {
            $query->where('machine_id', '=', $machine_id);
        }

        if (!empty($working_hours_id)) {
            $query->where('working_hours_id', '=', $working_hours_id);
        }
        $query->groupBy('machine_id')->orderByDesc('id');

        return $query->get();
    }

    public function getSalesGroupByVoucher($filterParams)
    {
        // Create the date variables
        $fromDate = $filterParams['from_date'] ?? null;
        $toDate = $filterParams['to_date'] ?? null;
        $operator_id = $filterParams['operator_id'];
        $print_type_id = $filterParams['printing_type_id'];
        $machine_id = $filterParams['machine_id'];
        $working_hours_id = $filterParams['working_hours_id'];

        // Generate a query to get the sales group by item
        $query = $query = Printing::with('machine', 'printType');

        // Add conditions based on the filter params
        if (!empty($fromDate)) {
            $query->whereDate('date', '>=', $fromDate);
        }

        if (!empty($toDate)) {
            $query->whereDate('date', '<=', $toDate);
        }

        if (!empty($operator_id)) {
            $query->where('operator_id', '=', $operator_id);
        }

        if (!empty($print_type_id)) {
            $query->where('print_type_id', '=', $print_type_id);
        }

        if (!empty($machine_id)) {
            $query->where('machine_id', '=', $machine_id);
        }

        if (!empty($working_hours_id)) {
            $query->where('working_hours_id', '=', $working_hours_id);
        }

        $query->orderByDesc('id');

        $results = $query->get();
        foreach ($results as $key => $value) {
            if (is_string($value->operator_id)) {
                $value->operator_name = implode(' , ', Employee::whereIn('id', explode(',', $value->operator_id))->pluck('name')->toArray());
            }
        }
        return $results;
    }

    public function getSalesGroupByCreated($filterParams)
    {
        // Create the date variables
        $fromDate = $filterParams['from_date'] ?? null;
        $toDate = $filterParams['to_date'] ?? null;
        $operator_id = $filterParams['operator_id'];
        $product_type_id = $filterParams['product_type_id'];
        $row_material_id = $filterParams['row_material_id'];
        $shift_id = $filterParams['shift_id'];
        $machine_id = $filterParams['machine_id'];

        // Generate a query to get the sales group by item
        $query = Printing::with('shift', 'machine', 'operator', 'productType', 'rowMaterial', 'cavity')
            ->select(
                DB::raw('SUM(runner_waste) as runner_waste_sum'),
                DB::raw('SUM(production_weight) as production_weight'),
                DB::raw('SUM(production_pieces_quantity) as production_pieces_quantity'),
                DB::raw('SUM(component_rejection) as component_rejection'),
            );

        // Add conditions based on the filter params
        if (!empty($fromDate)) {
            $query->whereDate('date', '>=', $fromDate);
        }

        if (!empty($toDate)) {
            $query->whereDate('date', '<=', $toDate);
        }

        if (!empty($operator_id)) {
            $query->where('operator_id', '=', $operator_id);
        }

        if (!empty($print_type_id)) {
            $query->where('print_type_id', '=', $print_type_id);
        }

        if (!empty($machine_id)) {
            $query->where('machine_id', '=', $machine_id);
        }
        $query->groupBy('created_by')->orderByDesc('id');

        return $query->get();
    }
}
