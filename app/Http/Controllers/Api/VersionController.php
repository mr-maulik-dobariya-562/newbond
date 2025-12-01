<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appversionstatus;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class VersionController extends Controller
{
    public function index(Request $request)
    {
        $xmlContent = [];

        $rawPostData = $request->getContent();
        $doc = new \DOMDocument();
        $doc->loadXML($rawPostData);
        $elements = $doc->getElementsByTagName("value");

        if ($elements->length > 0) {
            $UserID = $doc->getElementsByTagName("UserID")->item(0)->nodeValue;
            $UserPass = $doc->getElementsByTagName("UserPass")->item(0)->nodeValue;

            if ($UserID == env('ADMIN_USER_ID') && $UserPass == env('ADMIN_USER_PASS')) {
                $latestVersion = Appversionstatus::orderBy('created_at', 'DESC')->first();
            } else {
                $user = Customer::where('email', $UserID)->first();

                if ($user && $UserPass == $user->password) {
                    $latestVersion = Appversionstatus::orderBy('created_at', 'DESC')->first();
                } else {
                    $xmlContent["code"] = 0;
                    $xmlContent["Message"] = "User ID or password does not match.";
                    return response()->json($xmlContent, 200, [], JSON_UNESCAPED_UNICODE);
                }
            }

            if ($latestVersion) {
                $xmlContent["code"] = 1;
                $xmlContent["data"][0]["UploadDataVersion"] = ['UploadDataCode' => (int)$latestVersion->AppVersionText];
            } else {
                $xmlContent["code"] = 0;
                $xmlContent["Message"] = "No Latest Data Version";
            }
        } else {
            $xmlContent["code"] = 0;
            $xmlContent["Message"] = "Length Not Found";
        }

        return response()->json($xmlContent, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
