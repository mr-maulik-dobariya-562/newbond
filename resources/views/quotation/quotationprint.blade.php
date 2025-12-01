<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
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
                size: a4 landscape;
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <div class="order-section" style="padding-left: 100px;">
        @foreach ($quotations as $quotation)
        <div class="order-header">{{ $quotation->customer->name }}</div>
        <table class="order-table" style="height: 50%;width: 80%;">
            <thead>
                <tr>
                    <th colspan="4" style="border: none;"></th>
                    <th style="border: none;">Transport : </th>
                </tr>
                <tr>
                    <th colspan="2" style="border: none;">{{ $quotation->date }}</th>
                    <th style="border: none;text-align: right;padding-right: 0px;">Pen Qty (Pcs.)</th>
                    <th style="border: none;">|</th>
                    <th style="border: none;padding-left: 0px;text-align: left;">Parcel</th>
                    <th style="border: none;">Item Remarks</th>
                    <th style="border: none;">Type</th>
                </tr>
            </thead>
            <tbody>
                <?php $qtyTotal = 0; $totalParcel = 0; ?>
                @foreach ($quotation->quotationDetail as $quotationDetail)
                <tr>
                    <td colspan="2" style="border-left: none; border-right: none;border-top: none;text-align: center;">{{ $quotationDetail->item->name }}T</td>
                    <td style="border-top: none;border-left: none;border-right: none;text-align: right;">{{ $quotationDetail->qty }}</td>
                    <td style="border-left: none;border-top: none;border-right: none;">|</td>
                    <td style="border-top: none;border-left: none;border-right: none;padding-left: 0px;text-align: center;">
                        @php
                            $pendingQty = $quotationDetail->qty - $quotationDetail->dispatch_qty;    
                            $parcel = round(($pendingQty / $quotationDetail->item->packing),2);
                            $totalParcel += $parcel;
                        @endphp
                        {{ $parcel }}
                    </td>
                    <td style="border-top: none;border-left: none;border-right: none;"></td>
                    <td style="border-top: none;border-left: none;border-right: none;">{{ $quotationDetail->printType->name }}</td>
                </tr>
                @php
                    $qtyTotal += $quotationDetail->qty;
                @endphp
                @endforeach

                <tr>
                    <td colspan="2" style="border-bottom: none;border-left: none; border-right: none;padding-bottom: 0px;">Online</td>
                    <td style="border-bottom: none;border-left: none; border-right: none;padding-bottom: 0px;text-align: right;">{{ $qtyTotal }}</td>
                    <td style="border-bottom: none;border-left: none; border-right: none;padding-bottom: 0px;">|</td>
                    <td style="border-bottom: none;border-left: none; border-right: none;padding-bottom: 0px;padding-left: 0px;text-align: center;">{{ $totalParcel }}</td>
                </tr>
                <tr>
                    <td colspan="7" style="border: none;text-align: start;padding: 0px;">_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</td>
                </tr>
                <tr>
                    <td colspan="2" style="border: none;"></td>
                    <td style="border: none;text-align: right;background-color: red" class="quotation-total">{{ $qtyTotal }}</td>
                </tr>
            </tbody>
        </table>
        @endforeach
    </div>
</body>
<script>
    window.print();
</script>

</html>