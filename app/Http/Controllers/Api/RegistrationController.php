<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\PartyType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->getContent();

        if (empty($data)) {
            // Handle empty data scenario here if needed
        }

        $data = simplexml_load_string($data);

        $adminEmail = $data->AdminEmail;
        $adminPassword = $data->AdminPassword;
        $userName = $data->UserName;
        $userEmail = $data->UserEmail;
        $userPassword = $data->UserPassword;
        $userMobile = $data->UserMobile;
        $contactPerson = $data->ContactPersonName;
        $userSelectedType = $data->Type;
        $userReferralCode = $data->RCode;
        $tokenId = $data->TokenId ?? null;



        $strVerificationCode = Str::random(6);

        if ($adminEmail != env('ADMIN_USER_ID') && $adminPassword != env('ADMIN_USER_PASS')) {
            return response()->json([
                'code' => 0,
                'Message' => 'Admin Id And Password are Wrong'
            ]);
        }

        // Check if UserEmail exists
        if (DB::table('customer')->where('email', $userEmail)->whereNull('deleted_at')->exists()) {
            return response()->json([
                'code' => 0,
                'Message' => 'User Email already exists.'
            ]);
        }

        // Check if UserMobile exists
        if (DB::table('customer')->where('mobile', $userMobile)->whereNull('deleted_at')->exists()) {
            return response()->json([
                'code' => 0,
                'Message' => 'User mobile already exists.'
            ]);
        }

        // Check Referral Code validity
        $referenceID = null;
        if (!empty($userReferralCode->__toString())) {
            $referrer = DB::table('customer')->where('mobile', $userReferralCode)->first();
            if (!$referrer) {
                return response()->json([
                    'code' => 0,
                    'Message' => 'Referral code is not valid. Please use correct code.'
                ]);
            }
            $referenceID = $referrer->id;
        }

        // $strReferralCode = $userMobile;

        // $userCode = DB::table('party_contacts')->max('UserCode') + 1;
        // $partyId = DB::table('party_contacts')->max('PartyId') + 1;

        // Insert into PartyContact
        $customerId = DB::table('customer')->insertGetId([
            'name' => $userName,
            'email' => $userEmail,
            'password' => $userPassword,
            'status' => 'ACTIVE',
            'mobile' => $userMobile,
            'contact_person' => $contactPerson,
            'token_id' => $tokenId,
            'ReferralCode' => $referenceID,
            'party_type_id' => PartyType::where('name', $userSelectedType)->value('id') ?? null,
            'party_group_id' => 31,
            'other_sample' => 'NO',
            'branch_id' => 1,
        ]);

        // For demonstration, we'll simulate an SMS message.
        $smsMessage = "Welcome in Spectacase app , your OTP for registration is $strVerificationCode\nFrom Team Spectacase";

        DB::beginTransaction();

        $walletController = new \App\Http\Controllers\WalletController();

        $customer = Customer::find($customerId);
        if ($customer) {
            $walletController->credit($customer->id, 600, $customer->id, 5, 'Register Cash Back Coin', date('Y-m-d'));
        }

        DB::commit();

        return response()->json([
            'code' => 1,
            'Message' => 'You have successfully registered. Please login into your account.',
            'sms' => $smsMessage
        ]);
    }

    public function updatePassword(Request $request)
    {
        $HTTP_RAW_POST_DATA = $request->getContent();
        $xmlContent = [];

        if (strlen($HTTP_RAW_POST_DATA) == 0) {
            $xmlContent["code"] = 0;
            $xmlContent["Message"] = "No XML input found";
            return response()->json($xmlContent);
        }

        $doc = new \DOMDocument();
        $doc->loadXML($HTTP_RAW_POST_DATA);
        $elements = $doc->getElementsByTagName("value");

        if ($elements->length > 0) {
            $UserID = $doc->getElementsByTagName("UserID")->item(0)->nodeValue;
            $UserOldPass = $doc->getElementsByTagName("UserOldPass")->item(0)->nodeValue;
            $UserNewPass = $doc->getElementsByTagName("UserNewPass")->item(0)->nodeValue;

            $user = DB::table('customer')->where('email', $UserID)->first();

            if ($user && $UserOldPass == $user->password) {
                $updated = DB::table('customer')
                    ->where('email', $UserID)
                    ->update(['password' => $UserNewPass]);

                if ($updated) {
                    // DB::table('UploadDataversion')->insert([
                    //     'UploadDataType' => 'PasswordUpdate',
                    //     'UploadDataStatus' => 'Success',
                    //     'UploadDataDate' => now()
                    // ]);

                    $xmlContent["code"] = 1;
                    $xmlContent["Message"] = "Password Changed successfully";
                } else {
                    $xmlContent["code"] = 0;
                    $xmlContent["Message"] = "Password Not Changed successfully";
                }
            } else {
                $xmlContent["code"] = 0;
                $xmlContent["Message"] = "UserEmail And Password Not match";
            }
        } else {
            $xmlContent["code"] = 0;
            $xmlContent["Message"] = "XML Length Not Found";
        }

        return response()->json($xmlContent);
    }
}
