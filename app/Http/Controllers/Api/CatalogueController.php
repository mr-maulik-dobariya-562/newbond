<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatalogueController extends Controller
{
    public function index(Request $request)
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
            $UserPass = $doc->getElementsByTagName("UserPass")->item(0)->nodeValue;
            $country = $doc->getElementsByTagName("country")->item(0)->nodeValue;

            if ($UserID == env('ADMIN_USER_ID') && $UserPass == env('ADMIN_USER_PASS')) {
                if (empty($country)) {
                    $country = '1';
                }

                $sql = "SELECT id AS Id, `image` AS URL, `name` AS Name, id AS OI, `item_group_id` AS GCode FROM catalogues WHERE `status`='ACTIVE' AND country_id = ? ORDER BY id";
                $catalogues = DB::select($sql, [$country]);

                if (empty($catalogues)) {
                    $sql = "SELECT id AS Id, `image` AS URL, `name` AS Name, id AS OI, `item_group_id` AS GCode FROM catalogues WHERE `status`='ACTIVE' AND country_id = 1 ORDER BY id";
                    $catalogues = DB::select($sql);
                }

                $xmlContent["pdf"] = $country == 'Kenya'
                    ? "https://spectacase.com/wp-content/uploads/2019/11/spectacase.pdf"
                    : "https://spectacase.com/wp-content/uploads/2019/09/Final-Book-2019.pdf";

                if (!empty($catalogues)) {
                    $xmlContent["code"] = 1;
                    $xmlContent["catalogueurl"] = "" . env("RAVI_URL") .'catalogue/';
                    $xmlContent["thumburl"] = "" . env("RAVI_URL") . 'catalogue/';
                    $xmlContent["data"] = $catalogues;

                    $groupSQL = "SELECT id AS GCode, group_name AS GName, ' ' AS Settings FROM item_group";
                    $groups = DB::select($groupSQL);

                    if (!empty($groups)) {
                        $xmlContent["GroupURLPrefix"] ="" . env("RAVI_URL") .'printGroupImage/';
                        $xmlContent["Groups"] = $groups;
                    }
                } else {
                    $xmlContent["code"] = 0;
                    $xmlContent["Message"] = "No Catalogue Found";
                }
            } else {
                $xmlContent["code"] = 0;
                $xmlContent["Message"] = "User Id Or password not match";
            }
        } else {
            $xmlContent["code"] = 0;
            $xmlContent["Message"] = "XML Length Not Found";
        }

        return response()->json($xmlContent);
    }
}
