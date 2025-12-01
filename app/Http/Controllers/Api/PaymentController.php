<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PaymentController extends Controller
{
	public function index(Request $request)
	{
		$xmlContent = array();
		$xmlInput = $request->getContent();

		if (empty($xmlInput)) {
			$xmlContent["code"] = 0;
			$xmlContent["Message"] = "No input data";
			return response()->json($xmlContent, JSON_UNESCAPED_UNICODE);
		}

		$doc = new \DOMDocument();
		$doc->loadXML($xmlInput);

		$elements = $doc->getElementsByTagName("value");
		if ($elements->length > 0) {
			$orderCode = $doc->getElementsByTagName("ordercode")->item(0)->nodeValue;
			$paymentDate = date('Y-m-d', strtotime($doc->getElementsByTagName("paymentDate")->item(0)->nodeValue));
			$amount = $doc->getElementsByTagName("amount")->item(0)->nodeValue;
			$detail = $doc->getElementsByTagName("detail")->item(0)->nodeValue;
			$userID = $doc->getElementsByTagName("UserID")->item(0)->nodeValue;
			$userPass = $doc->getElementsByTagName("UserPass")->item(0)->nodeValue;

			$user = DB::table('customer')
				->where('email', $userID)
				->first();

			if ($user && $userPass == $user->password) {
				$existingPayment = DB::table('payments')
					->where('id', $orderCode)
					->first();
                if ($existingPayment) {
                    DB::table('payments')
                        ->where('id', $orderCode)
                        ->update([
                            'date' => $paymentDate,
                            'amount' => $amount,
                            'remark' => $detail
                        ]);
                    $xmlContent["Message"] = "Payment Detail Updated successfully";
                } else {
                    DB::table('payments')
                        ->insert([
                            'type'         => 'CREDIT',
                            'payment_type' => 'CASH',
                            'date'         => $paymentDate,
                            'amount'       => $amount,
                            'remark'       => $detail,
                            'customer_id'  => $user->id,
                            'created_by'   => $user->id
                        ]);
                    $xmlContent["Message"] = "Payment Detail Inserted successfully";
                }
				$xmlContent["code"] = 1;
			} else {
				$xmlContent["code"] = 0;
				$xmlContent["Message"] = "UserId And Password Not match";
			}
		} else {
			$xmlContent["code"] = 0;
			$xmlContent["Message"] = "Length Not Found";
		}

		return response()->json($xmlContent, JSON_UNESCAPED_UNICODE);
	}
}
