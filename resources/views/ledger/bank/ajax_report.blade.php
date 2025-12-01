<?php
$totalOpeningFine = 0;

$bank_id = [];
foreach ($data['data'] as $di => $dv) {
    if (!in_array($dv->bank_id, $bank_id)) {
        $bank_id[] = $dv->bank_id;
    }
}
foreach ($data['opening_data'] as $di => $dv) {
    if (!in_array($dv->bank_id, $bank_id)) {
        $bank_id[] = $dv->bank_id;
    }
}

$data['filtered_data'] = [];
$totalDebitAmt         = 0;
$totalCreditAmt        = 0;
$totalClosingAmt       = 0;
$totalOpeningAmt       = 0;

foreach ($bank_id as $abc => $c) {
    $closingAmt       = 0;
    $openingAmt       = 0;
    $date             = '';
    $bankName     = '';
    $bankId       = 0;
    $totalDebitAmt2   = 0;
    $totalCreditAmt2  = 0;

    foreach ($data['data'] as $di => $v) {
        $closingAmt  = number_format($closingAmt, 3, '.', '');
        $totalDebitAmt2   = number_format($totalDebitAmt2, 3, '.', '');
        $totalCreditAmt2  = number_format($totalCreditAmt2, 3, '.', '');

        if ($v->bank_id == $c) {
            $bankId    = $c;
            $date      = $v->date;
            $bankName  = $v->bank_name;

            if ($v->type == 'DEBIT') {
                $closingAmt -=  abs($v->total_net_amt);
                $totalDebitAmt2 +=  abs($v->total_net_amt);
                $totalClosingAmt -=  abs($v->total_net_amt);
            } else if ($v->type == 'CREDIT') {
                $closingAmt +=  abs($v->total_net_amt);
                $totalCreditAmt2 +=  abs($v->total_net_amt);
                $totalClosingAmt +=  abs($v->total_net_amt);
            }
        }
    }

    foreach ($data['opening_data'] as $odi => $v) {
        $openingAmt       = number_format($openingAmt, 3, '.', '');
        $totalOpeningAmt  = number_format($totalOpeningAmt, 3, '.', '');
        if ($c == $v->bank_id) {
            $bankId    = $c;
            $date      = $v->date;
            $bankName  = $v->bank_name;
            if ($v->type == 'DEBIT') {
                $openingAmt -=  abs($v->total_net_amt);
                $totalOpeningAmt -=  abs($v->total_net_amt);
            } else if ($v->type == 'CREDIT') {
                $openingAmt +=  abs($v->total_net_amt);
                $totalOpeningAmt +=  abs($v->total_net_amt);
            }
        }
    }

    $totalDebitAmt += $totalDebitAmt2;
    $totalCreditAmt += $totalCreditAmt2;

    $data['filtered_data'][] = [
        'customer_name'     => $bankName,
        'party_id'          => $bankId,
        'opening_amt'       => $openingAmt,
        'total_debit_amt'   => $totalDebitAmt2,
        'total_credit_amt'  => $totalCreditAmt2,
        'closing_amt'       => $closingAmt,
    ];
}
?>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th></th>
            <th class="text-center p-1">Opening Amount</th>
            <th class="text-center p-1">Debit Amount</th>
            <th class="text-center p-1">Credit Amount</th>
            <th class="text-center p-1">Closing Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($data['filtered_data'] as $fdi => $fdv) {
            $url = '';
        ?>
            <tr>
                <td><a href="{{ route('bank-ledger.ledgerReportByCustomer', $fdv['party_id']) }}" target="_blank">
                        <?= isset($fdv['customer_name']) && !empty($fdv['customer_name']) ? $fdv['customer_name'] : ""; ?>
                    </a></td>
                <td>
                    <?= $fdv['opening_amt'] ?>
                </td>
                <td>
                    <?= abs($fdv['total_debit_amt']) ?>
                </td>
                <td>
                    <?= abs($fdv['total_credit_amt']) ?>
                </td>
                <?php $closing_amt = number_format($fdv['closing_amt'], 2, '.', '') + number_format($fdv['opening_amt'], 2, '.', '') ?>
                <td class="<?= ($closing_amt < 0) ? 'text-danger' : 'text-success' ?>"><?= sprintf("%.3f", abs($closing_amt)) ?> <?= ($closing_amt < 0) ? 'DR' : 'CR' ?></td>
            </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <th>Summary</th>
            <th>
                <?= $totalOpeningAmt ?>
            </th>
            <th>
                <?= $totalDebitAmt ?>
            </th>
            <th>
                <?= $totalCreditAmt ?>
            </th>
            <?php $closing = number_format($totalClosingAmt, 2, '.', '') + number_format($totalOpeningAmt, 2, '.', ''); ?>
            <th class="<?= ($closing < 0) ? 'text-danger' : 'text-success' ?>"><?= sprintf("%.3f", abs($closing)) ?> <?= ($closing < 0) ? 'DR' : 'CR' ?></th>
        </tr>
    </tfoot>
</table>