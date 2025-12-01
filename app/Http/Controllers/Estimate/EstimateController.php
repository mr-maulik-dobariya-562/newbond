<?php

namespace App\Http\Controllers\Estimate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
    BillGroup,
    Courier,
    Customer,
    Estimate,
    EstimateDetail,
    InvoiceType,
    Item,
    ItemDetail,
    Order,
    OrderDetail,
    PartyCategory,
    PartyGroup,
    PartyType,
    PrintType,
    Transport,
    Wallet
};
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Exports\EstimateExport;
use App\Exports\RegisterExport;
use App\Exports\SummaryExport;
use App\Helpers\FileUpload;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Facades\Excel;

class EstimateController extends Controller implements HasMiddleware
{
    protected $estimate;

    public function __construct(Estimate $estimate)
    {
        $this->estimate = $estimate;
        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:estimate-create', only: ['create', 'store', 'getRate', 'getOrder']),
            new Middleware('permission:estimate-view', only: ['index', "getList"]),
            new Middleware('permission:estimate-edit', only: ['edit', "update", 'getRate', 'getOrder']),
            new Middleware('permission:estimate-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::get();
        $partyTypes = PartyType::all();
        $item = Item::with('printTypes')->get();
        $prints = PrintType::all();
        $partyGroups = PartyGroup::all();
        $invoices = InvoiceType::all();
        $parentUsers = Customer::with('parent')->where('parent_id', '!=', NUll)->get();
        $transports = Transport::all();
        return view("Estimate::index", compact('customers', 'partyTypes', 'item', 'prints', 'partyGroups', 'invoices', 'transports', 'parentUsers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $partyTypes = PartyType::all();
        $transport = Transport::all();
        $billGroups = BillGroup::all();
        $couriers = Courier::all();
        $invoices = InvoiceType::all();
        $partyCategorys = PartyCategory::all();
        $item = Item::with('itemDetails.printType', 'itemDetails')->get();
        $items = [];
        foreach ($item as $value) {
            foreach ($value->itemDetails as $v) {
                $items[] = [
                    $value->name . ' - ' . $v->printType->name . ' - ' . $value->packing,
                    $value->id . ' - ' . $v->printType->id . ',' . $value->name . ' - ' . $v->printType->name . ' - ' . $value->packing,
                ];
            }
        }
        return view("Estimate::create", compact('partyTypes', 'couriers', 'items', "transport", "billGroups", "invoices", "partyCategorys"));
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

            "discount" => "required|numeric",
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
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $image = FileUpload::upload($file, 'lrPhoto', app("storage"));
            } else {
                $image = null;
            }
            $estimate = Estimate::create([
                "customer_id" => $request->customer_id,
                "address" => $request->address,
                "estimate_code" => 'E - ' . (Estimate::latest('id')->first()?->id + 1 ?? 1),
                "discription" => $request->discription,
                "note" => $request->note,
                "bill_type" => $request->bill_type ?? null,
                "courier_id" => $request->courier_id,
                "invoice_id" => $request->invoice_id,
                "transport_id" => $request->transport_id,
                "lr_no" => $request->lr_no,
                "lr_date" => $request->lr_date,
                "parcel" => $request->parcel,
                "note" => $request->note,
                "docket" => $request->docket,
                "po_no" => (int) Estimate::where('customer_id', $request->customer_id)->latest('id')->first()?->po_no + 1 ?? 1,
                "date" => $request->detail_date ?? Carbon::now()->toDateString(),
                "company_name" => $request->company_name,
                "payment_date" => NULL,
                "payment_amount" => 0,
                "comments" => $request->comments,
                "is_verified" => 0,
                "discount" => $request->discount,
                "discount_amount" => $request->discount_amount,
                "total_amount" => round($request->total_amount),
                "redeem_coin" => $request->redeem_coin,
                "last_closing" => Customer::where('id', $request->customer_id)->first()->balance ?? 0,
                "net_amount" => round($request->net_amount),
                "other_charge" => round($request->other_charge),
                "cash_back_coin" => $request->cash_back_coin,
                "offer_discount" => $request->offer_discount,
                "lr_photo" => $image ?? null,
                "is_special" => 0,
                "estimate_type" => "offline",
                "created_by" => auth()->id()
            ]);

            $lastInsertedId = $estimate->id;
            foreach ($request['item_id'] as $index => $item) {
                EstimateDetail::create([
                    'estimate_id' => $lastInsertedId,
                    'item_id' => explode('-', explode(',', $item)[0])[0],
                    'print_type_id' => explode('-', explode(',', $item)[0])[1],
                    'item_name' => explode(',', $item)[1],
                    'qty' => $request->qty[$index],
                    'rate' => $request->rate[$index],
                    'block' => $request->block[$index] ?? NULL,
                    'narration' => $request->narration[$index],
                    'remark' => $request->remark[$index],
                    'other_remark' => $request->other_remark[$index],
                    'transport_id' => $request->transport_id[$index] ?? null,
                    'amount' => round($request->amount[$index]),
                    'date' => $request->date[$index],
                    'design' => $request->design[$index] ?? NULL,
                    'discount' => $request->order_discount[$index],
                    'parcel' => $request->item_parcel[$index],
                    'print_type_other_id' => $request->print_type_other_id[$index] ?? NULL,
                    'order_id' => $request->order_id[$index],
                    "is_special" => $request->is_special[$index] == 1 ? 1 : 0,
                    "created_by" => auth()->id()
                ]);

                OrderDetail::where('id', $request->order_id[$index])->update([
                    "dispatch_qty" => $request->qty[$index] + OrderDetail::where('id', $request->order_id[$index])->first()->dispatch_qty
                ]);
            }
            // $walletController = new \App\Http\Controllers\WalletController();
            // $customer = Customer::find($request->customer_id);
            // if ($request->cash_back_coin > 0 && $customer->party_type_id == 2) {
            //     if ($request->customer_id) {
            //         $walletController->credit($request->customer_id, $request->cash_back_coin, $lastInsertedId, 3, 'Order Cash Back Coin', date('Y-m-d'));
            //     }
            // }
            DB::commit();

            if ($request->ajax()) {
                return $this->withSuccess("Estimate created successfully");
            }

            return $this->withSuccess("Estimate created successfully")->back();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Estimate $estimate)
    {
        $partyTypes = PartyType::all();
        $transport = Transport::all();
        $couriers = Courier::all();
        $billGroups = BillGroup::all();
        $couriers = Courier::all();
        $invoices = InvoiceType::all();
        $partyCategorys = PartyCategory::all();
        $item = Item::with('itemDetails.printType', 'itemDetails')->get();
        $items = [];
        foreach ($item as $value) {
            foreach ($value->itemDetails as $v) {
                $items[] = [
                    $value->name . ' - ' . $v->printType->name . ' - ' . $value->packing,
                    $value->id . ' - ' . $v->printType->id . ',' . $value->name . ' - ' . $v->printType->name . ' - ' . $value->packing,
                ];
            }
        }
        $estimateDetail = EstimateDetail::where('estimate_id', $estimate->id)->get();
        foreach ($estimateDetail as $key => $value) {
            $value->item_id = [$value->item_id . ' - ' . $value->print_type_id . ',' . $value->item_name];
        }
        $estimate->load(['customer', 'customer.partyType', 'customer.partyGroup', 'customer.partyCategory']);
        return view("Estimate::create", compact('estimate', 'estimateDetail', 'items', "transport", "billGroups", 'couriers', 'invoices', 'partyCategorys'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Estimate $estimate)
    {
        $request->validate([
            "customer_id" => "required|numeric",
            "address" => "required|string",
            "discription" => "nullable|string|max:255",
            "detail_date" => "required",
            "company_name" => "nullable|string|max:255",

            "discount" => "required|numeric",
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

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $image = FileUpload::upload($file, 'lrPhoto', app("storage"));
            } else {
                $image = '';
            }

            // $walletController = new \App\Http\Controllers\WalletController();
            // $customer = Customer::find($request->customer_id);
            // if ($request->cash_back_coin > 0 && $request->cash_back_coin != $estimate->cash_back_coin && $customer->party_type_id == 2) {
            //     $wallet = Wallet::where(['user_id' => $estimate->customer_id, 'type' => 'CREDIT', 'txn_type_id' => 3, 'ref_id' => $estimate->id])->orderBy('id', 'desc')->first();
            //     if ($wallet) {
            //         $walletController->debit($estimate->customer_id, $wallet->amount, $estimate->id, 5, 'Order Cash Back Coin Reward', date('Y-m-d'));
            //     }
            //     if ($request->customer_id) {
            //         $walletController->credit($request->customer_id, $request->cash_back_coin, $estimate->id, 3, 'Order Cash Back Coin', date('Y-m-d'));
            //     }
            // }
            $estimate->update([
                "customer_id" => $request->customer_id,
                "address" => $request->address,
                "discription" => $request->discription,
                "note" => $request->note,
                "bill_type" => $request->bill_type ?? NULL,
                "courier_id" => $request->courier_id,
                "invoice_id" => $request->invoice_id,
                "transport_id" => $request->transport_id,
                "lr_no" => $request->lr_no,
                "lr_date" => $request->lr_date,
                "parcel" => $request->parcel,
                "note" => $request->note,
                "docket" => $request->docket,
                // "po_no"           => $request->po_no,
                "date" => $request->detail_date ?? Carbon::now()->toDateString(),
                "company_name" => $request->company_name,
                "payment_date" => NULL,
                "payment_amount" => 0,
                "comments" => $request->comments,
                "is_verified" => 0,
                "discount" => $request->discount,
                "discount_amount" => $request->discount_amount,
                "total_amount" => round($request->total_amount, 2),
                "redeem_coin" => $request->redeem_coin,
                "net_amount" => round($request->net_amount, 2),
                "other_charge" => round($request->other_charge),
                "cash_back_coin" => $request->cash_back_coin,
                "offer_discount" => $request->offer_discount,
                'lr_photo' => $image ?? NULL,
                "is_special" => 0,
                'updated_by' => auth()->id()
            ]);

            $deleteQ = EstimateDetail::where('estimate_id', $estimate->id)->get();

            foreach ($deleteQ as $deleteR) {
                if (!in_array($deleteR['id'], $request['estimate_id'])) {
                    $report = EstimateDetail::where(['id' => $deleteR['id'], 'estimate_id' => $estimate->id])->first();
                    if ($report) {
                        $orderDetail = OrderDetail::where('id', $report->order_id)->first();
                        $qty = $orderDetail->dispatch_qty - $report->qty;
                        $orderDetail->update([
                            "dispatch_qty" => $qty
                        ]);
                        $report->delete();
                    }
                }
            }

            foreach ($request['item_id'] as $index => $item) {
                $report = EstimateDetail::where('estimate_id', $estimate->id)->where('id', $request->estimate_id[$index])->first();
                if ($report) {
                    $orderDetail = OrderDetail::where('id', $request->order_id[$index])->first();
                    $qty = $orderDetail->dispatch_qty - $report->qty;
                    $orderDetail->update([
                        "dispatch_qty" => $qty
                    ]);
                    EstimateDetail::where('id', $request->estimate_id[$index])->update([
                        'estimate_id' => $estimate->id,
                        'item_id' => explode('-', explode(',', $item)[0])[0],
                        'print_type_id' => explode('-', explode(',', $item)[0])[1],
                        'item_name' => explode(',', $item)[1],
                        'qty' => $request->qty[$index],
                        'rate' => $request->rate[$index],
                        'block' => $request->block[$index] ?? NULL,
                        'narration' => $request->narration[$index],
                        'remark' => $request->remark[$index],
                        'other_remark' => $request->other_remark[$index],
                        'transport_id' => $request->transport_ids[$index] ?? NULL,
                        'amount' => round($request->amount[$index]),
                        'date' => $request->date[$index],
                        'design' => $request->design[$index] ?? NULL,
                        'discount' => $request->order_discount[$index],
                        'parcel' => $request->item_parcel[$index],
                        'print_type_other_id' => $request->print_type_other_id[$index] ?? NULL,
                        'order_id' => $request->order_id[$index],
                        "is_special" => $request->is_special[$index] == 1 ? 1 : 0,
                        'updated_by' => auth()->id()
                    ]);
                    OrderDetail::where('id', $request->order_id[$index])->update([
                        "dispatch_qty" => $request->qty[$index] + OrderDetail::where('id', $request->order_id[$index])->first()->dispatch_qty
                    ]);
                } else {
                    EstimateDetail::create([
                        'estimate_id' => $estimate->id,
                        'item_id' => explode('-', explode(',', $item)[0])[0],
                        'print_type_id' => explode('-', explode(',', $item)[0])[1],
                        'item_name' => explode(',', $item)[1],
                        'qty' => $request->qty[$index],
                        'rate' => round($request->rate[$index]),
                        'block' => $request->block[$index] ?? NULL,
                        'narration' => $request->narration[$index],
                        'remark' => $request->remark[$index],
                        'other_remark' => $request->other_remark[$index],
                        'transport_id' => $request->transport_ids[$index] ?? NULL,
                        'amount' => round($request->amount[$index]),
                        'date' => $request->date[$index],
                        'design' => $request->design[$index] ?? NULL,
                        'discount' => $request->order_discount[$index],
                        'parcel' => $request->item_parcel[$index],
                        'print_type_other_id' => $request->print_type_other_id[$index] ?? NULL,
                        'order_id' => $request->order_id[$index],
                        "is_special" => $request->is_special[$index] == 1 ? 1 : 0,
                        "created_by" => auth()->id()
                    ]);
                    OrderDetail::where('id', $request->order_id[$index])->update([
                        "dispatch_qty" => $request->qty[$index] + OrderDetail::where('id', $request->order_id[$index])->first()->dispatch_qty
                    ]);
                }
            }
            DB::commit();

            if ($request->ajax()) {
                return $this->withSuccess("Estimate Updated successfully");
            }
            return $this->withSuccess("Estimate Updated successfully")->back();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Estimate $estimate)
    {
        $estimate->estimateDetail()->delete();
        $estimate->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Estimate delete successfully");
        }
        return $this->withSuccess("Estimate delete successfully")->back();
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
                $data = $this->estimate->getSalesGroupByItem($request);
                $url = $url;
                break;
            case 'customer':
                $data = $this->estimate->getSalesGroupByCustomer($request);
                $url = $url;
                break;
            case 'bill':
                $data = $this->estimate->getSalesGroupByBill($request);
                $url = $url;
                break;
            case 'voucher':
                $data = $this->estimate->getSalesGroupByVoucher($request);
                $url = $url;
                break;
            case 'created_user':
                $data = $this->estimate->getSalesGroupByCreated($request);
                $url = $url;
                break;
            case 'print_type':
                $data = $this->estimate->getSalesGroupByPrintType($request);
                $url = $url;
                break;
            case 'party_group':
                $data = $this->estimate->getSalesGroupByPartyGroup($request);
                $url = $url;
                break;
        }
        return view("estimate.{$report}_ajax", compact('data', 'url'));
    }

    public function getRate(Request $request)
    {
        $rate = 0;
        $custmomer = Customer::with('partyType')->find($request->customer_id);
        $item_id = explode('-', explode(',', $request->item_id)[0])[0];
        $print_type_id = explode('-', explode(',', $request->item_id)[0])[1];
        $item = ItemDetail::with('item')->where('item_id', $item_id)->where('print_type_id', $print_type_id)->first();
        if ($custmomer->partyType->name == "Dealer") {
            $rate = $item->item->dealer_old_price > 0 ? $item->item->dealer_old_price : $item->item->dealer_current_price;
            $discount = $item->item->extra_dealer_discount;
        } else {
            $rate = $item->item->dealer_old_price > 0 ? $item->item->dealer_old_price : $item->item->dealer_current_price;
            $discount = $item->item->extra_dealer_discount;
        }
        return response()->json(['rate' => $rate, 'discount' => $discount]);
    }

    public function getOrder(Request $request)
    {
        $custmomerId = $request->customer_id;
        $orderHtml = "";
        $customer = Customer::with('city', 'state', 'country')->find($custmomerId);
        $orderCodes = OrderDetail::whereColumn('qty', '>', 'dispatch_qty')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->where('customer_id', $custmomerId)
            ->groupBy('order_code')
            ->pluck('orders.order_code');
        if ($request->type == 'true') {
            $orderId = Order::where('customer_id', $custmomerId)->pluck('id');
            $data = OrderDetail::with('item', 'transport', 'order', 'printTypeOther')->whereIn('order_id', $orderId)->get();
            foreach ($data as $value) {
                $print = PrintType::find($value->print_type_id);
                $item = $value->item?->name . ' - ' . $print?->name . ' - ' . $value->item?->packing ?? '';
                $check = EstimateDetail::where('order_id', $value->id)->where('item_id', $value->item_id)->first();
                $checked = $check ? 'checked' : '';
                $color = $value->is_special == 1 ? "style='color:red;'" : '';
                if (($value->qty - $value->dispatch_qty) > 0 && $check == null && $value->cancel_qty == 0) {
                    $orderHtml .= "<tr>
                        <td " . $color . " class='orderCode' data-is_special='" . $value->is_special . "'>" . $value->order->order_code . "</td>
                        <td><input type='checkbox' data-InvRemark='" . $value->order->discription . "' class='orderId' " . $checked . " value='" . $value->id . "'></td>
                        <td " . $color . " class='orderDate'>" . $value->order->date . "</td>
                        <td " . $color . " class='itemOrder' data-itemId='" . $item . "'>" . $value->item->name . ' - ' . $print->name . "</td>
                        <td " . $color . " class='printTypeOther' data-printOtherId='" . $value?->printTypeOther?->id . "'>" . $value?->printTypeOther?->code . "</td>


                        <td " . $color . " class='pendingqty'>" . $value->qty - $value->dispatch_qty . "</td>
                        <td " . $color . " class='rateOrder'>" . ($value->rate) . "</td>
                        <td " . $color . " class='narrationOrder'>" . $value->narration . "</td>

                        <td " . $color . " class='remarkOrder'>" . $value->remark . "</td>
                        <td " . $color . " class='remarkOtherOrder'>" . $value->other_remark . "</td>
                        <td " . $color . " class='transportOrder' data-transportId='" . $value->transport_id . "'>" . $value->transport?->name . "</td>
                        <td " . $color . " class='amountOrder'>" . round($value->amount) . "</td>
                        <td " . $color . " class='designOrder'>" . $value->design . "</td>
                        <td " . $color . " class='discountOrder'>" . $value->discount . "</td>
                        <td " . $color . " class='blockOrder'>" . $value->block . "</td>
                        <td " . $color . " class='qtyOrder'>" . $value->qty . "</td>
                         <td " . $color . " class='dispatchqtyOrder'>" . $value->dispatch_qty . "</td>
                    </tr>";
                }
            }
        } else {
            $orderId = Order::where('customer_id', $custmomerId)->pluck('id');
            $data = OrderDetail::with('item', 'transport', 'order', 'printTypeOther')->whereIn('order_id', $orderId)->get();
            foreach ($data as $value) {
                $print = PrintType::find($value->print_type_id);
                $item = $value->item?->name . ' - ' . $print?->name . ' - ' . $value->item?->packing ?? '';
                $color = $value->is_special == 1 ? "style='color:red;'" : '';
                if (($value->qty - $value->dispatch_qty) > 0 && $value->cancel_qty == 0) {
                    $orderHtml .= "<tr>
                        <td " . $color . " class='orderCode' data-is_special='" . $value->is_special . "'>" . $value->order->order_code . "</td>
                        <td><input type='checkbox' data-InvRemark='" . $value->order->discription . "' class='orderId' value='" . $value->id . "'></td>
                        <td " . $color . " class='orderDate'>" . $value->order->date . "</td>
                        <td " . $color . " class='itemOrder' data-itemId='" . $item . "'>" . $value->item->name . ' - ' . $print->name . "</td>
                        <td " . $color . " class='printTypeOther' data-printOtherId='" . $value?->printTypeOther?->id . "'>" . $value?->printTypeOther?->code . "</td>


                        <td " . $color . " class='pendingqty'>" . $value->qty - $value->dispatch_qty . "</td>
                        <td " . $color . " class='rateOrder'>" . ($value->rate) . "</td>
                        <td " . $color . " class='narrationOrder'>" . $value->narration . "</td>

                        <td " . $color . " class='remarkOrder'>" . $value->remark . "</td>
                        <td " . $color . " class='remarkOtherOrder'>" . $value->other_remark . "</td>
                        <td " . $color . " class='transportOrder' data-transportId='" . $value->transport_id . "'>" . $value->transport?->name . "</td>
                        <td " . $color . " class='amountOrder'>" . round($value->amount) . "</td>
                        <td " . $color . " class='designOrder'>" . $value->design . "</td>
                        <td " . $color . " class='discountOrder'>" . $value->discount . "</td>
                        <td " . $color . " class='blockOrder'>" . $value->block . "</td>
                        <td " . $color . " class='qtyOrder'>" . $value->qty . "</td>
                        <td " . $color . " class='dispatchqtyOrder'>" . $value->dispatch_qty . "</td>
                    </tr>";
                }
            }
        }
        return response()->json(['orderHtml' => $orderHtml, 'customer' => $customer, 'orderCodes' => $orderCodes]);
    }

    public function getEstimatePdf(Request $request)
    {
        if ($request->id) {
            if ($request->type == 'estimate') {
                $estimate = Estimate::with('customer', 'customer.partyType', 'transport', 'customer.city', 'estimateDetail', 'estimateDetail.transport', 'estimateDetail.item', 'estimateDetail.printType')->whereIn('id', $request->id)->get();


                $pdf = App::make('dompdf.wrapper');
                $pdf->loadHTML(view('estimate.estimatePdf', compact('estimate'))->render())->setPaper('a5', 'portrait');
                return $pdf->stream();

                // return view('estimate.estimatePdf', compact('estimate'));

            } else if ($request->type == 'estimate-register') {
                $estimate = Estimate::whereIn('id', $request->id)
                    ->with(['estimateDetail:id,estimate_id', 'customer'])
                    ->groupBy('customer_id')
                    ->get();
                foreach ($estimate as $key => $value) {
                    $estimateId = Estimate::whereIn('id', $request->id)->where('customer_id', $value->customer_id)->pluck('id');
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
                return view('estimate.registerPdf', compact('estimate'));
            } else if ($request->type == 'estimate-excel') {
                $estimates = Estimate::whereIn('id', $request->id)->get();
                $codes = str_replace(' ', '', $estimates->pluck('estimate_code')->implode('-'));
                return Excel::download(new EstimateExport($request->id), "estimate-" . $codes . "-Date-" . date('d-m-Y') . ".xlsx");
            } else if ($request->type == 'summary-pdf') {
                return $this->getSummaryPdf($request->id, $request->status);
            } else if ($request->type == 'summary-excel') {
                return Excel::download(new SummaryExport($request->id, $request->status), "summary-" . date('d-m-Y') . ".xlsx");
            } else if ($request->type == 'register-excel') {
                return Excel::download(new RegisterExport($request->id), "register-" . date('d-m-Y') . ".xlsx");
            } else if ($request->type == 'cover-print') {
                $customerId = Estimate::whereIn('id', $request->id)->pluck('customer_id');
                $customer = Customer::with('city')->whereIn('id', $customerId)->get();
                return view('User::customer.cover_print', compact('customer'));
            }
        } else {
            if (request()->ajax()) {
                return $this->withSuccess("Please Select Estimate");
            }
            return $this->withSuccess("Please Select Estimate")->back();
        }
    }

    public function getSummaryPdf($id = null, $status = null)
    {
        $estimate = EstimateDetail::select(DB::raw('SUM(amount) as amount'), DB::raw('SUM(qty) as qty'), 'item_id', 'print_type_id')
            ->with('item', 'item.categories.itemGroup', 'printType')
            ->whereIn('estimate_id', $id)
            ->groupBy('item_id', 'print_type_id')
            ->get();

        $mergedProducts = [];
        foreach ($estimate as $value) {
            $itemId = $value->item->id;
            $printTypeName = $value->printType->name;
            $groupName = $value->item->categories->itemGroup->group_name;

            if (!array_key_exists($groupName, $mergedProducts)) {
                $mergedProducts[$groupName] = [];
            }

            if (!array_key_exists($itemId, $mergedProducts[$groupName])) {
                $mergedProducts[$groupName][$itemId] = [
                    'item_name' => $value->item->name,
                    'item_id' => $itemId,
                ];
            }

            $mergedProducts[$groupName][$itemId][$printTypeName]['amount'] = $value->amount;
            $mergedProducts[$groupName][$itemId][$printTypeName]['qty'] = $value->qty;
        }

        $data = $mergedProducts;
        return view('estimate.summary', compact('data', 'status'));
    }

    public function billGenerated(Request $request)
    {
        if ($request->id && $request->item == 'true') {
            Estimate::where("id", $request->id)->update(['bill_generated' => 'Yes']);
        } else if ($request->id && $request->item == 'false') {
            Estimate::where("id", $request->id)->update(['bill_generated' => 'No']);
        } else {
            return $this->withSuccess("Bill Generated Updated Failed");
        }
        return $this->withSuccess("Bill Generated Updated successfully");
    }

    public function view(Estimate $estimate)
    {
        $partyTypes = PartyType::all();
        $transport = Transport::all();
        $couriers = Courier::all();
        $billGroups = BillGroup::all();
        $couriers = Courier::all();
        $invoices = InvoiceType::all();
        $partyCategorys = PartyCategory::all();
        $item = Item::with('itemDetails.printType', 'itemDetails')->get();
        $items = [];
        foreach ($item as $value) {
            foreach ($value->itemDetails as $v) {
                $items[] = [
                    $value->name . ' - ' . $v->printType->name . ' - ' . $value->packing,
                    $value->id . ' - ' . $v->printType->id . ',' . $value->name . ' - ' . $v->printType->name . ' - ' . $value->packing,
                ];
            }
        }
        $estimateDetail = EstimateDetail::where('estimate_id', $estimate->id)->get();
        foreach ($estimateDetail as $key => $value) {
            $value->item_id = [$value->item_id . ' - ' . $value->print_type_id . ',' . $value->item_name];
        }
        $estimate->load(['customer']);
        return view("Estimate::view", compact('estimate', 'estimateDetail', 'items', "transport", "billGroups", 'couriers', 'invoices', 'partyTypes', 'partyCategorys'));
    }
}
