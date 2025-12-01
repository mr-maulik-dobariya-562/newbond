<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Molding extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "date",
        "shift_id",
        "machine_id",
        "operator_id",
        "product_type_id",
        "row_material_id",
        "cavity_id",
        "item_id",
        "machine_counter",
        "production_weight",
        "production_pieces_quantity",
        "runner_waste",
        "component_rejection",
        "color_type",
        "remark",
        "branch_id",
        "created_by"
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class, "shift_id");
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class, "machine_id");
    }

    public function operator()
    {
        return $this->belongsTo(Customer::class, "operator_id");
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class, "product_type_id");
    }

    public function rowMaterial()
    {
        return $this->belongsTo(RowMaterial::class, "row_material_id");
    }

    public function cavity()
    {
        return $this->belongsTo(Cavity::class, "cavity_id");
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function item()
    {
        return $this->belongsTo(Item::class, "item_id");
    }

    /**
     * Get the molding sales group by operator
     *
     * @param array $filterParams
     * @return \Illuminate\Support\Collection
     */
    public function getSalesGroupByOperator($filterParams)
    {
        // Create the date variables
        $fromDate = $filterParams['from_date'] ?? null;
        $toDate = $filterParams['to_date'] ?? null;
        $operator_id = $filterParams['operator_id'];
        $product_type_id = $filterParams['product_type_id'];
        $row_material_id = $filterParams['row_material_id'];
        $shift_id = $filterParams['shift_id'];
        $machine_id = $filterParams['machine_id'];
        $location_id = $filterParams['location_id'];
        $product_id = $filterParams['product_id'];

        // Generate a query to get the sales group by item
        $query = Molding::with('item', 'shift', 'machine', 'productType', 'rowMaterial', 'cavity')
            ->select(
                'operator_id',
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

        if (!empty($product_type_id)) {
            $query->where('product_type_id', '=', $product_type_id);
        }

        if (!empty($row_material_id)) {
            $query->where('row_material_id', '=', $row_material_id);
        }

        if (!empty($shift_id)) {
            $query->where('shift_id', '=', $shift_id);
        }

        if (!empty($machine_id)) {
            $query->where('machine_id', '=', $machine_id);
        }

        if (!empty($product_id)) {
            $query->where('item_id', '=', $product_id);
        }

        if (!empty($location_id)) {
            $query->whereHas('machine', function ($q) use ($location_id) {
                $q->where('location_id', '=', $location_id);
            });
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
        $product_type_id = $filterParams['product_type_id'];
        $row_material_id = $filterParams['row_material_id'];
        $shift_id = $filterParams['shift_id'];
        $machine_id = $filterParams['machine_id'];
        $location_id = $filterParams['location_id'];
        $product_id = $filterParams['product_id'];

        // Generate a query to get the sales group by item
        $query = Molding::with('item', 'shift', 'machine', 'productType', 'rowMaterial', 'cavity')
            ->select(
                'machine_id',
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

        if (!empty($product_type_id)) {
            $query->where('product_type_id', '=', $product_type_id);
        }

        if (!empty($row_material_id)) {
            $query->where('row_material_id', '=', $row_material_id);
        }

        if (!empty($shift_id)) {
            $query->where('shift_id', '=', $shift_id);
        }

        if (!empty($machine_id)) {
            $query->where('machine_id', '=', $machine_id);
        }

        if (!empty($product_id)) {
            $query->where('item_id', '=', $product_id);
        }

        if (!empty($location_id)) {
            $query->whereHas('machine', function ($q) use ($location_id) {
                $q->where('location_id', '=', $location_id);
            });
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
        $product_type_id = $filterParams['product_type_id'];
        $row_material_id = $filterParams['row_material_id'];
        $shift_id = $filterParams['shift_id'];
        $machine_id = $filterParams['machine_id'];
        $location_id = $filterParams['location_id'];
        $product_id = $filterParams['product_id'];

        // Generate a query to get the sales group by item
        $query = Molding::with('item', 'shift', 'machine', 'productType', 'rowMaterial', 'cavity');

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

        if (!empty($product_type_id)) {
            $query->where('product_type_id', '=', $product_type_id);
        }

        if (!empty($row_material_id)) {
            $query->where('row_material_id', '=', $row_material_id);
        }

        if (!empty($shift_id)) {
            $query->where('shift_id', '=', $shift_id);
        }

        if (!empty($machine_id)) {
            $query->where('machine_id', '=', $machine_id);
        }

        if (!empty($product_id)) {
            $query->where('item_id', '=', $product_id);
        }

        if (!empty($location_id)) {
            $query->whereHas('machine', function ($q) use ($location_id) {
                $q->where('location_id', '=', $location_id);
            });
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
        $query = Molding::with('item', 'shift', 'machine', 'operator', 'productType', 'rowMaterial', 'cavity')
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

        if (!empty($product_type_id)) {
            $query->where('product_type_id', '=', $product_type_id);
        }

        if (!empty($row_material_id)) {
            $query->where('row_material_id', '=', $row_material_id);
        }

        if (!empty($shift_id)) {
            $query->where('shift_id', '=', $shift_id);
        }

        if (!empty($machine_id)) {
            $query->where('machine_id', '=', $machine_id);
        }

        if (!empty($location_id)) {
            $query->whereHas('machine', function ($q) use ($location_id) {
                $q->where('location_id', '=', $location_id);
            });
        }
        $query->groupBy('created_by')->orderByDesc('id');

        return $query->get();
    }
}
