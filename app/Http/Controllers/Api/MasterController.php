<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FileUpload;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Feedback;
use App\Models\Offers;
use App\Models\TermsCondition;
use Google\Service\ServiceControl\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->getContent();

        $xml = simplexml_load_string($data);

        if ($xml && isset($xml->UserID, $xml->UserPass)) {

            $UserID = (string)$xml->UserID;
            $UserPass = (string)$xml->UserPass;

            if ($UserID == env('ADMIN_USER_ID') && $UserPass == env('ADMIN_USER_PASS')) {
                $offers = Offers::get();
            } else {
                $user = Customer::where('email', $UserID)
                    ->first();

                if ($user && $UserPass == $user->password) {
                    $offers = Offers::where('party_type_id', $user->party_type_id)->get();
                } else {
                    return response()->json([
                        'code' => 0,
                        'Message' => 'user id password not match'
                    ], 200, [], JSON_UNESCAPED_UNICODE);
                }
            }
            // $data = [
            //     [
            //         "SchemeId" => 1,
            //         "Type" => "REGISTRATION",
            //         "Value" => 600,
            //         "ValueType" => "NUMBER",
            //         "DiscountType" => "WALLET",
            //         "Status" => "ON",
            //         "Limit" => 0,
            //         "CreateDate" => [
            //             "date" => "2022-04-13 11:14:35.573000",
            //             "timezone_type" => 3,
            //             "timezone" => "Asia/Kolkata"
            //         ],
            //         "LastUpdateDate" => [
            //             "date" => "2022-04-13 11:14:35.573000",
            //             "timezone_type" => 3,
            //             "timezone" => "Asia/Kolkata"
            //         ],
            //         "Remark" => "For first time users to encourage for using our application, giving 600 wallet coins on successful registration.",
            //         "CoinValidity" => 180
            //     ],
            //     [
            //         "SchemeId" => 2,
            //         "Type" => "REFERRAL",
            //         "Value" => 400,
            //         "ValueType" => "NUMBER",
            //         "DiscountType" => "WALLET",
            //         "Status" => "ON",
            //         "Limit" => 0,
            //         "CreateDate" => [
            //             "date" => "2022-04-13 11:14:35.573000",
            //             "timezone_type" => 3,
            //             "timezone" => "Asia/Kolkata"
            //         ],
            //         "LastUpdateDate" => [
            //             "date" => "2022-04-13 11:14:35.573000",
            //             "timezone_type" => 3,
            //             "timezone" => "Asia/Kolkata"
            //         ],
            //         "Remark" => "If new user registers using referral code than the user who refers will get 400 wallet coins.",
            //         "CoinValidity" => 180
            //     ],
            //     [
            //         "SchemeId" => 4,
            //         "Type" => "ORDER",
            //         "Value" => 10,
            //         "ValueType" => "PERCENT",
            //         "DiscountType" => "WALLET",
            //         "Status" => "ON",
            //         "Limit" => 1000,
            //         "CreateDate" => [
            //             "date" => "2022-04-30 12:32:37.580000",
            //             "timezone_type" => 3,
            //             "timezone" => "Asia/Kolkata"
            //         ],
            //         "LastUpdateDate" => [
            //             "date" => "2022-04-30 12:32:37.580000",
            //             "timezone_type" => 3,
            //             "timezone" => "Asia/Kolkata"
            //         ],
            //         "Remark" => "Get 10% as cashback coins on any order",
            //         "CoinValidity" => 180
            //     ],
            //     [
            //         "SchemeId" => 6,
            //         "Type" => "REDEEM",
            //         "Value" => 10,
            //         "ValueType" => "PERCENT",
            //         "DiscountType" => "WALLET",
            //         "Status" => "ON",
            //         "Limit" => 0,
            //         "CreateDate" => [
            //             "date" => "2022-06-27 20:31:15.860000",
            //             "timezone_type" => 3,
            //             "timezone" => "Asia/Kolkata"
            //         ],
            //         "LastUpdateDate" => [
            //             "date" => "2022-06-27 20:31:15.860000",
            //             "timezone_type" => 3,
            //             "timezone" => "Asia/Kolkata"
            //         ],
            //         "Remark" => "For each order one can redeem upto 20% of order value coins.",
            //         "CoinValidity" => 0
            //     ]
            // ];
            if ($data) {
                return response()->json([
                    'code' => 1,
                    'data' => $offers
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                return response()->json([
                    'code' => 0,
                    'Message' => 'No New Arrival'
                ], 200, [], JSON_UNESCAPED_UNICODE);
            }
        } else {
            return response()->json([
                'code' => 0,
                'Message' => 'No data received'
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function termsConditions(Request $request)
    {
        $data = $request->getContent();

        $xml = simplexml_load_string($data);

        if ($xml && isset($xml->UserID, $xml->UserPass)) {
            $UserID = (string)$xml->UserID;
            $UserPass = (string)$xml->UserPass;

            if ($UserID == env('ADMIN_USER_ID') && $UserPass == env('ADMIN_USER_PASS')) {
                $termsConditions = TermsCondition::get();
            } else {
                $user = DB::table('customer')
                    ->where('email', $UserID)
                    ->first();

                if ($user && $UserPass == $user->password) {
                    $termsConditions = TermsCondition::where('party_type_id', $user->party_type_id)->get();
                } else {
                    return response()->json([
                        'code' => 0,
                        'Message' => 'user id password not match'
                    ], 200, [], JSON_UNESCAPED_UNICODE);
                }
            }

            if ($termsConditions->isNotEmpty()) {
                return response()->json([
                    'code' => 1,
                    'data' => $termsConditions
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                return response()->json([
                    'code' => 0,
                    'Message' => 'No New Arrival'
                ], 200, [], JSON_UNESCAPED_UNICODE);
            }
        } else {
            return response()->json([
                'code' => 0,
                'Message' => 'No data received'
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function feedback(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'UserID' => 'required|string',
            'UserPass' => 'required|string',
        ]);

        if ($request->UserID == env('ADMIN_USER_ID') && $request->UserPass == env('ADMIN_USER_PASS')) {
            $feedback = Feedback::create([
                'title' => $data['title'],
                'message' => $data['message'],
                'created_by' => 1,
                'branch_id' => 1
            ]);
            if (isset($data['image'])) {
                $fileUrl = (new FileUpload())->upload($data['image'], 'feedback');
                $feedback->update(['image' => $fileUrl]);
            }
        } else {
            $user = Customer::where('email', $request->UserID)
                ->first();

            if ($user && $request->UserPass == $user->password) {
                $feedback = Feedback::create([
                    'title' => $data['title'],
                    'message' => $data['message'],
                    'created_by' => $user->id,
                    'branch_id' => $user->branch_id
                ]);

                if (isset($data['image'])) {
                    $fileUrl = (new FileUpload())->upload($data['image'], 'feedback');
                    $feedback->update(['image' => $fileUrl]);
                }
            } else {
                return response()->json([
                    'code' => 0,
                    'Message' => 'User ID or password do not match'
                ], 200, [], JSON_UNESCAPED_UNICODE);
            }
        }

        if ($feedback) {
            return response()->json([
                'code' => 1,
                'Message' => 'Feedback Add successfully'
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return response()->json([
                'code' => 0,
                'Message' => 'Failed to create feedback'
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
