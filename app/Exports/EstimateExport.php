<?php

namespace App\Exports;

use App\Models\EstimateDetail;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class EstimateExport implements FromView
{
    public function __construct(public $estimateId)
    {
    }
    public function view(): View
    {
        // $estimate = EstimateDetail::with('item','item.category','item.itemGroup')
        //             ->whereIn('estimate_id', $this->estimateId)
        //             ->groupBy('rate', 'discount', 'category.item_group_id')
        //             ->get();

        $query = DB::table('estimate_details as ed')
            ->select(
                'ig.bill_title',
                'ed.rate',
                'ed.discount',
                DB::raw('SUM(ed.qty) as quantity'),
            )
            ->leftJoin('items as item', 'ed.item_id', '=', 'item.id')
            ->leftJoin('item_categories as ic', 'item.categories_id', '=', 'ic.id')
            ->leftJoin('item_group as ig', 'ic.item_group_id', '=', 'ig.id')
            ->whereIn('ed.estimate_id', $this->estimateId)
            ->where('ed.branch_id', '=', session('branch_id'))
            ->groupBy('ed.rate', 'ed.discount', 'ig.id', 'ig.bill_title');

        $estimate = $query->get();
        if (!$estimate) {
            abort(404);
        }

        return view('estimate.excel', compact('estimate'));
    }
}
