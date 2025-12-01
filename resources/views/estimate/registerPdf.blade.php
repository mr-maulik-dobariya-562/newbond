<!DOCTYPE html>
<html>

<head>
    <title>Gate Pass</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h1 {
            margin: 0;
        }

        .details,
        .items {
            width: 100%;
            border-collapse: collapse;
            /* margin-bottom: 20px; */
        }

        .details td,
        .items td,
        .items th {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .details td {
            border: none;
        }

        @media print {
            @page {
                size: a4 Landscape;
            }
        }
    </style>
</head>

<body>
    <div style="height: 100%;width: 100%;justify-content: center;">
        <h3 style="text-align: center;padding-top: 50px;"></h3>
        <table class="items">
            <thead>
                <tr>
                    <th colspan="8" style="border: none;padding-left: 400px">
                        <h2>Estimate Register</h2>
                    </th>
                    <td colspan="1" style="border: none;text-align: right;">Period Date : {{ date('d-m-Y') .' To '. date('d-m-Y') }}</td>
                </tr>
                <tr>
                    <th colspan="9" style="border: none;text-align: right;">In SPECTA CASE</th>
                </tr>
                <tr style="background-color: red;padding-bottom: 0px">
                    <th style="border-top: none;border-right: none;border-left: none;text-align: center;">Date</th>
                    <th style="border-top: none;border-right: none;border-left: none;text-align: center;">PartyName</th>
                    <th style="border-top: none;border-right: none;border-left: none;text-align: center;"></th>
                    <th style="border-top: none;border-right: none;border-left: none;text-align: center;"></th>
                    <th style="border-top: none;border-right: none;border-left: none;text-align: center;">Basic Amt</th>
                    <th style="border-top: none;border-right: none;border-left: none;text-align: center;">Disc</th>
                    <th style="border-top: none;border-right: none;border-left: none;text-align: center;">Other</th>
                    <th style="border-top: none;border-right: none;border-left: none;text-align: center;">Redeem</th>
                    <th style="border-top: none;border-right: none;border-left: none;text-align: center;">Net Amt</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $allAmtTotal = 0;
                $allDisTotal = 0;
                $allNetTotal = 0;
                $allRedeemTotal = 0;
                $allOtherTotal = 0;
                ?>
                @foreach ($estimate as $data)
                <tr>
                    <td style="border-top: none;border-right: none;border-left: none;text-align: left;">{{ $data->customer->name }}</td>
                    <td style="border-top: none;border-right: none;border-left: none;text-align: right;"></td>
                    <td style="border-top: none;border-right: none;border-left: none;text-align: center;">--</td>
                    <td colspan="2" style="border-top: none;border-right: none;border-left: none;text-align: center;"></td>
                    <!-- <td style="border-top: none;border-right: none;border-left: none;text-align: center;"></td> -->
                    <td style="border-top: none;border-right: none;border-left: none;text-align: center;"></td>
                    <td style="border-top: none;border-right: none;border-left: none;text-align: center;"></td>
                    <td style="border-top: none;border-right: none;border-left: none;text-align: center;"></td>
                    <td style="border-top: none;border-right: none;border-left: none;text-align: center;"></td>
                </tr>
                <?php
                $amtTotal = 0;
                $disTotal = 0;
                $netTotal = 0;
                $redeemTotal = 0;
                $otherTotal = 0;
                ?>
                @foreach ($data->estimateDetail as $detail)
                <tr>
                    <td style="border: none;text-align: right;">{{ $data->estimate_code }} {{ date('d-m-Y', strtotime($detail->date)) }}</td>
                    <td style="border:none;text-align: center;">{{ $data->customer->name }}</td>
                    <td style="border: none;text-align: center;"></td>
                    <td style="border: none;text-align: center;">{{ $detail->invoice_type }}</td>
                    <td style="border: none;text-align: center;">{{ $detail->total_amount }}</td>
                    <td style="border: none;text-align: center;">{{ $detail->discount_amount }}</td>
                    <td style="border: none;text-align: center;">{{ $detail->other_charge }}</td>
                    <td style="border: none;text-align: center;">{{ $detail->redeem_coin }}</td>
                    <td style="border: none;text-align: center;">{{ $detail->net_amount }}</td>
                </tr>
                <?php
                $amtTotal += $detail->total_amount;
                $disTotal += $detail->discount_amount;
                $netTotal += $detail->net_amount;
                $redeemTotal += $detail->redeem_coin;
                $otherTotal += $detail->other_charge;
                ?>
                @endforeach
                <tr>
                    <td style="border: none;text-align: right;"></td>
                    <td style="border:none;text-align: right;"></td>
                    <td style="border: none;text-align: center;"></td>
                    <td style="border: none;text-align: center;"></td>
                    <td style="border: none;text-align: center;"><b>{{ round($amtTotal) }}</b></td>
                    <td style="border: none;text-align: center;"><b>{{ round($disTotal) }}</b></td>
                    <td style="border: none;text-align: center;"><b>{{ round($otherTotal) }}</b></td>
                    <td style="border: none;text-align: center;"><b>{{ round($redeemTotal) }}</b></td>
                    <td style="border: none;text-align: center;"><b>{{ round($netTotal) }}</b></td>
                </tr>
                <?php
                $allAmtTotal += $amtTotal;
                $allDisTotal += $disTotal;
                $allNetTotal += $netTotal;
                $allRedeemTotal += $redeemTotal;
                $allOtherTotal += $otherTotal;
                ?>
                @endforeach
                <tr>
                    <td style="border: none;text-align: right;"></td>
                    <td style="border:none;text-align: right;"></td>
                    <td style="border: none;text-align: center;"></td>
                    <td style="border: none;text-align: center;"></td>
                    <td style="border: none;text-align: center;"><b>{{ round($allAmtTotal) }}</b></td>
                    <td style="border: none;text-align: center;"><b>{{ round($allDisTotal) }}</b></td>
                    <td style="border: none;text-align: center;"><b>{{ round($allOtherTotal) }}</b></td>
                    <td style="border: none;text-align: center;"><b>{{ round($allRedeemTotal) }}</b></td>
                    <td style="border: none;text-align: center;"><b>{{ round($allNetTotal) }}</b></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
<script>
    window.print();
</script>

</html>
