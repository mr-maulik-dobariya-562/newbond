<?php

namespace App\Http\Controllers\Order;

use App\Helpers\FileUpload;
use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\Customer;
use App\Models\Item;
use App\Models\ItemDetail;
use App\Models\ItemGroupPrice;
use App\Models\ItemGroupPrintExtra;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderPayment;
use App\Models\PartyGroup;
use App\Models\PartyType;
use App\Models\Payment;
use App\Models\PrintType;
use App\Models\PrintTypeExtra;
use App\Models\Transport;
use App\Models\Wallet;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller implements HasMiddleware
{
    use DataTable;
    public static function middleware(): array
    {
        return [
            new Middleware('permission:order-create', only: ['create']),
            new Middleware('permission:order-view', only: ['index', "getList"]),
            new Middleware('permission:order-edit', only: ['edit', "update"]),
            new Middleware('permission:order-delete', only: ['destroy']),
        ];
    }

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
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
        $partyGroups = PartyGroup::all();
        $parentUsers = Customer::with('parent')->where('parent_id', '!=', NUll)->get();
        return view('order.index', compact('customers', 'partyTypes', 'item', 'prints', 'partyGroups', 'parentUsers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $partyTypes = PartyType::all();
        $printTypes = PrintType::all();
        $transport = Transport::all();
        $couriers = Courier::all();
        $item = Item::with('printTypes')->get();
        $printTypeExtras = PrintTypeExtra::all();
        $items = [];
        foreach ($item as $key => $value) {
            foreach ($value->printTypes as $k => $v) {
                $items[] = [$value->name . ' - ' . $v->name . ' - ' . $value->packing, $value->id . ' - ' . $v->id . ',' . $value->name . ' - ' . $v->name];
            }
        }
        return view("Order::create", compact('partyTypes', 'couriers', 'items', "transport", "printTypes", "printTypeExtras"));
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
            "print_type_extra_id" => "nullable|string|max:255",
            "delivery_date" => "required",

            // "payment_date"        => "required",
            // "discount"         => "required|numeric",
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
            $order = Order::create([
                "customer_id" => $request->customer_id,
                "address" => $request->address,
                "discription" => $request->discription,
                "print_type_id" => $request->print_type_id ? $request->print_type_id : null,
                "print_type_extra_id" => $request->print_type_extra_id ?? null,
                "order_code" => 'OR-' . (Order::latest('id')->first()?->id + 1 ?? 1),
                "po_no" => (int) Order::where('customer_id', $request->customer_id)->latest('id')->first()?->po_no + 1 ?? 1,
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
                "last_closing" => Customer::where('id', $request->customer_id)->first()->balance ?? 0,
                "block_find" => $request->block_find,
                "delivery_date" => $request->delivery_date,
                "discount_amount" => $request->discount_amount ?? 0,
                "is_special" => 0,
                'order_type' => 'offline',
                "created_by" => auth()->id()
            ]);

            $lastInsertedId = $order->id;

            foreach ($request['item_id'] as $index => $item) {
                OrderDetail::create([
                    'order_id' => $lastInsertedId,
                    'item_id' => explode('-', explode(',', $item)[0])[0],
                    'print_type_id' => explode('-', explode(',', $item)[0])[1],
                    'item_name' => explode(',', $item)[1],
                    'qty' => $request->qty[$index],
                    'rate' => $request->rate[$index],
                    'block' => $request->block[$index] ?? NULL,
                    'narration' => $request->narration[$index],
                    'remark' => $request->remark[$index],
                    'other_remark' => $request->other_remark[$index],
                    'transport_id' => $request->transport_id[$index] ?? NULL,
                    'amount' => $request->amount[$index],
                    // 'date'       => $request->date[$index],
                    // 'design'     => $request->design[$index],
                    "is_special" => $request->is_special[$index] == 1 ? 1 : 0,
                    "print_type_other_id" => $request->print_type_other_id[$index] ?? null,
                    'discount' => $request->order_discount[$index],
                    "created_by" => auth()->id()
                ]);
            }

            foreach ($request['paymentDate'] as $index => $payment) {
                OrderPayment::create([
                    "order_id" => $lastInsertedId,
                    "date" => $payment,
                    "amount" => $request->paymentAmount[$index],
                    "created_by" => auth()->id()
                ]);
            }

            $walletController = new \App\Http\Controllers\WalletController();
            $customer = Customer::find($request->customer_id);
            if ($request->redeem_coin > 0 && $customer->party_type_id == 2) {
                if ($request->customer_id) {
                    $walletController->debit($order->customer_id, $request->redeem_coin, $lastInsertedId, 1, 'Order Redeem Coin', date('Y-m-d'));
                }
            }

            if ($request->cash_back_coin > 0 && $customer->party_type_id == 2) {
                if ($request->customer_id) {
                    $walletController->credit($request->customer_id, $request->cash_back_coin, $lastInsertedId, 3, 'Order Cash Back Coin', date('Y-m-d'));
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return $this->withSuccess("Order created successfully");
            }
            return $this->withSuccess("order created successfully")->back();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $partyTypes = PartyType::all();
        $printTypes = PrintType::all();
        $transport = Transport::all();
        $couriers = Courier::all();
        $item = Item::with('printTypes')->get();
        $printTypeExtras = PrintTypeExtra::all();
        $items = [];
        foreach ($item as $key => $value) {
            foreach ($value->printTypes as $k => $v) {
                $items[] = [$value->name . ' - ' . $v->name . ' - ' . $value->packing, $value->id . ' - ' . $v->id . ',' . $value->name . ' - ' . $v->name];
            }
        }
        $order->load(['customer', 'customer.city', 'customer.partyType', 'customer.partyGroup', 'customer.partyCategory']);
        $orderDetail = OrderDetail::with('item.itemDetails')->where('order_id', $order->id)->get();
        $url = OrderDetail::select(['id', 'design'])
            ->where('order_id', $order->id)
            ->get()
            ->pluck('design');

        $image = [];
        foreach ($url as $img) {
            $image[] = $img ? FileUpload::url($img, 'order') : '';
        }
        $orderPayments = OrderPayment::where('order_id', $order->id)->get();
        return view("Order::create", compact('order', 'orderDetail', 'items', "transport", "printTypes", 'printTypeExtras', 'orderPayments', 'image'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            "customer_id" => "required|numeric",
            "address" => "required|string",
            "discription" => "nullable|string|max:255",
            "detail_date" => "required",
            "company_name" => "nullable|string|max:255",
            "print_type_id" => "required|numeric",
            "delivery_date" => "required",

            // "payment_date"   => "required",
            // "discount"    => "required|numeric",
            "total_amount" => "required|numeric",
            "redeem_coin" => "required|numeric",
            "cash_back_coin" => "required|numeric",
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
            $walletController = new \App\Http\Controllers\WalletController();
            $customer = Customer::find($request->customer_id);
            if ($request->redeem_coin > 0 && $request->redeem_coin != $order->redeem_coin && $customer->party_type_id == 2) {
                $wallet = Wallet::where(['user_id' => $order->customer_id, 'type' => 'DEBIT', 'txn_type_id' => 1, 'ref_id' => $order->id])->first();
                if ($wallet) {
                    $walletController->credit($request->customer_id, $order->redeem_coin, $order->id, 4, 'Order Redeem Coin Reward', date('Y-m-d'));
                }

                if ($request->customer_id) {
                    $walletController->debit($order->customer_id, $request->redeem_coin, $order->id, 1, 'Order Redeem Coin', date('Y-m-d'));
                }
            }

            if ($request->cash_back_coin > 0 && $request->cash_back_coin != $order->cash_back_coin && $customer->party_type_id == 2) {
                $wallet = Wallet::where(['user_id' => $order->customer_id, 'type' => 'CREDIT', 'txn_type_id' => 3, 'ref_id' => $order->id])->first();
                if ($wallet) {
                    $walletController->debit($order->customer_id, $order->cash_back_coin, $order->id, 5, 'Order Cash Back Coin Reward', date('Y-m-d'));
                }
                if ($request->customer_id) {
                    $walletController->credit($request->customer_id, $request->cash_back_coin, $order->id, 3, 'Order Cash Back Coin', date('Y-m-d'));
                }
            }

            $order->update([
                "customer_id" => $request->customer_id,
                // "po_no"               => $request->po_no ?? "",
                "address" => $request->address,
                "discription" => $request->discription,
                "date" => $request->detail_date ?? Carbon::now()->toDateString(),
                "company_name" => $request->company_name,
                "print_type_id" => $request->print_type_id ? $request->print_type_id : null,
                "print_type_extra_id" => $request->print_type_extra_id ?? null,
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
                "is_special" => 0,
                'updated_by' => auth()->id()
            ]);

            $deleteQ = OrderDetail::where('order_id', $order->id)->get();

            foreach ($deleteQ as $deleteR) {
                if (!in_array($deleteR['id'], $request->order_detail_id)) {
                    OrderDetail::where(['id' => $deleteR['id']])->delete();
                }
            }

            foreach ($request['item_id'] as $index => $item) {
                $report = OrderDetail::where('order_id', $order->id)->where('id', $request->order_detail_id[$index])->first();
                if ($report) {
                    OrderDetail::where('id', $request->order_detail_id[$index])->update([
                        'order_id' => $order->id,
                        'item_id' => explode('-', explode(',', $item)[0])[0],
                        'print_type_id' => explode('-', explode(',', $item)[0])[1] ?? null,
                        'item_name' => explode(',', $item)[1],
                        'qty' => $request->qty[$index],
                        'rate' => $request->rate[$index],
                        'block' => $request->block[$index] ?? NULL,
                        'narration' => $request->narration[$index],
                        'remark' => $request->remark[$index],
                        'other_remark' => $request->other_remark[$index],
                        'transport_id' => $request->transport_id[$index] ?? NULL,
                        'amount' => $request->amount[$index],
                        // 'date'       => $request->date[$index],
                        // 'design'     => $request->design[$index],
                        "is_special" => $request->is_special[$index] == 1 ? 1 : 0,
                        "print_type_other_id" => $request->print_type_other_id[$index] ?? null,
                        'discount' => $request->order_discount[$index],
                        'updated_by' => auth()->id()
                    ]);
                } else {
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'item_id' => explode('-', explode(',', $item)[0])[0],
                        'print_type_id' => explode('-', explode(',', $item)[0])[1],
                        'item_name' => explode(',', $item)[1],
                        'qty' => $request->qty[$index],
                        'rate' => $request->rate[$index],
                        'block' => $request->block[$index] ?? NULL,
                        'narration' => $request->narration[$index],
                        'remark' => $request->remark[$index],
                        'other_remark' => $request->other_remark[$index],
                        'transport_id' => $request->transport_id[$index],
                        'amount' => $request->amount[$index],
                        // 'date'       => $request->date[$index],
                        // 'design'     => $request->design[$index],
                        "is_special" => $request->is_special[$index] == 1 ? 1 : 0,
                        "print_type_other_id" => $request->print_type_other_id[$index] ?? null,
                        'discount' => $request->order_discount[$index],
                        "created_by" => auth()->id()
                    ]);
                }
            }

            foreach ($request['paymentDate'] as $index => $payment) {
                OrderPayment::updateOrCreate([
                    "order_id" => $order->id,
                    "id" => $request->payment_id[$index],
                ], [
                    "date" => $payment,
                    "amount" => $request->paymentAmount[$index],
                    "created_by" => auth()->id()
                ]);
            }

            DB::commit();

            if ($request->ajax()) {
                return $this->withSuccess("Order Updated successfully");
            }
            return $this->withSuccess("Order Updated successfully")->back();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->orderDetail()->delete();
        $order->delete();
        if (request()->ajax()) {
            return $this->withSuccess("Order delete successfully");
        }
        return $this->withSuccess("Order delete successfully")->back();
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
                $data = $this->order->getSalesGroupByItem($request);
                $url = $url;
                break;
            case 'customer':
                $data = $this->order->getSalesGroupByCustomer($request);
                $url = $url;
                break;
            case 'bill':
                $data = $this->order->getSalesGroupByBill($request);
                $url = $url;
                break;
            case 'voucher':
                $data = $this->order->getSalesGroupByVoucher($request);
                $url = $url;
                break;
            case 'created_user':
                $data = $this->order->getSalesGroupByCreated($request);
                $url = $url;
                break;
            case 'print_type':
                $data = $this->order->getSalesGroupByPrintType($request);
                $url = $url;
                break;
            case 'party_group':
                $data = $this->order->getSalesGroupByPartyGroup($request);
                $url = $url;
                break;
            case 'bill_print':
                $data = $this->order->getSalesGroupByBillPrintGroup($request);
                $url = $url;
                break;
        }
        return view("order.{$report}_ajax", compact('data', 'url'));
    }

    public function getRate(Request $request)
    {
        $rate = 0;
        $discount = 0;
        $url = "";
        $customer = Customer::with('partyType')->find($request->customer_id);
        $item_id = explode('-', explode(',', $request->item_id)[0])[0];
        $print_type_id = explode('-', explode(',', $request->item_id)[0])[1];
        $item = ItemDetail::with('item', 'item.categories')->where('item_id', $item_id)->where('print_type_id', $print_type_id)->first();
        $order = Order::where('customer_id', $request->customer_id)->first();
        $url = OrderDetail::select(['id', 'design', 'item_id', 'print_type_id'])
            ->where('item_id', $item_id)
            ->where('print_type_id', $print_type_id)
            ->get()
            ->pluck('design', 'item_id', 'print_type_id');

        $image = [];
        foreach ($url as $img) {
            $image[] = $img ? FileUpload::url($img, 'order') : '';
        }
        if ($customer->price == 'Active') {
            if ($customer->partyType->item_price == "Dealer") {
                $rate = $item->item->dealer_old_price;
            } else if ($customer->partyType->item_price == "Retailer") {
                $rate = $item->item->retail_old_price;
            } else if ($customer->partyType->item_price == "USD") {
                $rate = $item->item->usd_old_price;
            }
        } else {
            if ($customer->partyType->item_price == "Dealer") {
                $rate = $item->item->dealer_current_price;
            } else if ($customer->partyType->item_price == "Retailer") {
                $rate = $item->item->retail_current_price;
            } else if ($customer->partyType->item_price == "USD") {
                $rate = $item->item->usd_current_price;
            }
        }

        if ($customer->partyType->extra_price == "USD") {
            $rate = $rate + (int) ItemGroupPrice::where(['print_type_id' => $print_type_id, 'item_group_id' => $item->item->categories->item_group_id])->first()->usd_extra_price;
        } else if ($customer->partyType->extra_price == "INR") {
            $rate = $rate + (int) ItemGroupPrice::where(['print_type_id' => $print_type_id, 'item_group_id' => $item->item->categories->item_group_id])->first()->extra_price;
        }

        if ($request->printTypeExtra) {
            $printExtra = ItemGroupPrintExtra::where(['print_extra_id' => $request->printTypeExtra, 'item_group_id' => $item->item->categories->item_group_id])->first();
            $rate = $rate + ($printExtra ? $printExtra->amount : 0);
        }

        if ($customer->partyType->name == 'Dealer') {
            $discount = (int) $item->item->extra_dealer_discount;
        } else if ($customer->partyType->name == 'Retail') {
            $discount = (int) $item->item->extra_retail_discount;
        } else {
            $discount = 0;
        }
        return response()->json(['rate' => $rate, 'discount' => $discount, 'image' => $image]);
    }

    public function getAddress(Request $request)
    {
        $customer = Customer::with('city', 'state')->where('id', $request->customer_id)->first();
        $billAddress = $customer->address . ' ,  ' . $customer->area . ' ,  ' . $customer->city?->name . ' ,  ' . $customer->state?->name . ' ,  ' . $customer->pincode . ' ,  ' . ($customer->contact_person ? 'Contact: ' . $customer->contact_person : '') . ' ,  ' . $customer->mobile . ($customer->gst ? ', GST: ' . $customer->gst : '') . ($customer->pan_no ? ', PAN No: ' . $customer->pan_no : '');
        return $billAddress;
    }

    public function getItem(Request $request)
    {
        $item = Item::with([
            'itemDetails' => function ($q) use ($request) {
                $q->where('checkbox', '1');
                if ($request->print_type_id != '0') {
                    $q->where('print_type_id', $request->print_type_id);
                }
            }
        ])
            ->orWhere('name', 'like', '%' . $request->search . '%')
            ->get();

        $items = [];
        $items[] = [
            'id' => '0',
            'text' => 'Select Item'
        ];
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
            $orderIdsWithSameNarration = DB::table('order_details')
                ->select('order_id', 'narration')
                ->whereIn('order_id', $request->id)
                ->where('branch_id', session('branch_id'))
                ->groupBy('order_id', 'narration')
                ->get();
            foreach ($orderIdsWithSameNarration as $narration) {
                $orders[] = Order::with([
                    'printTypeExtra',
                    'customer',
                    'customer.city',
                    'customer.partyGroup',
                    'orderDetail' => function ($query) use ($narration) {
                        $query->where('narration', $narration->narration)
                            ->with([
                                'transport',
                                'printType',
                                'item',
                                'item.itemDetails' => function ($q) {
                                    $q->where('checkbox', '1');
                                }
                            ]);
                    }
                ])
                    ->where('id', $narration->order_id)
                    ->get();

                $orders[0][0]->transport = $orders[0][0]->orderDetail->first(function ($order) {
                    return $order->transport != null;
                })->transport->name ?? null;
            }
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML(view('order.gatePass', compact('orders'))->render())->setPaper('a5', 'portrait');
            return $pdf->stream();
            // return view('order.gatePass', compact('orders'));
        } else if ($request->type == 'order' && $request->id) {
            // $orders = Order::with('orderDetail', 'customer', 'orderDetail.item')->whereIn('id', $request->id)->get();
            $orderIdsWithSameNarration = DB::table('order_details')
                ->select('order_id', 'narration')
                ->whereIn('order_id', $request->id)
                ->where('order_details.branch_id', session('branch_id'))
                ->groupBy('order_id', 'narration')
                ->get();
            foreach ($orderIdsWithSameNarration as $narration) {
                $orders[] = Order::with([
                    'printTypeExtra',
                    'customer',
                    'customer.city',
                    'customer.partyGroup',
                    'orderDetail' => function ($query) use ($narration) {
                        $query->where('narration', $narration->narration)
                            ->with([
                                'transport',
                                'printType',
                                'item',
                                'item.itemDetails' => function ($q) {
                                    $q->where('checkbox', '1');
                                }
                            ]);
                    }
                ])
                    ->where('id', $narration->order_id)
                    ->get();

                $orders[0][0]->transport = $orders[0][0]->orderDetail->first(function ($order) {
                    return $order->transport != null;
                })->transport->name ?? null;
                $orders[0][0]->narration = $orders[0][0]->orderDetail->first(function ($order) {
                    return $order->transport != null;
                })->narration ?? null;
            }
            return view('order.orderprint', compact('orders'));
        } else if ($request->type == 'quotation') {

            $orders = Order::with(['printTypeExtra', 'customer'])
                ->where('id', $request->id)
                ->get()
                ->map(function ($order) {
                    $orderDetails = DB::table('order_details')
                        ->select(
                            'item_id',
                            'print_type_id',
                            'qty as total_qty',
                            'rate as total_rate',
                            'amount as total_amount',
                            'items.name as item_name',
                            'print_type.name as print_type_name',
                            'order_details.discount',
                            'order_details.narration',
                            'order_details.remark',
                        )
                        ->leftJoin('items', 'order_details.item_id', '=', 'items.id')
                        ->leftJoin('print_type', 'order_details.print_type_id', '=', 'print_type.id')
                        ->where('order_id', $order->id)
                        ->where('order_details.branch_id', session('branch_id'))
                        ->get()->toArray();

                    $order->orderDetails = $orderDetails;
                    return $order;
                });

            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML(view('order.quotation_print', compact('orders'))->render())->setPaper('a4', 'portrait');
            return $pdf->stream();
            // return view('order.quotation_print', compact('orders'));

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
            Order::where("id", $request->id)->update(['block_find' => 'Yes']);
        } else {
            Order::where("id", $request->id)->update(['block_find' => 'No']);
        }
        return $this->withSuccess("Block Find Updated successfully");
    }

    public function view(Order $order)
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
        $order->load(['customer', 'customer.city', 'customer.partyType']);
        $orderDetail = OrderDetail::with('item.itemDetails')->where('order_id', $order->id)->get();
        return view("Order::view", compact('order', 'orderDetail', 'items', "transport", "printTypes"));
    }

    public function cancelledOrder(Order $order)
    {
        $orderDetail = OrderDetail::where('order_id', $order->id)->get();
        foreach ($orderDetail as $value) {
            $value->update(['cancel_qty' => $value->qty - $value->dispatch_qty]);
        }

        $walletController = new \App\Http\Controllers\WalletController();
        $customer = Customer::find($order->customer_id);
        if ($order->redeem_coin > 0 && $customer->party_type_id == 2) {
            $wallet = Wallet::where(['user_id' => $order->customer_id, 'type' => 'DEBIT', 'txn_type_id' => 1, 'ref_id' => $order->id])->first();
            if ($wallet) {
                $walletController->credit($order->customer_id, $order->redeem_coin, $order->id, 4, 'Order Redeem Coin Reward', date('Y-m-d'));
            }
        }

        if ($order->cash_back_coin > 0 && $customer->party_type_id == 2) {
            $wallet = Wallet::where(['user_id' => $order->customer_id, 'type' => 'CREDIT', 'txn_type_id' => 3, 'ref_id' => $order->id])->first();
            if ($wallet) {
                $walletController->debit($order->customer_id, $order->cash_back_coin, $order->id, 5, 'Order Cash Back Coin Reward', date('Y-m-d'));
            }
        }

        return $this->withSuccess("Order Cancel Successfully")->back();
    }

    public function restoreQtyOrder(Order $order)
    {
        $orderDetail = OrderDetail::where('order_id', $order->id)->get();
        foreach ($orderDetail as $value) {
            $value->update(['cancel_qty' => 0]);
        }
        return $this->withSuccess("Order Re Store successfully")->back();
    }

    public function changeStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:Pending,Confirmed,Cancelled',
            'id' => 'required|exists:orders,id',
        ]);

        $order = Order::find($request->id);
        $order->status = $request->status;
        $order->save();

        if (request()->ajax()) {
            return $this->withSuccess("Order delete successfully");
        }
        return $this->withSuccess("Order delete successfully")->back();
    }
}
