<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Customer;
use App\Models\PrintType;
use DOMDocument;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'xmlContent' => 'required|string',
            'image_name.*' => 'file|mimes:jpeg,png,jpg|max:2048', // Adjust as needed
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Process XML content
        $xmlContent = ['Message' => 'Order Inserted successfully'];
        $httpRawPostData = $request->input('xmlContent');
        // Log::info('XML Content:', $request->all());
        $httpRawPostData = str_replace(["&", "@", "%"], "", $httpRawPostData);

        $doc = new \DOMDocument();
        $doc->loadXML($httpRawPostData);

        // $elements = $doc->getElementsByTagName('data');
        $userCode = $doc->getElementsByTagName('usercode')->item(0)->nodeValue;
        $userid = $doc->getElementsByTagName('userid')->item(0)->nodeValue;
        // $dataVersion = $doc->getElementsByTagName('version')->item(0)->nodeValue;
        $coinRedeem = $doc->getElementsByTagName('redeem')->item(0)->nodeValue;
        $coinCashback = $doc->getElementsByTagName('cashback')->item(0)->nodeValue;
        $offerDiscount = $doc->getElementsByTagName('offer')->item(0)->nodeValue;
        $schemeID = $doc->getElementsByTagName('sid')->item(0)->nodeValue;
        // $deviceType = $doc->getElementsByTagName('DType')->item(0)->nodeValue ?? 1;
        $tempUserId = $doc->getElementsByTagName('userid')->item(0)->nodeValue;
        $orderDiscount = $doc->getElementsByTagName('disc')->item(0)->nodeValue;
        $productCount = $doc->getElementsByTagName('product')->length;

        $isUploaded = true;
        if ($request->hasFile('image_name')) {
            foreach ($request->file('image_name') as $imageFile) {
                $folderName = public_path('images/order');
                if (!is_dir($folderName)) {
                    mkdir($folderName, 0777, true);
                }
                $newFilename = $folderName . DIRECTORY_SEPARATOR . $imageFile->getClientOriginalName();
                $isUploaded = $imageFile->move($folderName, $newFilename);
                if (!$isUploaded) {
                    break;
                }
            }
        } else {
            $xmlContent['note'] = 'No files';
        }

        if (!$isUploaded) {
            $xmlContent['code'] = 0;
            $xmlContent['note'] = 'File upload failed';
            return response()->json($xmlContent, 200, [], JSON_UNESCAPED_UNICODE);
        }
        try {
            $customer = Customer::with('city', 'state')->findorFail($userid);
            $billAddress = $customer?->address . ' ,  ' . $customer?->area . ' ,  ' . $customer?->city?->name . ' ,  ' . $customer?->state?->name . ' ,  ' . $customer?->pincode . ' ,  ' . ($customer?->contact_person ? 'Contact: ' . $customer?->contact_person : '') . ' ,  ' . $customer?->mobile . ($customer?->gst ? ', GST: ' . $customer?->gst : '') . ($customer?->pan_no ? ', PAN No: ' . $customer?->pan_no : '');

            $order = new Order();
            $order->created_by      = 11;
            $order->redeem_coin     = $coinRedeem;
            $order->cash_back_coin  = $coinCashback;
            $order->order_code      = 'OR-' . (Order::latest('id')->first()?->id + 1 ?? 1);
            $order->po_no           = (int) Order::where('customer_id', $userid)->latest('id')->first()?->po_no + 1 ?? 1;
            $order->customer_id     = $tempUserId;
            $order->branch_id       = 1;
            $order->print_type_id   = 1;
            $order->address         = $billAddress;
            $order->discount        = $orderDiscount;
            $order->date            = Carbon::now()->toDateString();
            $order->delivery_date   = Carbon::now()->toDateString();
            $order->discount_amount = round($offerDiscount);
            $order->is_special      = 0;
            $order->order_type      = 'online';
            $order->save();

            $subTotalAmount = 0;

            for ($i = 0; $i < $productCount; $i++) {
                $rawImageData = $doc->getElementsByTagName('image_name')->item($i)->nodeValue ?? null;
                $filename = null;

                if ($rawImageData) {
                    $base64Image = html_entity_decode($rawImageData);

                    if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                        $extension = strtolower($type[1]);
                        $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                    } else {
                        $extension = 'png';
                    }

                    $imageData = base64_decode($base64Image, true);

                    if ($imageData === false) {
                        throw new Exception('Invalid base64 image data');
                    }

                    $oneTime = time();
                    $filename = uniqid("IMG-") . $oneTime . '.' . $extension;
                    $filePath = 'order/' . $filename;

                    $saved = Storage::disk('public')->put($filePath, $imageData);

                    if (!$saved) {
                        throw new Exception('Failed to write image to disk using Storage');
                    }
                }


                $orderDetail = new OrderDetail();
                $orderDetail->item_id       = $doc->getElementsByTagName('id')->item($i)->nodeValue;
                $orderDetail->item_name     = $doc->getElementsByTagName('name')->item($i)->nodeValue;
                $orderDetail->qty           = $doc->getElementsByTagName('quantity')->item($i)->nodeValue;
                $orderDetail->rate          = $doc->getElementsByTagName('rate')->item($i)->nodeValue;
                $orderDetail->amount        = round($doc->getElementsByTagName('amount')->item($i)->nodeValue);
                $orderDetail->discount      = $doc->getElementsByTagName('itemdisc')->item($i)->nodeValue;
                $orderDetail->narration     = $doc->getElementsByTagName('printing')->item($i)->nodeValue;
                $orderDetail->other_remark  = $doc->getElementsByTagName('narration')->item($i)->nodeValue;
                $orderDetail->remark        = $doc->getElementsByTagName('remarks')->item($i)->nodeValue;
                $orderDetail->block         = $doc->getElementsByTagName('block')->item($i)->nodeValue ? $doc->getElementsByTagName('block')->item($i)->nodeValue : NULL;
                $orderDetail->print_type_id = PrintType::where('name', $doc->getElementsByTagName('size')->item($i)->nodeValue)->first()->id ?? NULL;
                $orderDetail->transport_id  = NULL;
                $orderDetail->branch_id     = 1;
                $orderDetail->created_by    = 11;
                $orderDetail->order_id      = $order->id;
                $orderDetail->design        = $filename;
                $orderDetail->save();

                $subTotalAmount += $orderDetail->amount;
            }

            if ($orderDiscount > 0) {
                $offerDiscount = ($subTotalAmount * ($orderDiscount / 100));
            }

            $netAmount = 0;
            $netAmount = $subTotalAmount - $offerDiscount - $coinRedeem;

            $order->update(['total_amount' => $subTotalAmount, 'net_amount' => $netAmount, 'discount_amount' => $offerDiscount]);

            DB::beginTransaction();

            $walletController = new \App\Http\Controllers\WalletController();
            if ($order->redeem_coin > 0) {
                if ($order->customer_id) {
                    $walletController->debit($order->customer_id, $order->redeem_coin, $order->id, 1, 'Order Redeem Coin', date('Y-m-d'));
                }
            }

            $customer = Customer::find($order->customer_id);
            if ($order->cash_back_coin > 0 && $customer->party_type_id == 2) {
                if ($order->customer_id) {
                    $walletController->credit($order->customer_id, $order->cash_back_coin, $order->id, 3, 'Order Cash Back Coin', date('Y-m-d'));
                }
            }

            DB::commit();

            $xmlContent['code'] = 1;
            $xmlContent['OrderCode'] = $order->order_code;
            return response()->json($xmlContent, 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            // throw $e;
            $re['code'] = 0;
            $re['note'] = $e->getMessage();
            return response()->json($re, 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function past_list(Request $request)
    {
        $data = $request->getContent();

        if (empty($data)) {
            return response()->json([
                'code' => 0,
                'Message' => 'No data received'
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }

        $xml = simplexml_load_string($data);

        $UserID = (string)$xml->UserID;
        $UserPass = (string)$xml->UserPass;
        $AppVersion = (string)$xml->AppVersion;

        $appVersionQuery = DB::table('appversionstatus')
            ->where('AppVersionText', '<=', $AppVersion)
            ->exists();

        if ($appVersionQuery) {
            $user = DB::table('customer')
                ->select('status as UserActive', 'id as UserCode', 'id as PartyConCode', 'name as PartyName', 'password')
                ->where('email', $UserID)
                ->first();

            if ($user && $UserPass == $user->password) {
                if ($user->UserActive == 'ACTIVE') {
                    $UserCode = $user->UserCode;

                    $orderDetailsQuery = DB::table('order_details')
                        ->select('order_details.order_id as OrderCode', 'order_details.status as Status')
                        ->distinct()
                        ->join('orders', 'orders.id', '=', 'order_details.order_id')
                        ->where('orders.customer_id', $UserCode)
                        ->where('order_details.Status', 'Non Sale')
                        ->orderBy('OrderCode')
                        ->get();

                    if ($orderDetailsQuery->isNotEmpty()) {
                        $response = [
                            'code' => 1,
                            'data' => []
                        ];

                        foreach ($orderDetailsQuery as $index => $orderDetail) {
                            $TempOrderCode = $orderDetail->OrderCode;

                            $response['data'][$index] = (array) $orderDetail;

                            // $paymentDetails = DB::table('RetailPaymentDetail')
                            //     ->select(
                            //         'RetailPaymentID as PayId',
                            //         'TempOrderCode as OrderCode',
                            //         'RetailPaymentDate as PayDate',
                            //         'RetailPaymentDescription as Descr',
                            //         'RetailPaymentAmount as Amount',
                            //         'RetailPaymentVerified as Verified'
                            //     )
                            //     ->where('TempOrderCode', $TempOrderCode)
                            //     ->get();

                            // if ($paymentDetails->isNotEmpty()) {
                            //     $response['data'][$index]['payment'] = $paymentDetails;
                            // }

                            $orderItemDetails = DB::table('order_details')
                                ->select(
                                    DB::raw('date_format(`orders`.`date`, "%d-%m-%Y") as Date'),
                                    'order_details.item_id as ItemCode',
                                    'order_details.item_name as ItemName',
                                    'order_details.qty as Qty',
                                    'order_details.amount as Amount',
                                    'order_details.rate as Rate',
                                    'order_details.narration as Printing',
                                    'order_details.narration as Narration',
                                    'order_details.remark as Remark',
                                    DB::raw('" " as Place'),
                                    DB::raw('" " as Phone'),
                                    'order_details.block as Block',
                                    DB::raw('" " as Img'),
                                    DB::raw('rtrim(ltrim(print_type.name)) as Size'),
                                    'order_details.discount as Disc',
                                    'orders.discount as UDisc',
                                    DB::raw('" " as GST')
                                )
                                ->join('orders', 'orders.id', '=', 'order_details.order_id')
                                ->join('print_type', 'print_type.id', '=', 'order_details.print_type_id')
                                ->where('order_details.order_id', $TempOrderCode)
                                ->where('order_details.status', 'Non Sale')
                                ->get();

                            if ($orderItemDetails->isNotEmpty()) {
                                $response['data'][$index]['detail'] = $orderItemDetails;
                            }

                            // $orderOfferDetails = DB::table('TempOrderOffer')
                            //     ->select('RedeemCoins as rcoin', 'CashbackCoins as ccoin', 'OfferDiscount as disc')
                            //     ->where('TempOrderCode', $TempOrderCode)
                            //     ->first();

                            // if ($orderOfferDetails) {
                            //     $response['data'][$index]['offer'] = $orderOfferDetails;
                            // }
                        }

                        return response()->json($response, 200, [], JSON_UNESCAPED_UNICODE);
                    } else {
                        return response()->json([
                            'code' => 0,
                            'Message' => 'Order List Not Found'
                        ], 200, [], JSON_UNESCAPED_UNICODE);
                    }
                } else {
                    return response()->json([
                        'code' => 0,
                        'Message' => 'User Is Not Active'
                    ], 200, [], JSON_UNESCAPED_UNICODE);
                }
            } else {
                return response()->json([
                    'code' => 0,
                    'Message' => 'User Email And Password Not Match'
                ], 200, [], JSON_UNESCAPED_UNICODE);
            }
        } else {
            return response()->json([
                'code' => 2,
                'Message' => 'Update Your App Version'
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function pending_list(Request $request)
    {
        $data = $request->getContent();

        if (empty($data)) {
            return response()->json([
                'code' => 0,
                'Message' => 'No data received'
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }

        $xml = simplexml_load_string($data);

        $UserID = (string)$xml->UserID;
        $UserPass = (string)$xml->UserPass;
        $AppVersion = (string)$xml->AppVersion;

        $appVersionQuery = DB::table('appversionstatus')
            ->where('AppVersionText', '<=', $AppVersion)
            ->exists();

        if ($appVersionQuery) {
            $user = DB::table('customer')
                ->select('status as UserActive', 'created_by as UserCode', 'id as PartyConCode', 'name as PartyName', 'password')
                ->where('email', $UserID)
                ->first();

            if ($user && $UserPass == $user->password) {
                if ($user->UserActive == 'ACTIVE') {
                    $PartyConCode = $user->PartyConCode;
                    $PartyName = $user->PartyName;

                    $pendingOrdersQuery = Order::select(
                        'orders.id as TCode',
                        'orders.po_no as PONo',
                        DB::raw('date_format(`orders`.`date`, "%d-%m-%Y") as PODate'),
                        'order_details.item_name as ItemName',
                        'order_details.qty as OQty',
                        'order_details.dispatch_qty as SOQty',
                        'order_details.rate as Rate',
                        'order_details.block as Block',
                        'order_details.id as DetailCode',
                        'orders.order_code as OrderCode',
                        'items.categories_id as SCode',
                        'orders.customer_id as PCode',
                        'order_details.item_id as MCode',
                        'order_details.Narration',
                        DB::raw('" " as Transport'),
                        'order_details.remark as Remark',
                        'customer.city_id as PartyCity',
                        'order_details.design as Img',
                        'order_details.print_type_id',
                        'items.name as ItemName',
                        'print_type.name as Type',
                        'items.name as ItemDetailName',
                        DB::raw('order_details.qty - order_details.dispatch_qty as PendingQty')
                    )
                        ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                        ->join('print_type', 'order_details.print_type_id', '=', 'print_type.id')
                        ->join('items', 'order_details.item_id', '=', 'items.id')
                        ->join('customer', 'orders.customer_id', '=', 'customer.id')
                        ->where('orders.customer_id', $PartyConCode)
                        ->whereRaw('order_details.qty - order_details.dispatch_qty > 0')
                        ->get();

                    $poMasterQuery = DB::table('orders')
                        ->select(
                            'orders.po_no as PONo',
                            DB::raw('date_format(`orders`.`date`, "%d-%m-%Y") as PODate'),
                            DB::raw('SUM(order_details.dispatch_qty) as SOQty'),
                            DB::raw('SUM(order_details.qty) as OQty'),
                            DB::raw('SUM(order_details.qty - order_details.dispatch_qty) as PendingQty'),
                            'orders.order_code as OrderCode',
                            DB::raw('SUM(order_details.amount) as PurcOrderAmount')
                        )
                        ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                        ->join('customer', 'orders.customer_id', '=', 'customer.id')
                        ->where('customer.name', 'like', $PartyName)
                        ->whereRaw('order_details.qty - order_details.dispatch_qty > 0')
                        ->groupBy('orders.id', 'orders.po_no', 'orders.date', 'orders.order_code')
                        ->get();

                    if ($pendingOrdersQuery->isNotEmpty() || $poMasterQuery->isNotEmpty()) {
                        return response()->json([
                            'code' => 1,
                            'data' => [
                                'records' => $pendingOrdersQuery,
                                'master' => $poMasterQuery
                            ]
                        ], 200, [], JSON_UNESCAPED_UNICODE);
                    } else {
                        return response()->json([
                            'code' => 0,
                            'Message' => 'Order List Not Found'
                        ], 200, [], JSON_UNESCAPED_UNICODE);
                    }
                } else {
                    return response()->json([
                        'code' => 0,
                        'Message' => 'User Is Not Active'
                    ], 200, [], JSON_UNESCAPED_UNICODE);
                }
            } else {
                return response()->json([
                    'code' => 0,
                    'Message' => 'User Email And Password Not Match'
                ], 200, [], JSON_UNESCAPED_UNICODE);
            }
        } else {
            return response()->json([
                'code' => 2,
                'Message' => 'Update Your App Version'
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
