<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    public function index()
    {
        $xmlContent = [];

        // Query to get country & city list
        $cityRecords = City::with('state')->get()->map(function ($city) {
            return [
                'city' => $city?->name,
                'state' => $city->state?->name,
            ];
        });

        if (count($cityRecords) > 0) {
            $xmlContent["code"] = 1;
            $xmlContent["data"] = $cityRecords;
        } else {
            $xmlContent["code"] = 2;
            $xmlContent["Message"] = "Update Your App Version";
        }

        return response()->json($xmlContent, JSON_UNESCAPED_UNICODE);
    }
}
