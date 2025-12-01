<?php

namespace App\Exports;

use App\Models\Estimate;
use App\Models\EstimateDetail;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class RegisterExport implements FromView
{
	public function __construct(public $estimateId)
	{
	}

	/**
	* @return \Illuminate\Support\Collection
	*/
	public function view(): View
	{

		$estimate = Estimate::whereIn('id', $this->estimateId)
			->with(['estimateDetail:id,estimate_id','customer'])
			->groupBy('customer_id')
			->get();
			foreach ($estimate as $key => $value) {
				$estimateId = Estimate::whereIn('id', $this->estimateId)->where('customer_id', $value->customer_id)->pluck('id');
				$estimateDetail = EstimateDetail::select(
					'estimates.id as id',
					'estimates.customer_id',
					'estimates.estimate_code',
					'invoice_types.name as invoice_type',
					'customer.name as customer',
					DB::raw("DATE_FORMAT(estimates.date, '%d-%m-%Y') as date"),
					DB::raw('estimates.total_amount'),
					DB::raw('estimates.discount_amount'),
					DB::raw('estimates.net_amount'),
					DB::raw('estimates.other_charge'),
					DB::raw('estimates.redeem_coin'),
				)
				->leftJoin('estimates', 'estimates.id', '=', 'estimate_details.estimate_id')
				->leftJoin('customer', 'customer.id', '=', 'estimates.customer_id')
				->leftJoin('invoice_types', 'invoice_types.id', '=', 'estimates.invoice_id')
				->whereIn('estimates.id', $estimateId)
				->groupBy(
					'estimates.id',
				)->get();
				$estimate[$key]->estimateDetail = $estimateDetail;
			}

		if (!$estimate) {
			abort(404);
		}

		return view('estimate.register_excel', compact('estimate'));
	}
}
