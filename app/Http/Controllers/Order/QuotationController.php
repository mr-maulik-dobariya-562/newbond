<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\Customer;
use App\Models\Item;
use App\Models\ItemDetail;
use App\Models\ItemGroupPrice;
use App\Models\PartyType;
use App\Models\PrintType;
use App\Models\Quotation;
use App\Models\QuotationDetail;
use App\Models\Transport;
use App\Traits\DataTable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller implements HasMiddleware
{
	use DataTable;
	public static function middleware(): array
	{
		return [
			new Middleware('permission:quotation-create', only: ['create']),
			new Middleware('permission:quotation-view', only: ['index', "getList"]),
			new Middleware('permission:quotation-edit', only: ['edit', "update"]),
			new Middleware('permission:quotation-delete', only: ['destroy']),
		];
	}

	protected $quotation;

	public function __construct(Quotation $quotation)
	{
		$this->quotation = $quotation;
	}

	/**
		* Display a listing of the resource.
		*/
	public function index()
	{
		$customers = Customer::where('party_type_id', '!=', '4')->get();
		$partyTypes = PartyType::all();
		$item = Item::with('printTypes')->get();
		$prints = PrintType::all();
		return view('quotation.index', compact('customers', 'partyTypes', 'item', 'prints'));
	}

	/**
		* Show the form for creating a new resource.
		*/
	public function create()
	{
		$customers = Customer::where('party_type_id', '!=', '4')->get();

		$partyTypes = PartyType::all();
		$printTypes = PrintType::all();
		$transport = Transport::all();
		$couriers = Courier::all();
		$item = Item::with('printTypes')->get();
		$items = [];
		foreach ($item as $key => $value) {
			foreach ($value->printTypes as $k => $v) {
				$items[] = [$value->name . ' - ' . $v->name . ' - ' . $value->packing, $value->id . ' - ' . $v->id . ',' . $value->name . ' - ' . $v->name];
			}
		}
		return view("Quotation::create", compact("customers", 'partyTypes', 'couriers', 'items', "transport", "printTypes"));
	}

	/**
		* Store a newly created resource in storage.
		*/
	public function store(Request $request)
	{
		$request->validate([
			"customer_id" => "required|numeric",
			"address" => "required|string",
			"discription" => "nullable|string|max:255",
			"detail_date" => "required",
			"company_name" => "nullable|string|max:255",
			"delivery_date" => "required",

			"payment_date" => "required",
			// "discount" => "required|numeric",
			"total_amount" => "required|numeric",
			"net_amount" => "required|numeric",

			"item_id" => "required|array|min:1",
			"item_id.*" => "required",
			"qty" => "required|array|min:1",
			"qty.*" => "required|numeric",
			"rate" => "required|array|min:1",
			"rate.*" => "required|numeric",
			"amount" => "required|array|min:1",
			"amount.*" => "required|numeric",
		]);
		DB::beginTransaction();

		try {
			$quotation = Quotation::create([
				"customer_id" => $request->customer_id,
				"address" => $request->address,
				"discription" => $request->discription,
				"print_type_id" => $request->print_type_id ? $request->print_type_id : null,
				"quotation_code" => 'Q - ' . (Quotation::latest('id')->first()?->id + 1 ?? 1),
				"po_no" => $request->po_no ?? "",
				"date" => $request->detail_date ?? Carbon::now()->toDateString(),
				"company_name" => $request->company_name,
				"payment_date" => $request->payment_date,
				"payment_amount" => $request->payment_amount ?? 0,
				"comments" => $request->comments,
				"is_verified" => $request->is_verified ?? 0,
				"discount" => $request->discount ?? 0,
				"total_amount" => $request->total_amount,
				"redeem_coin" => $request->redeem_coin,
				"net_amount" => round($request->net_amount, 2),
				"cash_back_coin" => $request->cash_back_coin,
				"block_find" => $request->block_find,
				"delivery_date" => $request->delivery_date,
				"discount_amount" => $request->discount_amount ?? 0,
				"created_by" => auth()->id()
			]);

			$lastInsertedId = $quotation->id;

			foreach ($request['item_id'] as $index => $item) {
				QuotationDetail::create([
					'quotation_id' => $lastInsertedId,
					'item_id' => explode('-', explode(',', $item)[0])[0],
					'print_type_id' => explode('-', explode(',', $item)[0])[1],
					'item_name' => explode(',', $item)[1],
					'qty' => $request->qty[$index],
					'rate' => $request->rate[$index],
					'block' => $request->block[$index],
					'narration' => $request->narration[$index],
					'remark' => $request->remark[$index],
					'transport_id' => $request->transport_id[$index] ?? NULL,
					'amount' => $request->amount[$index],
					// 'date' => $request->date[$index],
					// 'design' => $request->design[$index],
					'discount' => $request->quotation_discount[$index],
					"created_by" => auth()->id()
				]);
			}

			// $walletController = new \App\Http\Controllers\WalletController();
			// if ($request->redeem_coin > 0) {
			// if ($request->customer_id) {
			// $walletController->debit($quotation->customer_id, $request->redeem_coin, $lastInsertedId, 1, 'Quotation Redeem Coin', date('Y-m-d'));
			// }
			// }

			// if ($request->cash_back_coin > 0) {
			// if ($request->customer_id) {
			// $walletController->credit($request->customer_id, $request->cash_back_coin, $lastInsertedId, 3, 'Quotation Cash Back Coin', date('Y-m-d'));
			// }
			// }

			DB::commit();

			if ($request->ajax()) {
				return $this->withSuccess("Quotation created successfully");
			}
			return $this->withSuccess("Quotation created successfully")->back();
		} catch (\Exception $e) {
			DB::rollBack();
			throw $e;
		}
	}

	/**
		* Show the form for editing the specified resource.
		*/
	public function edit(Quotation $quotation)
	{
		$partyTypes = PartyType::all();
		$printTypes = PrintType::all();
		$transport = Transport::all();
		$couriers = Courier::all();
		$item = Item::with('printTypes')->get();
		$items = [];
		foreach ($item as $key => $value) {
			foreach ($value->printTypes as $k => $v) {
				$items[] = [$value->name . ' - ' . $v->name . ' - ' . $value->packing, $value->id . ' - ' . $v->id . ',' . $value->name . ' - ' . $v->name];
			}
		}
		$quotation->load(['customer']);
		$quotationDetail = QuotationDetail::with('item.itemDetails')->where('quotation_id', $quotation->id)->get();
		return view("Quotation::create", compact('quotation', 'quotationDetail', 'items', "transport", "printTypes"));
	}

	/**
		* Update the specified resource in storage.
		*/
	public function update(Request $request, Quotation $quotation)
	{
		$request->validate([
			"customer_id" => "required|numeric",
			"address" => "required|string",
			"discription" => "nullable|string|max:255",
			"detail_date" => "required",
			"company_name" => "nullable|string|max:255",
			"print_type_id" => "required|numeric",
			"delivery_date" => "required",

			"payment_date" => "required",
			// "discount" => "required|numeric",
			"total_amount" => "required|numeric",
			// "redeem_coin" => "required|numeric",
			// "cash_back_coin" => "required|numeric",
			"net_amount" => "required|numeric",

			"item_id" => "required|array|min:1",
			"item_id.*" => "required",
			"qty" => "required|array|min:1",
			"qty.*" => "required|numeric",
			"rate" => "required|array|min:1",
			"rate.*" => "required|numeric",
			"amount" => "required|array|min:1",
			"amount.*" => "required|numeric",
		]);

		DB::beginTransaction();

		try {
			// $walletController = new \App\Http\Controllers\WalletController();
			// if ($request->redeem_coin > 0 && $request->redeem_coin != $quotation->redeem_coin) {
			// $wallet = Wallet::where(['user_id' => $quotation->customer_id, 'type' => 'DEBIT', 'txn_type_id' => 1, 'ref_id' => $quotation->id])->first();
			// if ($wallet) {
			// $walletController->credit($request->customer_id, $quotation->redeem_coin, $quotation->id, 4, 'Quotation Redeem Coin Reward', date('Y-m-d'));
			// }

			// if ($request->customer_id) {
			// $walletController->debit($quotation->customer_id, $request->redeem_coin, $quotation->id, 1, 'Quotation Redeem Coin', date('Y-m-d'));
			// }
			// }

			// if ($request->cash_back_coin > 0 && $request->cash_back_coin != $quotation->cash_back_coin) {
			// $wallet = Wallet::where(['user_id' => $quotation->customer_id, 'type' => 'CREDIT', 'txn_type_id' => 3, 'ref_id' => $quotation->id])->first();
			// if ($wallet) {
			// $walletController->debit($quotation->customer_id, $quotation->cash_back_coin, $quotation->id, 5, 'Quotation Cash Back Coin Reward', date('Y-m-d'));
			// }
			// if ($request->customer_id) {
			// $walletController->credit($request->customer_id, $request->cash_back_coin, $quotation->id, 3, 'Quotation Cash Back Coin', date('Y-m-d'));
			// }
			// }

			$quotation->update([
				"customer_id" => $request->customer_id,
				"po_no" => $request->po_no ?? "",
				"address" => $request->address,
				"discription" => $request->discription,
				"date" => $request->detail_date ?? Carbon::now()->toDateString(),
				"company_name" => $request->company_name,
				"print_type_id" => $request->print_type_id ? $request->print_type_id : null,
				"payment_date" => $request->payment_date,
				"payment_amount" => $request->payment_amount ?? 0,
				"comments" => $request->comments,
				"is_verified" => $request->is_verified ?? 0,
				"discount" => $request->discount ?? 0,
				"total_amount" => $request->total_amount,
				"redeem_coin" => $request->redeem_coin,
				"net_amount" => $request->net_amount,
				"cash_back_coin" => $request->cash_back_coin,
				"block_find" => $request->block_find,
				"delivery_date" => $request->delivery_date,
				"discount_amount" => $request->discount_amount ?? 0,
				"created_by" => auth()->id()
			]);

			$deleteQ = QuotationDetail::where('quotation_id', $quotation->id)->get();

			foreach ($deleteQ as $deleteR) {
				if (!in_array($deleteR['id'], $request->quotation_detail_id)) {
					QuotationDetail::where(['id' => $deleteR['id']])->delete();
				}
			}

			foreach ($request['item_id'] as $index => $item) {
				$report = QuotationDetail::where('quotation_id', $quotation->id)->where('id', $request->quotation_detail_id[$index])->first();
				if ($report) {
					QuotationDetail::where('id', $request->quotation_detail_id[$index])->update([
						'quotation_id' => $quotation->id,
						'item_id' => explode('-', explode(',', $item)[0])[0],
						'print_type_id' => explode('-', explode(',', $item)[0])[1] ?? NULL,
						'item_name' => explode(',', $item)[1],
						'qty' => $request->qty[$index],
						'rate' => $request->rate[$index],
						'block' => $request->block[$index],
						'narration' => $request->narration[$index],
						'remark' => $request->remark[$index],
						'transport_id' => $request->transport_id[$index] ?? NULL,
						'amount' => $request->amount[$index],
						// 'date' => $request->date[$index],
						// 'design' => $request->design[$index],
						'discount' => $request->quotation_discount[$index],
					]);
				} else {
					QuotationDetail::create([
						'quotation_id' => $quotation->id,
						'item_id' => explode('-', explode(',', $item)[0])[0],
						'print_type_id' => explode('-', explode(',', $item)[0])[1],
						'item_name' => explode(',', $item)[1],
						'qty' => $request->qty[$index],
						'rate' => $request->rate[$index],
						'block' => $request->block[$index],
						'narration' => $request->narration[$index],
						'remark' => $request->remark[$index],
						'transport_id' => $request->transport_id[$index],
						'amount' => $request->amount[$index],
						// 'date' => $request->date[$index],
						// 'design' => $request->design[$index],
						'discount' => $request->quotation_discount[$index],
						"created_by" => auth()->id()
					]);
				}
			}

			DB::commit();

			if ($request->ajax()) {
				return $this->withSuccess("Quotation Updated successfully");
			}
			return $this->withSuccess("Quotation Updated successfully")->back();
		} catch (\Exception $e) {
			DB::rollBack();
			throw $e;
		}
	}

	/**
		* Remove the specified resource from storage.
		*/
	public function destroy(Quotation $quotation)
	{
		$quotation->quotationDetail()->delete();
		$quotation->delete();
		if (request()->ajax()) {
			return $this->withSuccess("Quotation delete successfully");
		}
		return $this->withSuccess("Quotation delete successfully")->back();
	}

	public function getList(Request $request)
	{
		$report = $request['group'];
		$url = $request['url'];
		$table = [];
		DB::statement('SET SESSION sql_mode =
        REPLACE(REPLACE(REPLACE(
        @@sql_mode,
        "ONLY_FULL_GROUP_BY,", ""),
        ",ONLY_FULL_GROUP_BY", ""),
        "ONLY_FULL_GROUP_BY", "")');
		switch ($report) {
			case 'item':
				$data = $this->quotation->getSalesGroupByItem($request);
				$url = $url;
				break;
			case 'customer':
				$data = $this->quotation->getSalesGroupByCustomer($request);
				$url = $url;
				break;
			case 'bill':
				$data = $this->quotation->getSalesGroupByBill($request);
				$url = $url;
				break;
			case 'voucher':
				$data = $this->quotation->getSalesGroupByVoucher($request);
				$url = $url;
				break;
			case 'created_user':
				$data = $this->quotation->getSalesGroupByCreated($request);
				$url = $url;
				break;
			case 'print_type':
				$data = $this->quotation->getSalesGroupByPrintType($request);
				$url = $url;
				break;
		}
		return view("quotation.{$report}_ajax", compact('data', 'url'));
	}

	public function getRate(Request $request)
	{
		$rate = 0;
		$discount = 0;
		$customer = Customer::with('partyType')->find($request->customer_id);
		$item_id = explode('-', explode(',', $request->item_id)[0])[0];
		$print_type_id = explode('-', explode(',', $request->item_id)[0])[1];
		$item = ItemDetail::with('item','item.categories')->where('item_id', $item_id)->where('print_type_id', $print_type_id)->first();
		if($customer->price == 'Active'){
			if($customer->partyType->item_price == "Dealer"){
				$rate = $item->item->dealer_old_price;
			}else if($customer->partyType->item_price == "Retailer"){
				$rate = $item->item->retail_old_price;
			}else if($customer->partyType->item_price == "USD"){
				$rate = $item->item->usd_old_price;
			}
		}else{
			if($customer->partyType->item_price == "Dealer"){
				$rate = $item->item->dealer_current_price;
			}else if($customer->partyType->item_price == "Retailer"){
				$rate = $item->item->retail_current_price;
			}else if($customer->partyType->item_price == "USD"){
				$rate = $item->item->usd_current_price;
			}
		}
		if($customer->partyType->extra_price == "USD"){
			$rate = $rate = $rate + (int)ItemGroupPrice::where(['print_type_id' => $print_type_id, 'item_group_id' => $item->item->categories->item_group_id])->first()->usd_extra_price;
		}else if($customer->partyType->extra_price == "INR"){
			$rate = $rate + (int)ItemGroupPrice::where(['print_type_id' => $print_type_id, 'item_group_id' => $item->item->categories->item_group_id])->first()->extra_price;
		}
		$discount = (int)$item->item->extra_dealer_discount;
		return response()->json(['rate' => $rate, 'discount' => $discount]);
	}

	public function getAddress(Request $request)
	{
		if($request->customer_id){
			$customer = Customer::with('city', 'state')->where('id', $request->customer_id)->first();
			$billAddress = $customer->address.' ,  ' . $customer->area . ' ,  ' . $customer->city->name . ' ,  ' . $customer->state->name . ' ,  ' . $customer->pincode;
		}else{
			$billAddress = "";
		}
		return $billAddress;
	}

	public function getItem(Request $request)
	{
		$item = Item::with(['itemDetails' => function ($q) use ($request) {
			$q->where('checkbox', '1');
			if ($request->print_type_id != '0') {
				$q->where('print_type_id', $request->print_type_id);
			}
		}])->get();

		$items = [];
		foreach ($item as $value) {
			foreach ($value->itemDetails as $v) {
				$items[] = [
					'id' => $value->id . ' - ' . $v->printType->id . ',' . $value->name . ' - ' . $v->printType->name . ' - ' . $value->packing,
					'text' => $value->name . ' - ' . $v->printType->name . ' - ' . $value->packing
				];
			}
		}
		return response()->json($items);
	}

	public function gatePassPdf(Request $request)
	{
		if ($request->type == 'get-pass' && $request->id) {
			$quotations = Quotation::with('customer', 'customer.city', 'quotationDetail', 'quotationDetail.transport', 'quotationDetail.item', 'quotationDetail.printType')->whereIn('id', $request->id)->get();
			return view('quotation.estimatePdf', compact('quotations'));
		} else if ($request->type == 'quotation' && $request->id) {
			$quotations = Quotation::with('quotationDetail', 'customer', 'quotationDetail.item')->whereIn('id', $request->id)->get();
			return view('quotation.quotationprint', compact('quotations'));
		} else {
			if (request()->ajax()) {
				return $this->withSuccess("Please Select Estimate");
			}
			return $this->withSuccess("Please Select Estimate")->back();
		}
	}

	public function blockFind(Request $request)
	{
		if ($request->id && $request->item == 'true') {
			Quotation::where("id", $request->id)->update(['block_find' => 'Yes']);
		} else {
			Quotation::where("id", $request->id)->update(['block_find' => 'No']);
		}
		return $this->withSuccess("Block Find Updated successfully");
	}
}
