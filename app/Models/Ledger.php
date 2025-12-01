<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Ledger extends BaseModel
{
    use HasFactory;

    public static function getLedgerReport($fromDate, $toDate, $customer = 0, $partyType = 0, $nother = [])
    {
        $data = self::getLedgerQuery(false, $fromDate, $toDate, $customer, $partyType, $nother);
        $openingData = self::getLedgerQuery(true, $fromDate, $toDate, $customer, $partyType, $nother);

        return [
            'data' => $data,
            'opening_data' => $openingData,
            'other' => $nother
        ];
    }

    public static function getLedgerQuery($isOpening, $fromDate, $toDate, $customer, $partyType, $nother)
    {
        $queryType = $isOpening ? 'getOpeningSearchQuery' : 'getSearchQuery';

        // Call the dynamic method correctly
        $estimateQuery = call_user_func_array([self::class, $queryType], ['EST', 'date', $fromDate, $toDate, $customer, $partyType, $nother]);
        $paymentQuery = call_user_func_array([self::class, $queryType], ['PAY', 'date', $fromDate, $toDate, $customer, $partyType, $nother]);

        $q = "SELECT EST.id, EST.date, 'EST' AS `code`, 'ESTIMATE' as `type`, CONCAT(C.`name`, ' - (', ci.`name`, ' - ', pt.`name`,')') AS customer_name, EST.`customer_id`,
                    Pd.`amount` AS total_net_amt,
                    CONCAT('purchase/edit/', EST.id) AS link
                FROM estimates EST
                LEFT JOIN estimate_details Pd ON EST.id = Pd.estimate_id
                LEFT JOIN customer C ON C.id = EST.customer_id
                LEFT JOIN cities ci ON ci.id = C.city_id
                LEFT JOIN party_types pt ON pt.id = C.party_type_id
                $estimateQuery

            UNION ALL
                SELECT PAY.id, PAY.date, 'PAY' AS `code`,`type`, CONCAT(C.`name`, ' - (', ci.`name`, ' - ', pt.`name`,')') AS customer_name, PAY.`customer_id`,
                    PAY.amount AS total_net_amt,
                    CONCAT('purchase_return/edit/', PAY.id) AS link
                FROM `payments` PAY
                LEFT JOIN customer C ON C.id = PAY.customer_id
                LEFT JOIN cities ci ON ci.id = C.city_id
                LEFT JOIN party_types pt ON pt.id = C.party_type_id
                $paymentQuery

                ORDER BY `date`";

        return DB::select(DB::raw($q)->getValue(DB::getQueryGrammar()));
    }

    public static function getSearchQuery($table, $dateField, $fromDate, $toDate, $customer = 0, $partyType = 0, $sqother = [])
    {
        $q = " WHERE TRUE ";
        if (!empty($toDate)) {
            $q .= " AND DATE($table.$dateField) <= DATE('" . $toDate . "')";
        }

        if (!empty($fromDate)) {
            $q .= " AND DATE($table.$dateField) >= DATE('" . $fromDate . "') ";
        }

        if ($partyType > 0 && $partyType !== 'ALL') {
            $q .= " AND C.party_type_id = " . $partyType . " ";
        }

        if ($customer > 0 && $customer !== 'ALL') {
            $q .= " AND $table.customer_id = $customer ";
        }

        return $q;
    }

    public static function getOpeningSearchQuery($table, $dateField, $fromDate, $toDate = null, $customer = 0, $partyType = 0, $other = [])
    {
        $q = $fromDate ? " WHERE TRUE " : " WHERE FALSE ";
        // $q = " WHERE TRUE ";
        if (!empty($fromDate)) {
            $q .= " AND DATE($table.$dateField) < DATE('" . $fromDate . "') ";
        }

        if ($partyType !== NULL && $partyType !== 'ALL' && $partyType > 0) {
            $q .= " AND C.party_type_id = " . $partyType . " ";
        }

        if ($customer > 0 && $customer !== 'ALL') {
            $q .= " AND $table.customer_id = $customer ";
        }
        return $q;
    }
}
