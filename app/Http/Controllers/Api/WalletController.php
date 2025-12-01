<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class WalletController extends Controller
{

    public function index(Request $request)
    {
        $data = $request->getContent();

        if (empty($data)) {
            return response()->json([
                'code' => 0,
                'Message' => 'No data received'
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }

        $xml = simplexml_load_string($data);

        $UserID = (string)$xml->UserID;
        $UserPass = (string)$xml->UserPass;
        $AppVersion = (string)$xml->AppVersion;
        $startDate = (string)$xml->start;
        $endDate = (string)$xml->end;

        // Check for App Version
        $appVersionStatus = DB::table('appversionstatus')
            ->where('AppVersionText', '<=', $AppVersion)
            ->exists();

        if (!$appVersionStatus) {
            return response()->json([
                'code' => 2,
                'Message' => 'Update Your App Version'
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }

        // Check for User Credentials
        $user = DB::table('customer')
            ->select('status as UserActive', 'id as PartyConCode', 'password')
            ->where('email', $UserID)
            ->first();

        if (!$user || $UserPass != $user->password) {
            return response()->json([
                'code' => 0,
                'Message' => 'User Email And Password Not Match'
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }

        if ($user->UserActive != 'ACTIVE') {
            return response()->json([
                'code' => 0,
                'Message' => 'User Is Not Active'
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }

        $PartyConCode = $user->PartyConCode;

        // Get coins
        $coins = DB::table('wallets')
            ->where('user_id', $PartyConCode)
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get();

        // Get wallet transactions
        $walletQuery = DB::table('wallets')->where('user_id', $PartyConCode);

        if (!empty($startDate) && !empty($endDate)) {
            $walletQuery->whereBetween('date', [
                date('Y-m-d', strtotime($startDate)),
                date('Y-m-d', strtotime($endDate))
            ]);
        }

        $wallet = $walletQuery->get();

        if ($wallet->isEmpty()) {
            return response()->json([
                'code' => 0,
                'Message' => 'Wallet transactions Not Found'
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json([
            'code' => 1,
            'Coins' => $coins,
            'wallet' => $wallet
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
