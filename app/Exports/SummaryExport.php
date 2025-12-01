<?php

namespace App\Exports;

use App\Models\EstimateDetail;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class SummaryExport implements FromView
{
    public function __construct(public $estimateId, public $status)
    {
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        $estimate = EstimateDetail::select(DB::raw('SUM(amount) as amount'), DB::raw('SUM(qty) as qty'), 'item_id', 'print_type_id')
            ->with('item', 'item.categories.itemGroup', 'printType')
            ->whereIn('estimate_id', $this->estimateId)
            ->groupBy('item_id', 'print_type_id')
            ->get();

        $mergedProducts = [];
        foreach ($estimate as $value) {
            $itemId = $value->item->id;
            $printTypeName = $value->printType->name;
            $groupName = $value->item->categories->itemGroup->group_name;

            if (!array_key_exists($groupName,$mergedProducts)) {
                $mergedProducts[$groupName] = [];
            }

            if (!array_key_exists($itemId,$mergedProducts[$groupName])) {
                $mergedProducts[$groupName][$itemId] = [
                    'item_name' => $value->item->name,
                    'item_id' => $itemId,
                ];
            }

            $mergedProducts[$groupName][$itemId][$printTypeName]['amount'] = $value->amount;
            $mergedProducts[$groupName][$itemId][$printTypeName]['qty'] = $value->qty;
        }

        $data = $mergedProducts;

        if (!$data) {
            abort(404);
        }
        $status = $this->status;
        return view('estimate.summary_excel', compact('data', 'status'));
    }
}
