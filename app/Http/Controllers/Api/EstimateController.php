<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use SebastianBergmann\Template\Template;

class EstimateController extends Controller
{
    public function index(Request $request)
    {
        $xmlContent = [];
        $xmlContent["Message"] = "";

        $HTTP_RAW_POST_DATA = $request->getContent();
        if (strlen($HTTP_RAW_POST_DATA) == 0) {
            // $HTTP_RAW_POST_DATA = "<value><UserID>support@spectacase.com</UserID><UserPass>piyush2870</UserPass><AppVersion>2.6</AppVersion></value>";
        }

        $doc = new \DOMDocument();
        $doc->loadXML($HTTP_RAW_POST_DATA);
        $elements = $doc->getElementsByTagName("value");
        if ($elements->length > 0) {
            $UserID = $doc->getElementsByTagName("UserID")->item(0)->nodeValue;
            $UserPass = $doc->getElementsByTagName("UserPass")->item(0)->nodeValue;
            $AppVersion = $doc->getElementsByTagName("AppVersion")->item(0)->nodeValue;
            $startDate = $doc->getElementsByTagName("start")->item(0)->nodeValue;
            $endDate = $doc->getElementsByTagName("end")->item(0)->nodeValue;

            $appVersionStatus =  DB::table('appversionstatus')
            ->where('AppVersionText', '<=', $AppVersion)
            ->exists();

            if ($appVersionStatus) {
                $user = DB::table('customer')
                    ->select('status', 'id as PartyConCode', 'password')
                    ->where('email', $UserID)
                    ->first();
                if ($user && $UserPass == $user->password) {
                    if ($user->status == 'ACTIVE') {
                        $PartyConCode = $user->PartyConCode;
                        $query = DB::table('estimates')
                            ->select([
                                'estimates.id AS BCode',
                                'estimates.courier_id',
                                'po_no AS BNo',
                                'date AS BillDate',
                                'lr_no AS LRNo',
                                DB::raw('" " AS Dispetch'),
                                'parcel AS Parcel',
                                'total_amount AS Amount',
                                DB::raw('" " AS Disper'),
                                DB::raw('" " AS Pkg'),
                                DB::raw('" " AS VAT'),
                                DB::raw('" " AS VATAmt'),
                                'net_amount AS NetAmt',
                                'couriers.name AS Courier',
                                'lr_date AS LRDate',
                                'docket AS Docket',
                                'note AS Note',
                            ])
                            ->join('couriers', 'couriers.id', '=', 'estimates.courier_id', 'LEFT')
                            ->where('customer_id', $PartyConCode);

                        if (strlen($startDate) > 0 && strlen($endDate) > 0) {
                            $query->whereBetween('estimates.date', [date('Y-m-d', strtotime($startDate)), date('Y-m-d', strtotime($endDate))]);
                        }

                        $orders = $query->get();

                        if ($orders->count() > 0) {
                            $xmlContent["code"] = 1;
                            $index = 0;

                            foreach ($orders as $order) {
                                $TempOrderCode = $order->BCode;
                                $xmlContent["data"][$index] = (array) $order;
                                $xmlContent["data"][$index]["Amount"] = intval($order->Amount);

                                $details = DB::table('estimate_details')
                                    ->select([
                                        'id AS SrNo',
                                        'item_name AS Name',
                                        'qty as Qty',
                                        'rate as Rates',
                                        'amount AS Amount',
                                        'date AS Date',
                                        'narration AS Narration',
                                    ])
                                    ->where('estimate_id', $TempOrderCode)
                                    ->orderBy('SrNo')
                                    ->get();

                                if ($details->count() > 0) {
                                    $index2 = 0;
                                    foreach ($details as $detail) {
                                        $xmlContent["data"][$index]["records"][$index2] = (array) $detail;
                                        $index2++;
                                    }
                                }
                                $index++;
                            }

                            return response()->json($xmlContent, 200, [], JSON_UNESCAPED_UNICODE);
                        } else {
                            $xmlContent["code"] = 0;
                            $xmlContent["Message"] = "Order List Not Found";
                        }
                    } else {
                        $xmlContent["code"] = 0;
                        $xmlContent["Message"] = "User Is Not Active";
                    }
                } else {
                    $xmlContent["code"] = 0;
                    $xmlContent["Message"] = "User Email And Password Not Match";
                }
            } else {
                $xmlContent["code"] = 2;
                $xmlContent["Message"] = "Update Your App Version";
            }
        } else {
            $xmlContent["code"] = 0;
            $xmlContent["Message"] = "Length Not Found";
        }

        return response()->json($xmlContent, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
