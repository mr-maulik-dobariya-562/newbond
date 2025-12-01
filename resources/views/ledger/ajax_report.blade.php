<?php

$totalOpeningFine = 0;

$customers = [];
foreach ($data['data'] as $di => $dv) {
    if (!in_array($dv->customer_id, $customers)) {
        $customers[] = $dv->customer_id;
    }
}
foreach ($data['opening_data'] as $di => $dv) {
    if (!in_array($dv->customer_id, $customers)) {
        $customers[] = $dv->customer_id;
    }
}

$data['filtered_data'] = [];
$totalDebitAmt = 0;
$totalCreditAmt = 0;
$totalClosingAmt = 0;
$totalOpeningAmt = 0;

$totalLoss = 0;

foreach ($customers as $abc => $c) {
    $closingAmt = 0;
    $openingAmt = 0;
    $date = '';
    $customerName = '';
    $customerId = 0;
    $totalDebitAmt2 = 0;
    $totalCreditAmt2 = 0;

    $loss = 0;

    $isBank = false;
    $bank['bank_name'] = [];


    foreach ($data['data'] as $di => $v) {
        $closingAmt = number_format($closingAmt, 3, '.', '');
        $totalDebitAmt2 = number_format($totalDebitAmt2, 3, '.', '');
        $totalCreditAmt2 = number_format($totalCreditAmt2, 3, '.', '');

        if ($v->customer_id == $c) {
            $customerId = $c;
            $date = $v->date;
            $customerName = $v->customer_name;

            if ($v->code == 'EST' || ($v->code == 'PAY' && $v->type == 'DEBIT')) {
                $closingAmt -= (isset($v->total_net_amt) && !empty($v->total_net_amt)) ? abs($v->total_net_amt) : 0;
                $totalDebitAmt2 += (isset($v->total_net_amt) && !empty($v->total_net_amt)) ? abs($v->total_net_amt) : 0;
                $totalClosingAmt -= (isset($v->total_net_amt) && !empty($v->total_net_amt)) ? abs($v->total_net_amt) : 0;
            } else if ($v->code == 'PAY' && $v->type == 'CREDIT') {
                $closingAmt += (isset($v->total_net_amt) && !empty($v->total_net_amt)) ? abs($v->total_net_amt) : 0;
                $totalCreditAmt2 += (isset($v->total_net_amt) && !empty($v->total_net_amt)) ? abs($v->total_net_amt) : 0;
                $totalClosingAmt += (isset($v->total_net_amt) && !empty($v->total_net_amt)) ? abs($v->total_net_amt) : 0;
            }
        }
    }

    foreach ($data['opening_data'] as $odi => $v) {
        $openingAmt = number_format($openingAmt, 3, '.', '');
        $totalOpeningAmt = number_format($totalOpeningAmt, 3, '.', '');
        $totalOpeningFine = number_format($totalOpeningFine, 3, '.', '');
        if ($c == $v->customer_id) {
            $customerId = $c;
            $date = $v->date;
            $customerName = $v->customer_name;
            if ($v->code == 'EST' || ($v->code == 'PAY' && $v->type == 'DEBIT')) {
                $openingAmt -= (isset($v->total_net_amt) && !empty($v->total_net_amt)) ? abs($v->total_net_amt) : 0;
                $totalOpeningAmt -= (isset($v->total_net_amt) && !empty($v->total_net_amt)) ? abs($v->total_net_amt) : 0;
            } else if ($v->code == 'PAY' && $v->type == 'CREDIT') {
                $openingAmt += (isset($v->total_net_amt) && !empty($v->total_net_amt)) ? abs($v->total_net_amt) : 0;
                $totalOpeningAmt += (isset($v->total_net_amt) && !empty($v->total_net_amt)) ? abs($v->total_net_amt) : 0;
            }
        }
    }

    $totalDebitAmt += $totalDebitAmt2;
    $totalCreditAmt += $totalCreditAmt2;

    $bank_name = "";
    if ($data['other']['master_type'] == "bank" && !empty($bank['bank_name'][$abc])) {
        $bank_name = $bank['bank_name'][$abc];
    }
    $totalLoss += $loss;
    $data['filtered_data'][] = [
        'date' => $dv->date,
        'type' => '',
        'customer_name' => $customerName,
        'party_id' => $customerId,
        'opening_amt' => $openingAmt,
        'total_debit_amt' => $totalDebitAmt2,
        'total_credit_amt' => $totalCreditAmt2,
        'closing_amt' => $closingAmt,
        'loss' => $loss,
        'isBank' => $isBank,
        'bank_name' => $bank_name
    ];
}
?>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th></th>
            <th></th>
            <th class="text-center p-1">Opening Amount</th>
            <th class="text-center p-1">Debit Amount</th>
            <th class="text-center p-1">Credit Amount</th>
            <th class="text-center p-1">Closing Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php
// pre($data['filtered_data']);exit;
foreach ($data['filtered_data'] as $fdi => $fdv) {
    $url = '';
        ?>
        <tr>
            <td><a href="{{ route('ledger.ledgerReportByCustomer', $fdv['party_id']) }}" target="_blank">
                    <?= isset($fdv['customer_name']) && !empty($fdv['customer_name']) ? $fdv['customer_name'] : ""; ?>
                </a></td>
            <td>
                <?= $fdv['loss'] ?>
            </td>
            <td>
                <?= $fdv['opening_amt'] ?>
            </td>
            <td>
                <?= abs($fdv['total_debit_amt']) ?>
            </td>
            <td>
                <?= abs($fdv['total_credit_amt']) ?>
            </td>
            <td>
                <?= number_format($fdv['closing_amt'], 2, '.', '') + number_format($fdv['opening_amt'], 2, '.', '') ?>
            </td>
        </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <th>Summary</th>
            <th>
                <?= $totalLoss ?>
            </th>
            <th>
                <?= $totalOpeningAmt ?>
            </th>
            <th>
                <?= $totalDebitAmt ?>
            </th>
            <th>
                <?= $totalCreditAmt ?>
            </th>
            <th>
                <?= number_format($totalClosingAmt, 2, '.', '') + number_format($totalOpeningAmt, 2, '.', '') ?>
            </th>
        </tr>
    </tfoot>
</table>