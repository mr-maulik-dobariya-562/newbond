<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CaseType;
use App\Models\Customer;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemDetail;
use App\Models\ItemGroup;
use App\Models\ItemGroupDetail;
use App\Models\ItemGroupPrice;
use App\Models\ItemGroupPrintExtra;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $xmlContent = array();
        $xmlContent["Message"] = "";
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
            $userID = $doc->getElementsByTagName("UserID")->item(0)->nodeValue;
            $userPass = $doc->getElementsByTagName("UserPass")->item(0)->nodeValue;

            if ($userID == env('ADMIN_USER_ID') && $userPass == env('ADMIN_USER_PASS')) {
                $sql = "SELECT name AS Name, PurcItemBasicRate As Rate, PurcItemModelCode AS MCode, PurcItemSize AS Size, PurcItemCategoriesCode AS CCode, PurcItemGroupCode AS GCode, PurcItemModelSizeCode AS SCode, packing AS Pkg, image AS Image, ExtraDisc AS Disc, RetailDisc AS RDisc FROM ItemListshow WHERE PurcItemMaterialType='Active'";
            } else {
                $user = DB::table('customer')
                    ->select('party_types.name as partyName', 'customer.password', 'customer.id as custId')
                    ->join('party_types', 'customer.party_type_id', '=', 'party_types.id')
                    ->where('email', $userID)
                    ->first();

                if ($user && $userPass == $user->password) {
                    $userCategory = $user->partyName;

                    if ($userCategory == 'Retail') {
                        $categories = ItemCategory::with('size')->get()->toArray();
                    }
                } else {
                    $xmlContent["code"] = 0;
                    $xmlContent["Message"] = "User ID and password do not match";
                    return response()->json($xmlContent, JSON_UNESCAPED_UNICODE);
                }
            }

            $categories = ItemCategory::with('size')->get()->toArray();
            if (!empty($categories)) {
                $xmlContent["CategoryURLPrefix"] = "" . env("RAVI_URL") . "/storage/itemCategory/";
                $xmlContent["Categories"] = $categories;
            }

            $size = Size::all();
            if (!empty($size)) {
                $xmlContent["Size"] = $size;
            }
            $xmlContent['CaseType'] = CaseType::select('id', 'title', 'sequence_number', 'image')->where('is_active', 1)->get();
            if (!empty($xmlContent['CaseType'])) {
                $xmlContent["CaseTypeURLPrefix"] = "" . env("RAVI_URL") . "case_type/";
            }
            $groups = ItemGroup::with('details', 'prices', 'prices.printType', 'details.printType')->get();
            $Groups = [];
            foreach ($groups as $key => $value) {
                $Groups[] = [
                    'GCode' => $value->id,
                    'GName' => $value->group_name,
                    'GST' => $value->gst,
                    'CaseType' => $value->case_type_id,
                ];
                $groupDetail = ItemGroupDetail::with('printType')->where('item_group_id', $value->id)->get();
                foreach ($groupDetail as $k => $v) {
                    if ($user->partyName == 'Retail') {
                        $Groups[$key]['retail'][] = [
                            'min' => $v->min_retail,
                            'max' => $v->total_retail,
                            'print_type' => $v->printType->name,
                            'print_type_id' => $v->printType->id
                        ];
                    } else if ($user->partyName == 'Dealer') {
                        $Groups[$key]['dealer'][] = [
                            'min' => $v->min_dealer,
                            'max' => $v->total_dealer,
                            'print_type' => $v->printType->name,
                            'print_type_id' => $v->printType->id
                        ];
                    }
                }
            }

            if (!empty($groups)) {
                $xmlContent["GroupURLPrefix"] = "" . env("RAVI_URL") . 'itemGroup/';
                $xmlContent["Groups"] = $Groups;
            }

            $item = Item::with([
                'itemDetails' => function ($q) {
                    $q->where('checkbox', '1');
                }
            ])->orWhere('active_type', 'Active')->get();
            $items = [];
            foreach ($item as $itemvalue) {
                foreach ($itemvalue->itemDetails as $v) {
                    $items[] = [
                        'item_id' => $itemvalue->id,
                        'print_type_id' => $v->printType->id,
                    ];
                }
            }

            $products = [];
            foreach ($items as $value) {
                $value = $value;
                $item_id = $value['item_id'];
                $print_type_id = $value['print_type_id'];
                $customer = Customer::with('partyType')->find($user->custId);
                $item = ItemDetail::with('item', 'item.categories')->where('item_id', $item_id)->where('print_type_id', $print_type_id)->first();
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

                $products[] = [
                    "item_id" => $item_id,
                    "print_type_id" => $item->printType->id,
                    "Name" => $item->item->name,
                    "Rate" => $rate,
                    "Disc" => $discount,
                    "Image" => "" . env("RAVI_URL") . 'item/' . $item->item->image,
                    "size_id" => $item->item->categories?->size_id,
                    "Size" => $item->printType->name,
                    "categories_id" => $item->item->categories_id,
                    "group_id" => $item->item->categories->item_group_id,
                    "SCode" => "",
                    "Pkg" => $item->item->packing,
                    "RDisc" => ""
                ];
            }

            if (!empty($products)) {
                $xmlContent["code"] = 1;
                $xmlContent["ProductURLPrefix"] = "" . env("RAVI_URL") . 'item/';
                $xmlContent["Products"] = $products;
                return response()->json($xmlContent, JSON_UNESCAPED_UNICODE);
            } else {
                $xmlContent["code"] = 7;
                $xmlContent["Message"] = "No products available for you.\nPlease contact your dealer.";
                return response()->json($xmlContent, JSON_UNESCAPED_UNICODE);
            }
        } else {
            $xmlContent["code"] = 0;
            $xmlContent["Message"] = "Length Not Found";
            return response()->json($xmlContent, JSON_UNESCAPED_UNICODE);
        }
    }
}
