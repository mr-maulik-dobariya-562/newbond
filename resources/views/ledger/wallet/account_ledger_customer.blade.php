@extends("Layouts.app")

@section("title", "Wallet Ledger")

@section("header")
<style>
    #nprogress .bar {
        z-index: 2000;
    }

    #nprogress .peg {
        box-shadow: 0 0 10px #29d, 0 0 5px #29d;
    }
</style>
<div class="page-header d-print-none">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                Manage Order
            </div>
            <h2 class="page-title">
                Wallet Ledger
            </h2>
        </div>
    </div>
</div>
@endsection
@section("content")
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Account Wallet Ledger</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <label>From Date</label>
                        <input type="date" value="" class="form-control from" id="from_date" />
                    </div>
                    <div class="col-md-2">
                        <label>To Date</label>
                        <input type="date" value="" class="form-control to" id="to_date" />
                    </div>
                    <div class="col-md-2 mt-3">
                        <button class="btn btn-primary" id="search_btn" type="button">Search</button>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive" id="set_table_here">
                                    <?php
                                    $totalOpeningAmount = 0;
                                    foreach ($data['opening_data'] as $oIndex => $ov) {
                                        if ($ov->type == 'DEBIT') {
                                            $totalOpeningAmount -=  abs($ov['total_net_amt']);
                                        } else if ($ov->type == 'CREDIT') {
                                            $totalOpeningAmount +=  abs($ov['total_net_amt']);
                                        }
                                    }

                                    $closingAmt       = $totalOpeningAmount;
                                    $totalDebitAmt    = 0;
                                    $totalCreditAmt   = 0;
                                    $totalClosingAmt  = $totalOpeningAmount;
                                    $totalLoss        = 0;
                                    ?>

                                    <table class="table table-bordered ledger-table">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th>Debit</th>
                                                <th>Credit</th>
                                                <th>Closing</th>
                                            </tr>
                                            <tr>
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
                                                <th>Opening</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>

                                                <!--here-->
                                                <?php if ($totalOpeningAmount < 0) { ?>
                                                    <?php $totalDebitAmt += abs($totalOpeningAmount) ?>
                                                    <td></td>
                                                    <th>
                                                        <?= abs($totalOpeningAmount) ?>
                                                    </th>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                <?php } ?>
                                            </tr>
                                            <?php

                                            $grossTotal = 0;
                                            $lessTotal = 0;
                                            $netTotal = 0;
                                            $rateTotal = 0;
                                            $purityTotal = 0;
                                            foreach ($data['data'] as $i => $v) {
                                                $isVadharoGhatado = "";
                                                $closingAmt       = number_format($closingAmt, 3, '.', '');
                                                $totalDebitAmt    = number_format($totalDebitAmt, 3, '.', '');
                                                $totalClosingAmt  = number_format($totalClosingAmt, 3, '.', '');
                                            ?>
                                                <tr class="change_color">
                                                    <td>
                                                        <?php if (!empty($v->date)) {
                                                            echo date('d-m-Y', strtotime($v->date));
                                                        } ?>
                                                    </td>
                                                    <td>
                                                        <?= $v->type; ?>
                                                    </td>
                                                    <td>
                                                        <?= $v->txn_name; ?>
                                                    </td>
                                                    <?php if ($v->type == 'DEBIT') { ?>
                                                        <?php
                                                        $closingAmt -= (isset($v->total_net_amt) && !empty($v->total_net_amt)) ? abs($v->total_net_amt) : 0;
                                                        $totalDebitAmt += (isset($v->total_net_amt) && !empty($v->total_net_amt)) ? abs($v->total_net_amt) : 0;
                                                        $totalClosingAmt -= (isset($v->total_net_amt) && !empty($v->total_net_amt)) ? abs($v->total_net_amt) : 0;
                                                        ?>
                                                        <td>
                                                            <?= (isset($v->total_net_amt) && !empty($v->total_net_amt)) ? abs($v->total_net_amt) : 0 ?>
                                                        </td>
                                                        <td></td>
                                                        <td class="<?= ($closingAmt < 0) ? 'text-danger' : 'text-success' ?>"><?= sprintf("%.3f",abs($closingAmt)) ?> <?= ($closingAmt < 0) ? 'DR' : 'CR' ?></td>
                                                    <?php } else if ($v->type == 'CREDIT') { ?>
                                                        <?php
                                                        $closingAmt += (isset($v->total_net_amt) && !empty($v->total_net_amt)) ? abs($v->total_net_amt) : 0;
                                                        $totalCreditAmt += (isset($v->total_net_amt) && !empty($v->total_net_amt)) ? abs($v->total_net_amt) : 0;
                                                        $totalClosingAmt += (isset($v->total_net_amt) && !empty($v->total_net_amt)) ? abs($v->total_net_amt) : 0;
                                                        ?>
                                                        <td></td>
                                                        <td>
                                                            <?= (isset($v->total_net_amt) && !empty($v->total_net_amt)) ? abs($v->total_net_amt) : 0 ?>
                                                        </td>
                                                        <td class="<?= ($closingAmt < 0) ? 'text-danger' : 'text-success' ?>"><?= sprintf("%.3f",abs($closingAmt)) ?> <?= ($closingAmt < 0) ? 'DR' : 'CR' ?></td>
                                                    <?php } ?>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>

                                                <th>Summary</th>
                                                <th>
                                                    <?= $totalDebitAmt ?>
                                                </th>
                                                <th>
                                                    <?= $totalCreditAmt ?>
                                                </th>
                                                <th class="<?= ($totalClosingAmt < 0) ? 'text-danger' : 'text-success' ?>"><?= sprintf("%.3f",abs($totalClosingAmt)) ?> <?= ($totalClosingAmt < 0) ? 'DR' : 'CR' ?></th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th class="<?= ($totalClosingAmt < 0) ? 'text-danger' : 'text-success' ?>"><?= sprintf("%.3f",abs($totalClosingAmt)) ?> <?= ($totalClosingAmt < 0) ? 'DR' : 'CR' ?></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6"></div>
            </div>
        </div>
        <!-- /.card-body -->
    </div>
</div>
@endsection

@push("javascript")
<?php $time = time(); ?>
<script class="javascript">
    $(document).ready(function() {
        $('#search_btn').click(search);
        var customer = <?= $customer; ?>

        function search() {
            var fromDate = $('#from_date').val();
            var toDate = $('#to_date').val();
            $.ajax({
                beforeSend: function() {
                    $("#set_table_here").html("Please Wait...");
                },
                url: "{{ route('wallet-ledger.getLedgerCustomerReport') }}",
                method: 'POST',
                showLoader: true,
                data: {
                    fromDate: fromDate,
                    toDate: toDate,
                    customer: customer,
                },
                success: function(data) {
                    $("#set_table_here").html(data);
                    $(".varification").each(function() {
                        IsChecked(this);
                    });
                },
            });
        }
    });
</script>
@endpush