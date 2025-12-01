<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Catalogue;
use App\Models\ItemGroupPrice;
use App\Models\PriceList;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use SimpleXMLElement;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $xmlContent = [
            "Message" => "",
            "ralert" => "- Quality insurance by <b>SPECTA CASE</b><br>- Life time <b>Replacement</b> Guarantee<br>- <b>100%</b> Advance Payment<br>- Free delivery on 600 pcs but Preferred <b>door delivery, subject to transport service availability</b><br>- Rate is including <b>GST</b>",
        ];

        $rawPostData = $request->getContent();

        // Load the XML data
        $xml = simplexml_load_string($rawPostData);

        if (isset($xml->UserID, $xml->UserPass, $xml->AppVersion, $xml->TokenId)) {
            $UserID = $xml->UserID;
            $UserPass = $xml->UserPass;
            $AppVersion = $xml->AppVersion;
            $TokenId = $xml->TokenId;
            $DeviceType = isset($xml->DType) ? $xml->DType : 1;

            $appVersionRecord = DB::table('appversionstatus')
                // ->where('AppVersionText', $AppVersion)
                ->where('AppType', $DeviceType)
                ->first();

            // Check for App Version
            if ($appVersionRecord) {
                // Check for email and password
                $partyRecord = DB::table('customer')
                    ->where(function ($query) use ($UserID) {
                        $query->where('email', $UserID)
                            ->orWhere('mobile', $UserID);
                    })
                    ->where('password', $UserPass)
                    ->first();

                if ($partyRecord) {
                    // Fetch additional user data
                    $userRecords = DB::table('customer')
                        ->select('customer.name AS UserName',
                        'customer.id AS UserId',
                        'customer.email AS UserEmail',
                        'customer.address AS UserAddress',
                        'customer.area AS UserArea',
                        'cities.name AS UserCity',
                        'party_types.name AS Usertype',
                        'customer.password AS UserPassword',
                        DB::raw('"" AS askchg'),
                        'customer.status as UserActive',
                        'customer.id as UserCode',
                        'customer.mobile AS UserMobile',
                        DB::raw('"" AS UserDealerCode'),
                        DB::raw('"" AS UserDealerStatus'),
                        'customer.token_id AS TokenId',
                        'party_categorys.name AS UserCategory',
                        'states.name AS UserState',
                        'customer.pincode AS UserPincode',
                        'customer.gst AS UserGst',
                        'customer.contact_person as ContactPerson',
                        DB::raw('COALESCE(customer.discount, 0) AS PartyDiscount'),
                        'party_types.name As Type',
                        'customer.mobile AS RCode')
                        ->join('cities', 'customer.city_id', '=', 'cities.id','left')
                        ->join('states', 'customer.state_id', '=', 'states.id','left')
                        ->join('party_types', 'customer.party_type_id', '=', 'party_types.id','left')
                        ->join('party_categorys', 'customer.bill_type', '=', 'party_categorys.id','left')
                       ->where(function ($query) use ($UserID) {
                            $query->where('customer.email', $UserID)
                                ->orWhere('customer.mobile', $UserID);
                        })
                        ->where('customer.password', $UserPass)
                        ->where('customer.status', 'ACTIVE')
                        ->whereNull('customer.deleted_at')
                        ->get();

                    if ($userRecords->isNotEmpty()) {
                        DB::table('customer')
                            ->where(function ($query) use ($UserID) {
                                $query->where('email', $UserID)
                                    ->orWhere('mobile', $UserID);
                            })
                            ->update(['token_id' => $TokenId, 'last_login_date' => now()]);

                        $xmlContent["code"] = 1;
                        $xmlContent["qty_multiplier"] = 10;

                        // Prepare user data
                        foreach ($userRecords as $index => $userRecord) {
                            $xmlContent["data"][$index] = (array) $userRecord;
                        }

                        // Fetch Price List
                        $priceList = PriceList::selectRaw('price_lists.id, price_lists.title, party_types.name as PartyType, CONCAT("'.env('APP_URL').'/public/storage/pricelist/", price_lists.image) as image')
                            ->leftJoin('party_types', 'price_lists.party_type_id', '=', 'party_types.id')
                            ->where('party_types.name', $userRecord->Usertype)
                            ->get();

                        foreach ($priceList as $index => $priceRecord) {
                            $xmlContent["PriceList"][$index] = $priceRecord;
                        }

                        // Fetch Spectacoins
                        $coins = Wallet::orderBy('id', 'asc')
                            ->where('user_id', $userRecord->UserId)
                            ->get();

                        foreach ($coins as $index => $coinRecord) {
                            $xmlContent["Coins"][$index] = $coinRecord;
                        }

                        $offers=[
                            "0"=>[
                                "title"=>"For First time users to encourage for using our application,giving 600 wallet coins on successful registration.",
                                "start"=>"13-04-2022"
                            ],
                            "1"=>[
                                "title"=>"If new user register using referral code than the user who refers will get 400 wallet coins.",
                                "start"=>"13-04-2022"
                            ],
                            "2"=>[
                                "title"=>"Get 10% as cashback coins on any order",
                                "start"=>"30-04-2022"
                            ],
                            "3"=>[
                                "title"=>"For each order one can redeem upto 20% of order value coins.",
                                "start"=>"27-06-2022"
                            ],

                        ];

                        foreach ($offers as $index => $schemeRecord) {
                            $xmlContent["scm"][$index] = (array) $schemeRecord;
                        }

                        $xmlContent["dispose_policy_pdf"] = env('RAVI_URL') . "DisposePolicy.pdf";

                        return response()->json($xmlContent, 200, [], JSON_UNESCAPED_UNICODE);
                    } else {
                        $xmlContent["code"] = 0;
                        $xmlContent["Message"] = "Your account has not been approved. Please contact customer care for more details.";
                    }
                } else {
                    $xmlContent["code"] = 0;
                    $xmlContent["Message"] = "Entered User ID or Password may be wrong.";
                }
            } else {
                $xmlContent["code"] = 2;
                $xmlContent["Message"] = "Update Your App Version";
            }
        } else {
            $xmlContent["code"] = 0;
            $xmlContent["Message"] = "Invalid request format.";
        }

        return response()->json($xmlContent, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function credentials()
    {
        $loginType = filter_var(request()->input('UserID'), FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';

        return [
            $loginType => request()->input('UserID'),
            'password' => request()->input('UserPass'),
        ];
    }

    public function catalogue(Request $request)
    {
        $content = $request->getContent();
        if (empty($content)) {
            return Response::json(['code' => 0, 'Message' => 'Content is empty'], 200);
        }

        $xml = new SimpleXMLElement($content);
        $userID = $xml->UserID;
        $userPass = $xml->UserPass;
        $country = $xml->country ?? 'India';

        if ($userID != 'spectacaseadmin' || $userPass != 'admin@spectacase') {
            return Response::json(['code' => 0, 'Message' => 'User Id or password not match'], 200);
        }

        $pdfLink = $country == 'Kenya' ?
            'https://spectacase.com/wp-content/uploads/2019/11/spectacase.pdf' :
            'https://spectacase.com/wp-content/uploads/2019/09/Final-Book-2019.pdf';

        $catalogues = Catalogue::where('status', 'Active')
            ->where('country', $country)
            ->orderBy('order')
            ->get(['id AS Id', 'image AS URL', 'name AS Name', 'order AS OI', 'GroupCode AS GCode']);

        if ($catalogues->isEmpty()) {
            return Response::json(['code' => 0, 'Message' => 'No Catalogue Found'], 200);
        }

        $response = [
            'code' => 1,
            'catalogueurl' => 'http://order.spectacase.com/admin/images/Catalogue/',
            'thumburl' => 'http://order.spectacase.com/admin/images/Catalogue/',
            'pdf' => $pdfLink,
            'data' => $catalogues
        ];

        $groups = ItemGroupPrice::get(['PurcItemGroupCode AS GCode', 'name AS GName', 'description AS Settings']);
        if (!$groups->isEmpty()) {
            $response['Groups'] = $groups;
            $response['GroupURLPrefix'] = 'http://order.spectacase.com/admin/images/Group/';
        }

        return Response::json($response, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function login(Request $request)
    {
        $xmlContent = [
            "Message" => "",
            "ralert" => "- Quality insurance by <b>SPECTA CASE</b><br>- Life time <b>Replacement</b> Guarantee<br>- <b>100%</b> Advance Payment<br>- Free door delivery on purchase of more than 600 pcs in one order<br>- Rate is including <b>GST</b>"
        ];

        $inputXML = $request->getContent();
        $xml = simplexml_load_string($inputXML);

        if ($xml && isset($xml->UserID, $xml->UserPass, $xml->AppVersion, $xml->TokenId)) {
            $UserID = (string) $xml->UserID;
            $UserPass = (string) $xml->UserPass;
            $AppVersion = (string) $xml->AppVersion;
            $TokenId = (string) $xml->TokenId;
            $DeviceType = isset($xml->DType) ? (string) $xml->DType : 1;

            $appVersionStatus = DB::table('appversionstatus')
                ->where('AppVersionText', $AppVersion)
                ->where('AppType', $DeviceType)
                ->exists();

            if ($appVersionStatus) {
                $user = DB::table('customer')
                    ->where(function ($query) use ($UserID) {
                        $query->where('email', $UserID)
                            ->orWhere('mobile', $UserID);
                    })
                    ->where('password', $UserPass)
                    ->where('status', 'Active')
                    ->first();
                if ($user) {
                    if (empty($user->VerificationCode)) {
                        $userDetails = DB::table('customer')
                            ->select(
                                'name AS UserName',
                                'id AS UserId',
                                'email AS UserEmail',
                                'address AS UserAddress',
                                'area_id AS UserArea',
                                'city_id AS UserCity',
                                'party_type_id AS Usertype',
                                'password as UserPassword',
                                DB::raw('"askchg" as askchg'),
                                'status as UserActive',
                                'id as UserCode',
                                'mobile AS UserMobile',
                                DB::raw('"" as UserDealerCode'),
                                DB::raw('"" as UserDealerStatus'),
                                DB::raw('"token_id"  as TokenId'),
                                DB::raw('"UserCategory" as UserCategory'),
                                'state_id AS UserState',
                                'pincode AS UserPincode',
                                'gst AS UserGst',
                                'contact_person as ContactPerson',
                                'discount as PartyDiscount',
                                DB::raw('"UserSelectedType" AS Type'),
                                'id AS RCode'
                            )
                            ->where(function ($query) use ($UserID) {
                                $query->where('email', $UserID)
                                    ->orWhere('mobile', $UserID);
                            })
                            ->where('status', 'ACTIVE')
                            ->whereNull('deleted_at')
                            ->first();

                        if ($userDetails && $UserPass == $userDetails->UserPassword) {
                            $lastLoginDate = now();
                            DB::table('customer')
                                ->where(function ($query) use ($UserID) {
                                    $query->where('email', $UserID)
                                        ->orWhere('mobile', $UserID);
                                })
                                ->update(['token_id' => $TokenId, 'last_login_date' => $lastLoginDate]);

                            $xmlContent["code"] = 1;
                            $xmlContent["qty_multiplier"] = 10;
                            $xmlContent["min_qty_without_print"] = 10;
                            $xmlContent["min_qty_one_color_foil_print_group_11"] = 50;
                            $xmlContent["min_qty_one_color_foil_print"] = 80;
                            $xmlContent["min_qty_one_color_ink_print"] = 100;
                            $xmlContent["min_qty_two_color_ink_print"] = 200;

                            $xmlContent["min_retail_fp"] = 100;
                            $xmlContent["total_min_retail_fp"] = 300;
                            $xmlContent["min_retail_oi"] = 200;
                            $xmlContent["total_min_retail_oi"] = 600;
                            $xmlContent["min_retail_ti"] = 200;
                            $xmlContent["total_min_retail_ti"] = 600;
                            $xmlContent["retail_ti_extra"] = 2;
                            $xmlContent["retail_ep_extra"] = 2;

                            $xmlContent["pricelisturl"] = env('APP_URL') . "/storage/pricelist/";

                            $index = 0;
                            $xmlContent["data"][$index] = (array) $userDetails;
                            $userCode = $userDetails->UserId;
                            $index++;

                            $selectPriceList = DB::table('price_lists');
                            if ($user->UserCategory == 'RETAIL') {
                                $selectPriceList->where('PriceListCategory', $user->UserCategory);
                            }

                            $priceLists = $selectPriceList->get();
                            foreach ($priceLists as $priceRecord) {
                                $xmlContent["PriceList"][] = (array) $priceRecord;
                            }

                            // get coins
                            $coins = []; //DB::table('Spectacoin')
                            // ->where('Status', 'AVAILABLE')
                            // ->where('UserId', $userCode)
                            // ->get();
                            foreach ($coins as $coinRecord) {
                                // $xmlContent["Coins"][] = (array) $coinRecord;
                            }

                            // get schemes
                            $schemes = []; //DB::table('SpectaScheme')
                            // ->where('Status', 'ON')
                            // ->get();
                            foreach ($schemes as $schemeRecord) {
                                $xmlContent["scm"][] = (array) $schemeRecord;
                            }

                            return response()->json($xmlContent, 200, [], JSON_UNESCAPED_UNICODE);
                        } else {
                            return response()->json([
                                'code' => 0,
                                'Query' => "SELECT PartyName AS UserName, PartyConCode AS UserId, PartyEmail AS UserEmail, PartyAddress AS UserAddress, PartyArea AS UserArea, PartyCity AS UserCity, PartyType AS Usertype, UserPassword, askchg, UserActive, UserCode, "
                                    . "PartyMobileno AS UserMobile, UserDealerCode, UserDealerStatus, TokenId, UserCategory, PartyState AS UserState, PartyPinCode AS UserPincode, PartyFaxNo AS UserGst, ContactPerson, PartyDiscount, UserSelectedType As Type, ReferralCode AS RCode FROM PartyContact "
                                    . "WHERE (email = '$UserID' OR mobile = '$UserID') and password='$UserPass' and UserActive=1",
                                'Message' => 'Your account has not been approved. Please contact customer care for more details.'
                            ], 401);
                        }
                    } else {
                        return response()->json([
                            'code' => 8,
                            'Message' => 'Your mobile number and email are not verified, please verify your details before you may login.'
                        ], 401);
                    }
                } else {
                    return response()->json([
                        'code' => 0,
                        'query' => "SELECT * FROM PartyContact WHERE (PartyEmail = '$UserID' OR PartyMobileno = '$UserID') and UserPassword = '$UserPass'",
                        'Message' => 'Entered User Id or Password may be wrong.'
                    ], 401);
                }
            } else {
                return response()->json([
                    'code' => 2,
                    'Message' => 'Update Your App Version',
                    'query' => $appVersionStatus
                ], 400);
            }
        } else {
            return response()->json([
                'code' => 0,
                'Message' => 'XML content not found or incomplete.'
            ], 400);
        }
    }
}
