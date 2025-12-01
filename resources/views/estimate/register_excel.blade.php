<table>
    <thead>
        <tr>
            <th colspan="9" rowspan="2" style="text-align: center;background-color: red">
                <h1 style="height: 50px;">Estimate Register</h1>
            </th>
        </tr>
        <tr>

        </tr>
        <tr>
            <th colspan="9" style="border: none;text-align: right;">Period Date : {{ date('d-m-Y') .' To '. date('d-m-Y') }}</th>
        </tr>
        <tr>
            <th colspan="9" style="border: none;text-align: right;"><b>In SPECTA CASE</b></th>
        </tr>
        <tr>
            <th><b>Date</b></th>
            <th><b>PartyName</b></th>
            <th></th>
            <th></th>
            <th><b>Basic Amt</b></th>
            <th><b>Disc</b></th>
            <th><b>Other</b></th>
            <th>Redeem</th>
            <th><b>Net Amt</b></th>
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
            <td><b>{{ $data->customer->name }}</b></td>
            <td></td>
            <td>--</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
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
            <td>{{ $data->estimate_code }} {{ date('d-m-Y', strtotime($detail->date)) }}</td>
            <td>{{ $data->customer->name }}</td>
            <td></td>
            <td>{{ $detail->invoice_type }}</td>
            <td>{{ $detail->total_amount }}</td>
            <td>{{ $detail->discount_amount }}</td>
            <td>{{ $detail->other_charge }}</td>
            <td>{{ $detail->redeem_coin }}</td>
            <td>{{ $detail->net_amount }}</td>
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
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><b>{{ round($amtTotal) }}</b></td>
            <td><b>{{ round($disTotal) }}</b></td>
            <td><b>{{ round($otherTotal) }}</b></td>
            <td><b>{{ round($redeemTotal) }}</b></td>
            <td><b>{{ round($netTotal) }}</b></td>
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
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><b>{{ round($allAmtTotal) }}</b></td>
            <td><b>{{ round($allDisTotal) }}</b></td>
            <td><b>{{ round($allOtherTotal) }}</b></td>
            <td><b>{{ round($allRedeemTotal) }}</b></td>
            <td><b>{{ round($allNetTotal) }}</b></td>
        </tr>
    </tbody>
</table>
