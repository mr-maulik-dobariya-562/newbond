<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class WalletLedger extends BaseModel
{
    use HasFactory;

    public static function getLedgerReport($fromDate, $toDate, $customer = 0, $bank = 0, $nother = [])
    {
        $data = self::getLedgerQuery(false, $fromDate, $toDate, $customer, $bank, $nother);
        $openingData = self::getLedgerQuery(true, $fromDate, $toDate, $customer, $bank, $nother);

        return [
            'data' => $data,
            'opening_data' => $openingData,
            'other' => $nother
        ];
    }

    public static function getLedgerQuery($isOpening, $fromDate, $toDate, $customer, $bank, $nother)
    {
        $queryType = $isOpening ? 'getOpeningSearchQuery' : 'getSearchQuery';

        // Call the dynamic method correctly
        $paymentQuery = call_user_func_array([self::class, $queryType], ['wallet', 'date', $fromDate, $toDate, $customer, $bank, $nother]);

        $q = "SELECT wallet.id, wallet.date,
                    'wallet' AS `code`,
                    wallet.user_id AS customer_id,
                    CONCAT(C.`name`, ' - (', Ci.`name`, ' - ', pt.`name`,')') AS customer_name,
                    wallet.`amount` AS total_net_amt,
                    wallet.type,
                    txn.name AS txn_name
                FROM wallets wallet
                LEFT JOIN txn_types txn ON wallet.txn_type_id = txn.id
                LEFT JOIN customer C ON C.id = wallet.user_id
                LEFT JOIN cities Ci ON Ci.id = C.city_id
                LEFT JOIN party_types pt ON pt.id = C.party_type_id
                $paymentQuery
                ORDER BY `date`";

        return DB::select(DB::raw($q)->getValue(DB::getQueryGrammar()));
    }

    public static function getSearchQuery($table, $dateField, $fromDate, $toDate, $customer = 0, $bank = 0, $sqother = [])
    {
        $q = " WHERE TRUE ";
        if (!empty($toDate)) {
            $q .= " AND DATE($table.$dateField) <= DATE('" . $toDate . "')";
        }

        if (!empty($fromDate)) {
            $q .= " AND DATE($table.$dateField) >= DATE('" . $fromDate . "') ";
        }

        // if ($bank > 0 && $bank !== 'ALL') {
        //     $q .= " AND $table.bank_id = " . $bank . " ";
        // }

        if ($customer > 0 && $customer !== 'ALL') {
            $q .= " AND $table.user_id = $customer ";
        }

        return $q;
    }

    public static function getOpeningSearchQuery($table, $dateField, $fromDate, $toDate = null, $customer = 0, $bank = 0, $other = [])
    {
        $q = $fromDate ? " WHERE TRUE " : " WHERE FALSE ";
        if (!empty($fromDate)) {
            $q .= " AND DATE($table.$dateField) < DATE('" . $fromDate . "') ";
        }

        // if ($bank !== 'ALL' && $bank > 0) {
        //     $q .= " AND $table.bank_id = " . $bank . " ";
        // }

        if ($customer > 0 && $customer !== 'ALL') {
            $q .= " AND $table.user_id = $customer ";
        }

        return $q;
    }
}
