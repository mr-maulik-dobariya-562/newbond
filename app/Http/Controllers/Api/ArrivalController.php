<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ArrivalController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->getContent();

        if (empty($data)) {
            // Handle empty data scenario here if needed
        }

        $xml = simplexml_load_string($data);

        $UserID = (string)$xml->UserID;
        $UserPass = (string)$xml->UserPass;
        $country = (string)$xml->country;

        if ($UserID == env('ADMIN_USER_ID') && $UserPass == env('ADMIN_USER_PASS')) {
            if (empty($country)) {
                $newArrivals = DB::table('arrivals')
                    ->select('id as Id', 'image as URL', 'name as Name', 'sequence as OI')
                    ->where('status', 'Active')
                    ->where('branch_id', 1)
                    ->orderBy('sequence')
                    ->get();
            } else {
                $newArrivals = DB::table('arrivals')
                    ->select('id as Id', 'image as URL', 'name as Name', 'sequence as OI')
                    ->where('country_id', $country)
                    ->where('status', 'Active')
                    ->where('branch_id', 1)
                    ->orderBy('sequence')
                    ->get();
            }
        } else {
            $user = DB::table('customer')
                ->where('branch_id', 1)
                ->where('email', $UserID)
                ->first();

            if ($user && $UserPass == $user->password) {
                if (empty($country)) {
                    $newArrivals = DB::table('arrivals')
                        ->select('id as Id', 'image as URL', 'name as Name', 'sequence as OI')
                        ->where('status', 'Active')
                        ->where('branch_id', 1)
                        ->orderBy('sequence')
                        ->get();
                } else {
                    $newArrivals = DB::table('arrivals')
                        ->select('id as Id', 'image as URL', 'name as Name', 'sequence as OI')
                        ->where('status', 'Active')
                        ->where('branch_id', session('branch_id'))
                        ->where('country_id', $country)
                        ->orderBy('sequence')
                        ->get();
                }
            } else {
                return response()->json([
                    'code' => 0,
                    'Message' => 'user id password not match'
                ], 200, [], JSON_UNESCAPED_UNICODE);
            }
        }

        if ($newArrivals->isNotEmpty()) {
            return response()->json([
                'code' => 1,
                'NewArrivalurl' => "" . env("RAVI_URL") .'arrival/',
                'thumburl' => "" . env("RAVI_URL") .'arrival/',
                'data' => $newArrivals
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return response()->json([
                'code' => 0,
                'Message' => 'No New Arrival'
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
