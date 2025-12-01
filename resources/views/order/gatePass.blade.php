<!DOCTYPE html>
<html>

<head>
    <title>Gate Pass</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0px !important;
        }

        .header {
            text-align: center;
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
            padding: 4px;
            text-align: left;
        }

        .details td {
            border: none;
        }

        .items th {
            font-size: 10px;
            padding-bottom: 0px;
        }

        .items td {
            font-size: 10px;
        }

        td {
            max-width: 80px;
            word-wrap: break-word;
            word-break: break-all;
            white-space: normal;
            overflow-wrap: break-word;
        }

        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 10px;
            line-height: 0.5cm;
        }

        .footer th {
            padding: 4px;
            text-align: left;
            font-size: 10px;
            padding-bottom: 0px;
        }

        .footer {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        @page {
            margin-bottom: 150px;
        }

        @media print {
            /* @page {
                size: A5 portrait !important;
            } */

            table {
                /* page-break-after: always; */
            }

            .page-break {
                /* page-break-after: always; */
            }

        }

        .no-page-break {
            page-break-inside: avoid !important;
        }

        .page-break {
            page-break-after: always !important;
        }
    </style>
</head>
<footer>
    <table class="footer" style="padding-top: 0px;margin-top: 0px">
        <tr>
            <th colspan="6"
                style="text-align: left;border-right: none;border-left: none;border-bottom:none;font-size: 12px;"><span
                    class="totalQty">Total Qty </span> : _ _ _ _ _ _ _ _</th>
            <th colspan="2"
                style="text-align: right;border-left: none;border-right: none;border-bottom:none;font-size: 12px;">User
                Name : {{isset($orders[0][0]->createdBy->name) ? $orders[0][0]->createdBy->name : 'ONLINE'}}</th>
        </tr>
        <tr>
            <th colspan="6"
                style="text-align: left;border-right: none;border-left: none;border-bottom:none;border-top:none;font-size: 12px;">
                <span class="totalQty">No of Parcel </span> : _ _ _ _ _ _ _ _</th>
            <th colspan="2"
                style="text-align: right;border-left: none;border-right: none;border-bottom:none;border-top:none;font-size: 12px;">
                Security Signature</th>
        </tr>
        <tr>
            <th colspan="6"
                style="text-align: left;border-right: none;border-left: none;border-bottom:none;border-top:none;font-size: 12px;">
                <span class="totalQty">Dispatch Date </span> : _ _ _ _ _ _ _ _</th>
            <th colspan="2"
                style="text-align: right;border-left: none;border-right: none;border-bottom:none;border-top:none;font-size: 12px;">
                _ _ _ _ _ _ _ _ _</th>
        </tr>
    </table>
</footer>

<body>
    <div style="justify-content: center;">
        @foreach ($orders as $order)
                <div class="  {{ $loop->last ?: 'page-break' }}" style="margin-top:0px !important;">
                    <table class="items">
                        <thead>
                            <tr>
                                <th colspan="8"
                                    style="border-left: none;border-right:none;border-top:none;text-align: center;font-size: 10px">
                                    <h3>GATE PASS ( PO No - {{ $order[0]->order_code ?? '' }} )</h3>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th colspan="6" style="border-right: none;border-bottom:none;font-size: 12px;">
                                    <span>{{ $order[0]?->customer?->name ?? '' }} -
                                        {{ $order[0]?->customer?->city?->name ?? '' }}
                                        ({{ $order[0]?->customer?->partyGroup?->name ?? '' }})</span></th>

                                <td colspan="2"
                                    style="border-left: none;border-bottom: none;text-align: right;font-size: 10px;"><b>Date
                                        :{{ DATE("d-m-Y", strtotime($order[0]->date)) ?? '' }}</b></td>
                            </tr>
                            <tr>
                                <th colspan="4" style="border-right: none;border-bottom:none;border-top:none;font-size: 12px;">
                                    <span style="text-decoration: underline;">Print :</span>
                                    <span>{{ $order[0]->orderDetail[0]->narration ?? '' }} -
                                        {{ $order[0]->orderDetail[0]->block ?? '' }}</span></th>
                                <th colspan="4"
                                    style="border-left: none;border-bottom:none;border-top:none;text-align: right;font-size: 12px;">
                                    Block Lot : <span
                                        style="border-bottom: 1px solid #000;width: 100px;display: inline-block;text-align: center;">
                                    </span></th>
                            </tr>
                            <tr>
                                <th colspan="4"
                                    style="border-right: none;border-top:none;text-align: left;padding-bottom: 5px !important;font-size: 12px;">
                                    <span style="text-decoration: underline;">Remark :</span>
                                    <?php    $remaek = ''; ?>
                                    @if (isset($order[0]->orderDetail))
                                        @foreach ($order[0]->orderDetail as $orderDetail)
                                            @if ($orderDetail->other_remark != null)
                                                <?php                $remaek = $orderDetail->other_remark; ?>
                                                {{ $orderDetail->other_remark }}
                                                @break
                                            @endif
                                        @endforeach
                                    @endif
                                    @if ($remaek == '')

                                        <span style="width: 100px;display: inline-block;text-align: center;">
                                        </span>
                                    @endif
                                </th>
                                <th colspan="4"
                                    style="border-left: none;border-top:none;padding-bottom: 5px !important;text-align: right;font-size: 12px;">
                                    <span style="text-decoration: underline;">Transport :</span> <span
                                        style="width: 100px;display: inline-block;text-align: center;">{{ $orders[0][0]?->transport ?? '' }}
                                    </span></th>
                            </tr>
                            <tr>
                                <th rowspan="2"
                                    style="text-align: center;padding-bottom: 0px !important;width:10px !important;">Sr</th>
                                <th rowspan="2"
                                    style="text-align: center;padding-bottom: 0px !important;font-size: 11px;width:90px;">Item
                                </th>
                                <th rowspan="2" style="text-align: center;padding-bottom: 0px !important;width:30px;">
                                    Qty</br>(Pcs)</th>
                                <th rowspan="2" style="text-align: center;padding-bottom: 0px !important;">Item Remarks</th>
                                <th rowspan="2" style="text-align: center;padding-bottom: 0px !important;">Type</th>
                                <th colspan="3" style="text-align: center;padding-bottom: 0px !important;">Signature</th>
                            </tr>
                            <tr>
                                <th style="text-align: center;padding-bottom: 0px !important;font-size: 11px;">Print by</th>
                                <th style="text-align: center;padding-bottom: 0px !important;">Pest by</th>
                                <th style="text-align: center;padding-bottom: 0px !important;">Pack by</th>
                            </tr>


                            <?php    $qtyTotal = 0;
            $i = 1; ?>
                            @if (isset($order[0]->orderDetail))
                                @foreach ($order[0]->orderDetail as $orderDetail)
                                    @if ($orderDetail->dispatch_qty == $orderDetail->qty || $orderDetail->dispatch_qty>$orderDetail->qt)
                                        @continue
                                    @endif
                                    <tr>
                                        <td
                                            style="padding-bottom: 0px !important;border-bottom: none;text-align: center;font-size: 12px;">
                                            {{ $i++ }}</td>
                                        <td
                                            style="padding-bottom: 0px !important;border-bottom: none;text-align: left;font-size: 12px;">
                                            {{ $orderDetail->item->name }}</td>
                                        <td style="padding-bottom: 0px !important;text-align: right;font-size: 12px;">
                                            {{ $orderDetail->qty }}</td>
                                        <td
                                            style="padding-bottom: 0px !important;border-bottom: none;text-align: center;font-size: 10px;">
                                            {{ $orderDetail->remark }}</td>
                                        <td
                                            style="padding-bottom: 0px !important;border-bottom: none;text-align: center;font-size: 12px;">
                                            {{ $orderDetail->printType?->name }}{{ $order[0]?->printTypeExtra?->code ? ' - ' . $order[0]?->printTypeExtra?->code : '' }}
                                        </td>
                                        <td style="padding-bottom: 0px !important;border-bottom: none;text-align: center;"></td>
                                        <td style="padding-bottom: 0px !important;border-bottom: none;text-align: center;"></td>
                                        <td style="padding-bottom: 0px !important;border-bottom: none;text-align: center;"></td>
                                    </tr>
                                    <?php            $qtyTotal += $orderDetail->qty; ?>
                                @endforeach
                            @endif
                            <tr>
                                <td style="padding-bottom: 0px !important;border-top: none; border-bottom: none;"></td>
                                <td style="padding-bottom: 0px !important;border-top: none; border-bottom: none;"></td>
                                <td
                                    style="padding-bottom: 0px !important;text-align: right;border-top: none; border-bottom: none;font-size: 12px;">
                                    <b>{{ $qtyTotal }}</b></td>
                                <td style="padding-bottom: 0px !important;border-top: none; border-bottom: none;"></td>
                                <td style="padding-bottom: 0px !important;border-top: none; border-bottom: none;"></td>
                                <td style="padding-bottom: 0px !important;border-top: none; border-bottom: none;"></td>
                                <td style="padding-bottom: 0px !important;border-top: none; border-bottom: none;"></td>
                                <td style="padding-bottom: 0px !important;border-top: none; border-bottom: none;"></td>
                            </tr>

                            @for ($remainingRows = $i; $remainingRows < (26 - count($order[0]->orderDetail)); $remainingRows++)
                                <tr>
                                    <td style="padding-bottom: 0px !important;border-top: none; border-bottom: none;">&nbsp;</td>
                                    <td style="padding-bottom: 0px !important;border-top: none; border-bottom: none;">&nbsp;</td>
                                    <td
                                        style="padding-bottom: 0px !important;text-align: right;border-top: none; border-bottom: none;">
                                        &nbsp;</td>
                                    <td style="padding-bottom: 0px !important;border-top: none; border-bottom: none;">&nbsp;</td>
                                    <td style="padding-bottom: 0px !important;border-top: none; border-bottom: none;">&nbsp;</td>
                                    <td style="padding-bottom: 0px !important;border-top: none; border-bottom: none;">&nbsp;</td>
                                    <td style="padding-bottom: 0px !important;border-top: none; border-bottom: none;">&nbsp;</td>
                                    <td style="padding-bottom: 0px !important;border-top: none; border-bottom: none;">&nbsp;</td>
                                </tr>
                            @endfor
                            <tr>
                                <td style="border-top: none;">&nbsp;</td>
                                <td style="border-top: none;">&nbsp;</td>
                                <td style="border-top: none;">&nbsp;</td>
                                <td style="border-top: none;">&nbsp;</td>
                                <td style="border-top: none;">&nbsp;</td>
                                <td style="border-top: none;">&nbsp;</td>
                                <td style="border-top: none;">&nbsp;</td>
                                <td style="border-top: none;">&nbsp;</td>
                            </tr>
                        </tbody>
                        <!-- <tfoot>
                            <tr>
                                <th colspan="6" style="text-align: left;border-right: none;border-left: none;border-bottom:none;font-size: 12px;"><span class="totalQty">Total Qty </span> : _ _ _ _ _ _ _ _</th>
                                <th colspan="2" style="text-align: right;border-left: none;border-right: none;border-bottom:none;font-size: 12px;">User Name : ONLINE</th>
                            </tr>
                            <tr>
                                <th colspan="6" style="text-align: left;border-right: none;border-left: none;border-bottom:none;border-top:none;font-size: 12px;"><span class="totalQty">No of Parcel </span> : _ _ _ _ _ _ _ _</th>
                                <th colspan="2" style="text-align: right;border-left: none;border-right: none;border-bottom:none;border-top:none;font-size: 12px;">Security Signature</th>
                            </tr>
                            <tr>
                                <th colspan="6" style="text-align: left;border-right: none;border-left: none;border-bottom:none;border-top:none;font-size: 12px;"><span class="totalQty">Dispatch Date </span> : _ _ _ _ _ _ _ _</th>
                                <th colspan="2" style="text-align: right;border-left: none;border-right: none;border-bottom:none;border-top:none;font-size: 12px;">_ _ _ _ _ _ _ _ _</th>
                            </tr>
                        </tfoot> -->
                    </table>
                </div>
        @endforeach
    </div>

</body>

</html>
