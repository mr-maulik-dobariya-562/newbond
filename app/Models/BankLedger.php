<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class BankLedger extends BaseModel
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
        $paymentQuery = call_user_func_array([self::class, $queryType], ['PAY', 'date', $fromDate, $toDate, $customer, $bank, $nother]);

        $q = "SELECT PAY.id, PAY.date,
                    'PAY' AS `code`,
                    B.`name` AS bank_name,
                    B.`id` AS bank_id,
                    PAY.`customer_id`,
                    C.`name` AS customer_name,
                    PAY.`amount` AS total_net_amt,
                    PAY.`remark` AS `remark`,
                    PAY.type
                FROM payments PAY
                LEFT JOIN banks B ON PAY.bank_id = B.id
                LEFT JOIN customer C ON C.id = PAY.customer_id
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

        if ($bank > 0 && $bank !== 'ALL') {
            $q .= " AND $table.bank_id = " . $bank . " ";
        }

        if ($customer > 0 && $customer !== 'ALL') {
            $q .= " AND $table.customer_id = $customer ";
        }

        return $q;
    }

    public static function getOpeningSearchQuery($table, $dateField, $fromDate, $toDate = null, $customer = 0, $bank = 0, $other = [])
    {
        $q = $fromDate ? " WHERE TRUE " : " WHERE FALSE ";
        if (!empty($fromDate)) {
            $q .= " AND DATE($table.$dateField) < DATE('" . $fromDate . "') ";
        }

        if ($bank !== 'ALL' && $bank > 0) {
            $q .= " AND $table.bank_id = " . $bank . " ";
        }

        if ($customer > 0 && $customer !== 'ALL') {
            $q .= " AND $table.customer_id = $customer ";
        }

        return $q;
    }
}
