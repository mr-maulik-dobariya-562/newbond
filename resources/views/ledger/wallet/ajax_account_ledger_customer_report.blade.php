<?php

$totalOpeningAmount = 0;

foreach ($data['opening_data'] as $oIndex => $ov) {
    if ($ov->type == 'DEBIT') {
        $totalOpeningAmount -=  $ov->total_net_amt ;
    } else if ($ov->type == 'CREDIT') {
        $totalOpeningAmount +=  $ov->total_net_amt ;
    }
}
$closingAmt = $totalOpeningAmount;
$totalDebitAmt = 0;
$totalCreditAmt = 0;
$totalClosingAmt = $totalOpeningAmount;
$totalLoss = 0;
?>

<table class="table table-bordered ledger-table">
    <thead>
        <tr>
            <th>Code</th>
            <th>Date</th>
            <th>Particulars</th>
            <th>Txn Type</th>
            <th>Debit Amt.</th>
            <th>Credit Amt.</th>
            <th>Balance Amt.</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td></td>
            <td></td>
            <th>Opening</th>
            <?php if ($totalOpeningAmount < 0) { ?>
                <th><?= abs($totalOpeningAmount) ?></th>
                <?php $totalDebitAmt += abs($totalOpeningAmount) ?>
            <?php } else { ?>
                <th></th>
            <?php } ?>
            <?php if ($totalOpeningAmount > 0) { ?>
                <th><?= abs($totalOpeningAmount) ?></th>
                <?php $totalCreditAmt += abs($totalOpeningAmount) ?>
            <?php } else { ?>
                <th></th>
            <?php } ?>
            <td></td>
        </tr>
        <?php
        foreach ($data['data'] as $i => $v) {
            $closingAmt = number_format($closingAmt, 3, '.', '');
            $totalDebitAmt = number_format($totalDebitAmt, 3, '.', '');
            $totalClosingAmt = number_format($totalClosingAmt, 3, '.', '');
        ?>
            <?php $totalLoss += 0; ?>
            <tr class="change_color">
                <td>
                    <?= $i + 1; ?></a>
                </td>
                <td><?= !empty($v->date) ? date('d-m-Y', strtotime($v->date)) : ''; ?></td>
                <td><?= $v->type; ?></td>
                <td><?= $v->txn_name; ?></td>
                <?php if ($v->type == 'DEBIT') { ?>
                    <?php
                    $closingAmt -=  abs($v->total_net_amt);
                    $totalDebitAmt +=  abs($v->total_net_amt);
                    $totalClosingAmt -=  abs($v->total_net_amt);
                    ?>
                    <td>
                        <?= (isset($v->total_net_amt) && !empty($v->total_net_amt)) ? abs($v->total_net_amt) : 0 ?>
                    </td>
                    <td></td>
                    <td class="<?= ($closingAmt < 0) ? 'text-danger' : 'text-success' ?>"><?= sprintf("%.3f",abs($closingAmt)) ?> <?= ($closingAmt < 0) ? 'DR' : 'CR' ?></td>
                <?php } else if ($v->type == 'CREDIT') { ?>
                    <?php
                    $closingAmt +=  abs($v->total_net_amt);
                    $totalCreditAmt +=  abs($v->total_net_amt);
                    $totalClosingAmt +=  abs($v->total_net_amt);
                    ?>
                    <td></td>
                    <td>
                        <?= (isset($v->total_net_amt) && !empty($v->total_net_amt)) ? abs($v->total_net_amt) : 0 ?>
                    </td>
                    <td class="<?= ($closingAmt < 0) ? 'text-danger' : 'text-success' ?>"><?= sprintf("%.3f",abs($closingAmt)) ?> <?= ($closingAmt < 0) ? 'DR' : 'CR' ?></td>
                <?php }  ?>
            </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <th></th>
            <th></th>
            <th>Summary</th>
            <th></th>
            <th><?= $totalDebitAmt ?></th>
            <th><?= $totalCreditAmt ?></th>
            <th class="<?= ($totalClosingAmt < 0) ? 'text-danger' : 'text-success' ?>"><?= sprintf("%.3f",abs($totalClosingAmt)) ?> <?= ($totalClosingAmt < 0) ? 'DR' : 'CR' ?></th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th class="<?= ($totalClosingAmt < 0) ? 'text-danger' : 'text-success' ?>"><?= sprintf("%.3f",abs($totalClosingAmt)) ?> <?= ($totalClosingAmt < 0) ? 'DR' : 'CR' ?></th>
        </tr>
    </tfoot>
</table>