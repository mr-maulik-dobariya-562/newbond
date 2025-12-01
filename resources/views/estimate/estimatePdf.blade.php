<!DOCTYPE html>
<html>

<head>
    <title>Estimate</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0px !important;
        }

        .header {
            text-align: center;
            margin-bottom: 12px;
        }

        .header h1 {
            margin: 0;
        }

        .details,
        .items {
            width: 100%;
            border-collapse: collapse;
        }

        .details td,
        .items td,
        .items th {
            border: 1px solid #000000ad;
            padding: 8px;
            text-align: left;
        }

        .details td {
            border: none;
        }

        @media print {
            @page {
                size: A5 portrait !important;

            }

            /* table {
                page-break-after: always;
            } */

            /* .page-break {
            page-break-after: always;
            } */
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    @foreach ($estimate as $data)

    @if ($loop->last)
    <div class="bill-container">
    @else
    <div class="bill-container page-break">
        @endif
        <table class="items">
            <thead>
                <tr>
                    <th colspan="8"
                        style="border: none;text-align: center;font-size: 14px;margin-top:0px !important;">
                        <u>ESTIMATE</u>
                    </th>
                </tr>
                <tr>
                    <th colspan="4" style="border-right: none;border-bottom:none;font-size: 12px">
                        <span>{{ $data->customer->name }}</span>
                    </th>
                    <th colspan="4"
                        style="border-left: none;border-bottom:none;text-align: right;padding-right: 30px;font-size: 12px">
                        Date: {{ date('d/m/Y', strtotime($data->date)) }}
                    </th>
                </tr>
                <tr>
                    <th colspan="4" style="border-right: none;border-bottom:none;border-top:none;font-size: 12px">
                        {{ $data->customer->city->name }}
                    </th>
                    <th colspan="4"
                        style="border-left: none;border-bottom:none;border-top:none;text-align: right;padding-right: 65px;font-size: 12px">
                        Page No: 1
                    </th>
                </tr>
                <tr>
                    <th style="padding: 2px;text-align: center;font-size:12px;width:10px;">SR.</th>
                    <th style="padding: 2px;text-align: center;font-size:12px">PRINTING DETAIL</th>
                    <th style="padding: 2px;text-align: center;font-size:12px">ITEM</th>
                    <th style="padding: 2px;text-align: center;font-size:12px">TYPE</th>
                    <th style="padding: 2px;text-align: center;font-size:12px;width:40px;">QTY</th>
                    <th style="padding: 2px;text-align: center;font-size:12px;width:11px;">RATE</th>
                    <th style="padding: 2px;text-align: center;font-size:12px;width:10px;">EX DISC</th>
                    <th style="padding: 2px;text-align: center;font-size:12px">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                <?php $qtyTotal = 0; ?>
                <?php $amountTotal = 0;
                $i = 1; ?>
                @foreach ($data->estimateDetail as $detail)
                <?php $amountTotal += $detail->amount; ?>
                <?php $qtyTotal += $detail->qty; ?>
                <tr>
                    <td
                        style="font-size: 12px;padding: 1px;border-bottom: none;border-top: none;text-align: right;">
                        {{ $i++ }}
                    </td>
                    <td
                        style="font-size: 9px;padding: 1px;padding-left: 3px;border-bottom: none;border-top: none;border-right: none;">
                        {{ $detail->narration }}
                    </td>
                    <td
                        style="font-size: 12px;padding: 2px;border-bottom: none;border-top: none;border-right: none;border-left: none;text-align: left;">
                        {{ $detail->item->name }}
                    </td>
                    <td
                        style="font-size: 12px;padding: 2px;border-bottom: none;border-top: none;border-right: none;border-left: none;text-align: right;">
                        {{ $detail->printType->name }}
                    </td>
                    <td
                        style="font-size: 12px;padding: 1px;padding-right: 3px;border-bottom: none;border-top: none;text-align: right;">
                        {{ $detail->qty }}
                    </td>
                    <td
                        style="font-size: 12px;padding: 1px;padding-right: 3px;border-bottom: none;border-top: none;text-align: right;">
                        {{ $detail->rate }}
                    </td>
                    <td
                        style="font-size: 12px;padding: 1px;border-bottom: none;border-top: none;text-align: right;">
                        {{ round($detail->discount ?? 0) }}%
                    </td>
                    <td
                        style="font-size: 12px;padding: 1px;padding-left: 3px;border-bottom: none;border-top: none;text-align: right;">
                        {{ $detail->amount }}
                    </td>
                </tr>

                @endforeach
                @for ($remainingRows = $i; $remainingRows < 20; $remainingRows++)
                    <tr>
                    <td
                        style="font-size: 12px;padding: 1px;border-bottom: none;border-top: none;text-align: right;">
                        &nbsp;</td>
                    <td
                        style="font-size: 12px;padding: 1px;border-bottom: none;border-top: none;border-right: none;">
                        &nbsp;</td>
                    <td
                        style="font-size: 12px;padding: 2px;border-bottom: none;border-top: none;border-right: none;border-left: none;text-align: left;">
                        &nbsp;</td>
                    <td
                        style="font-size: 12px;padding: 2px;border-bottom: none;border-top: none;border-right: none;border-left: none;text-align: right;">
                        &nbsp;</td>
                    <td
                        style="font-size: 12px;padding: 1px;border-bottom: none;border-top: none;text-align: right;">
                        &nbsp;</td>
                    <td
                        style="font-size: 12px;padding: 1px;border-bottom: none;border-top: none;text-align: right;">
                        &nbsp;</td>
                    <td
                        style="font-size: 12px;padding: 1px;border-bottom: none;border-top: none;text-align: right;">
                        &nbsp;</td>
                    <td
                        style="font-size: 12px;padding: 1px;border-bottom: none;border-top: none;text-align: right;">
                        &nbsp;</td>
                    </tr>
                    @endfor
            </tbody>
            <tfoot>
                <?php $disAmount = $amountTotal * $data->discount / 100 ?>
                <tr>
                    <td colspan="4" style="text-align: right;padding:0px;font-size: 12px;"><b>TOTAL: &nbsp;</b></td>
                    <td colspan="3" style="text-align: left;font-size: 12px;"><b>{{ $qtyTotal }}</b></td>
                    <td style="text-align: center;font-size: 12px;"><b>{{ round($amountTotal) }}</b></td>
                </tr>
                <tr>
                    <td rowspan="{{ $data->customer->partyType->name == 'Retail' ? 4 : 3 }}" colspan="4"
                        style="font-size: 12px;padding: 0px;">
                        <br>
                        <span style="margin-left:5px;margin-bottom:5px;"><b>TRANSPORT:</b>
                            {{ $data?->transport?->name }}</span> <br><br>
                        <span style="margin-left:5px;margin-bottom:5px;"><b>LR No:</b> {{ $data->lr_no }} <b
                                style="padding-left: 20px;">&nbsp;&nbsp;&nbsp;Parcel:</b> {{ $data->parcel }}</span>
                    </td>
                    <td colspan="3" style="text-align: left;font-size: 12px;padding: 2px !important;">DISC
                        {{ $data->discount }}%
                    </td>
                    <td style="text-align: center;font-size: 12px;padding: 2px !important;">{{ round($disAmount) }}
                    </td>
                </tr>
                @if ($data->customer->partyType->name == 'Retail')
                <tr>
                    <td colspan="3" style="text-align: left;font-size: 12px;padding: 2px !important;">REDEEM COIN</td>
                    <td style="text-align: center;font-size: 12px;padding: 2px !important;">{{ $data->redeem_coin }}
                    </td>
                </tr>
                @endif
                <tr>
                    <td colspan="3" style="text-align: left;font-size: 12px;padding: 2px !important;">OTHER CHAG
                    </td>
                    <td style="text-align: center;font-size: 11px;padding: 2px !important;">
                        {{ $data->other_charge }}
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: left;font-size: 12px;padding: 2px !important;"><b>TOTAL</b>
                    </td>
                    <td style="text-align: center;font-size: 13px;padding: 2px !important;">
                        <b>{{ round($amountTotal - $disAmount - $data->redeem_coin + $data->other_charge) }}</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="8" style="font-size: 13px;"><b>Note:</b> {{ $data->note }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endforeach
</body>

</html>
