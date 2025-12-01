<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 10px;
        }

        .order-section {
            margin-bottom: 20px;
            padding: 20px;
        }

        .order-header {
            font-weight: bold;
            /* margin-bottom: 10px; */
            background-color: red;
        }

        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .order-table th,
        .order-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .order-total {
            font-weight: bold;
            text-align: right;
            margin-top: 10px;
        }

        .note {
            margin-top: 10px;
            font-style: italic;
        }

        @media print {
            @page {
                size: a5 portrait;
            }

            table {
                page-break-after: always;
            }
        }

        .order-table td {
            border: 1px solid #000;
            padding: 2px;
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="order-section" style="padding-left: 100px;">
        @foreach ($orders as $order)
                <div style="page-break-after: always;">
                    <div class="order-header">{{ $order[0]->customer->name }} - {{ $order[0]->customer?->city?->name }} -
                        ({{ $order[0]->customer?->partyGroup?->name }}) - {{ $order[0]->narration }}</div>
                    <table class="order-table" style="height: 50%;width: 80%;">
                        <thead>
                            <tr>
                                <th colspan="2" style="border: none;">Discription : {{ $order[0]->discription }}</th>
                                <th colspan="4" style="border: none;"></th>
                                <th colspan="2" style="border: none;">Transport : {{ $order[0]->transport }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" style="border: none;">{{ $order[0]->date }}</th>
                                <th style="border: none;text-align: right;padding-right: 0px;">Pen Qty (Pcs.)</th>
                                <th style="border: none;">|</th>
                                <th style="border: none;padding-left: 0px;text-align: left;">Parcel</th>
                                <th style="border: none;">Item Remarks</th>
                                <th style="border: none;">Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php    $qtyTotal = 0;
            $totalParcel = 0; ?>
                            @foreach ($order[0]->orderDetail as $orderDetail)
                            @php
                            $pendingQty = ($orderDetail->qty ?? 0) - ($orderDetail->dispatch_qty ?? 0);
                            if($pendingQty<=0){ continue;}
                            $parcel = ($orderDetail->item->packing > 0) ? round(($pendingQty / $orderDetail->item->packing), 2) : 0;
                            $totalParcel += $parcel;
                        @endphp
                                            <tr>
                                                <td colspan="3"
                                                    style="border-left: none; border-right: none;border-top: none;text-align: center;">
                                                    {{ $orderDetail->item->name }}</td>
                                                <td style="border-top: none;border-left: none;border-right: none;text-align: right;">
                                                    {{ $orderDetail->qty }}</td>
                                                <td style="border-left: none;border-top: none;border-right: none;">|</td>
                                                <td
                                                    style="border-top: none;border-left: none;border-right: none;padding-left: 0px;text-align: center;">

                                                    {{ abs($parcel) }}
                                                </td>
                                                <td style="border-top: none;border-left: none;border-right: none;">{{ $orderDetail->remarks }}
                                                </td>
                                                <td style="border-top: none;border-left: none;border-right: none;">
                                                    {{ $orderDetail->printType->name }}{{ $order[0]?->printTypeExtra?->code ? ' - ' . $order[0]?->printTypeExtra?->code : '' }}
                                                </td>
                                            </tr>
                                            @php
                                                $qtyTotal += $orderDetail->qty;
                                            @endphp
                            @endforeach

                            <tr>
                                <td colspan="3"
                                    style="border-bottom: none;border-left: none; border-right: none;padding-bottom: 0px;">
                                    Online</td>
                                <td
                                    style="border-bottom: none;border-left: none; border-right: none;padding-bottom: 0px;text-align: right;">
                                    {{ $qtyTotal }}</td>
                                <td style="border-bottom: none;border-left: none; border-right: none;padding-bottom: 0px;">|
                                </td>
                                <td
                                    style="border-bottom: none;border-left: none; border-right: none;padding-bottom: 0px;padding-left: 0px;text-align: center;">
                                    {{ $totalParcel }}</td>
                            </tr>
                            <tr>
                                <td colspan="8" style="border: none;text-align: start;padding: 0px;">_ _ _ _ _ _ _ _ _ _ _ _ _ _
                                    _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</td>
                            </tr>
                            <tr>
                                <td colspan="3" style="border: none;"></td>
                                <td style="border: none;text-align: right;background-color: red;" class="order-total">
                                    {{ $qtyTotal }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
        @endforeach
    </div>
</body>
<script>
    window.print();
</script>

</html>
