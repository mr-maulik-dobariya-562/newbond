<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $xmlContent = array();
        $xmlInput = $request->getContent();

        if (empty($xmlInput)) {
            // For testing, you can uncomment the line below to set a default XML string.
            // $xmlInput = "<data><mobile>9998890601</mobile><email>vimal.r.jain@gmail.com</email><vcode>251325</vcode><vcode>251325</vcode><brandcode>9</brandcode></data>";
            $xmlContent["code"] = 2;
            $xmlContent["Message"] = "No input data";
            return response()->json($xmlContent, JSON_UNESCAPED_UNICODE);
        }

        $doc = new \DOMDocument();
        $doc->loadXML($xmlInput);
        $elements = $doc->getElementsByTagName("value");

        if ($elements->length > 0) {
            $userName = $doc->getElementsByTagName("UserID")->item(0)->nodeValue;
            $userPass = $doc->getElementsByTagName("UserPass")->item(0)->nodeValue;
            $partyCode = $doc->getElementsByTagName("UID")->item(0)->nodeValue;
            $companyName = strtoupper($doc->getElementsByTagName("UserName")->item(0)->nodeValue);
            $userEmail = $doc->getElementsByTagName("UserEmail")->item(0)->nodeValue;
            $userAddress = $doc->getElementsByTagName("UserAddress")->item(0)->nodeValue;
            $userArea = $doc->getElementsByTagName("UserArea")->item(0)->nodeValue;
            $userCity = $doc->getElementsByTagName("UserCity")->item(0)->nodeValue;
            $userMobile = $doc->getElementsByTagName("UserMobile")->item(0)->nodeValue;
            $userState = $doc->getElementsByTagName("UserState")->item(0)->nodeValue;
            $userPinCode = $doc->getElementsByTagName("UserPincode")->item(0)->nodeValue;
            $userGstCode = strtoupper($doc->getElementsByTagName("UserGst")->item(0)->nodeValue);
            $contactPerson = $doc->getElementsByTagName("ContactPersonName")->item(0)->nodeValue;

            // Check for App Version
            $user = DB::table('customer')
                ->where(function ($query) use ($userName) {
                    $query->where('email', $userName)
                        ->orWhere('mobile', $userName);
                })
                ->where('deleted_at', null)
                ->first();
            if ($user && $userPass == $user->password) {
                if ($user->email != $userEmail) {
                    // Check if email already exists
                    $existingEmail = DB::table('customer')->where('email', '=', strtolower($userEmail))->first();
                    if ($existingEmail) {
                        $xmlContent["code"] = 0;
                        $xmlContent["Message"] = "User Email already exists";
                        return response()->json($xmlContent, JSON_UNESCAPED_UNICODE);
                    }
                }

                if (strcasecmp($user->mobile, $userMobile) != 0) {
                    // Check if mobile number already exists
                    $existingMobile = DB::table('customer')->where('mobile', $userMobile)->first();
                    if ($existingMobile) {
                        $xmlContent["code"] = 0;
                        $xmlContent["Message"] = "User Mobile already exists";
                        return response()->json($xmlContent, JSON_UNESCAPED_UNICODE);
                    }
                }

                DB::table('customer')
                    ->where('id', $partyCode)
                    ->update([
                        'name' => $companyName,
                        'email' => $userEmail,
                        'address' => $userAddress,
                        'area' => $userArea,
                        'city_id' => findOrCreate(City::class, "name", $userCity),
                        // 'city_id' => $userCity,
                        'mobile' => $userMobile,
                        'contact_person' => $contactPerson,
                        'state_id' => findOrCreate(State::class, "name", $userState),
                        // 'state_id' => $userState,
                        'pincode' => $userPinCode,
                        'gst' => $userGstCode,
                    ]);

                $xmlContent["code"] = 1;
                $xmlContent["Message"] = "Profile data updated successfully.";
            } else {
                $xmlContent["code"] = 2;
                $xmlContent["Message"] = "Invalid credentials";
            }
        } else {
            $xmlContent["code"] = 2;
            $xmlContent["Message"] = "Invalid request source";
        }

        return response()->json($xmlContent, JSON_UNESCAPED_UNICODE);
    }
}
