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
                size: a5 landscape;
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <div style="height: 100%;width: 100%;justify-content: center;">
        @foreach ($quotations as $quotation)
        <h3 style="text-align: center;padding-top: 50px;">GATE PASS ( PO No - {{ $quotation[0]->po_no ?? '' }} )</h3>
        <table class="items">
            <thead>
                <tr>
                    <th colspan="6" style="border-right: none;border-bottom:none;"><span>NAKODA OPTICAL - AhmedabadZ (Gujarat)</span></th>
                    <td colspan="1" style="border-left: none;border-bottom:none;text-align: right;">{{ $quotation[0]->date ?? '' }}</td>
                </tr>
                <tr>
                    <th colspan="3" style="border-right: none;border-bottom:none;border-top:none;"><span style="text-decoration: underline;">Print :</span> <span>{{ $quotation[0]->quotationDetail[0]->narration ?? '' }} - {{ $quotation[0]->quotationDetail[0]->block ?? '' }}</span></th>
                    <th colspan="4" style="border-left: none;border-bottom:none;border-top:none;text-align: right;">Block Loot : <span style="border-bottom: 1px solid #000;width: 100px;display: inline-block;text-align: center;"> </span></th>
                </tr>
                <tr>
                    <th colspan="3" style="border-right: none;border-top:none;"><span style="text-decoration: underline;">Transport :</span></th>
                    <th colspan="4" style="border-left: none;border-top:none;text-align: right;"><span style="text-decoration: underline;">Remark :</span> <span style="width: 100px;display: inline-block;text-align: center;"> </span></th>
                </tr>
                <tr>
                    <th rowspan="2" style="text-align: center;">Item</th>
                    <th rowspan="2" style="text-align: center;">Qty (Pcs)</th>
                    <th rowspan="2" style="text-align: center;">Item Remarks</th>
                    <th rowspan="2" style="text-align: center;">Type</th>
                    <th colspan="3" style="text-align: center;">Signature</th>
                </tr>
                <tr>
                    <th style="text-align: center;">Print by</th>
                    <th style="text-align: center;">Post by</th>
                    <th style="text-align: center;">Pack by</th>
                </tr>
            </thead>
            <tbody>
                <?php $qtyTotal = 0; ?>
                @if (isset($quotation[0]->quotationDetail))
                @foreach ($quotation[0]->quotationDetail as $quotationDetail)
                <tr>
                    <td style="border-bottom: none;text-align: center;">{{ $quotationDetail->item_name }}</td>
                    <td style="text-align: right;">{{ $quotationDetail->qty }}</td>
                    <td style="border-bottom: none;text-align: center;">{{ $quotationDetail->remark }}</td>
                    <td style="border-bottom: none;text-align: center;">{{ $quotationDetail->printType?->name }}</td>
                    <td style="border-bottom: none;text-align: center;"></td>
                    <td style="border-bottom: none;text-align: center;"></td>
                    <td style="border-bottom: none;text-align: center;"></td>
                </tr>
                <?php $qtyTotal += $quotationDetail->qty; ?>
                @endforeach
                @endif
                <tr>
                    <td style="border-top: none; border-bottom: none;"></td>
                    <td style="text-align: right;border-top: none; border-bottom: none;">{{ $qtyTotal }}</td>
                    <td style="border-top: none; border-bottom: none;"></td>
                    <td style="border-top: none; border-bottom: none;"></td>
                    <td style="border-top: none; border-bottom: none;"></td>
                    <td style="border-top: none; border-bottom: none;"></td>
                    <td style="border-top: none; border-bottom: none;"></td>
                </tr>
                <tr style="height: 100px;">
                    <td style="border-top: none;"></td>
                    <td style="border-top: none;"></td>
                    <td style="border-top: none;"></td>
                    <td style="border-top: none;"></td>
                    <td style="border-top: none;"></td>
                    <td style="border-top: none;"></td>
                    <td style="border-top: none;"></td>
                </tr>
            </tbody>
        </table>
        <div class="footer">
            <div style="display: flex; justify-content: space-between;">
                <div>
                    <style>
                        .totalValue {
                            display: inline-block;
                            width: 100px;
                            border-bottom: 1px solid #000;
                            text-align: center;
                        }
                    </style>
                    <p><span class="totalQty">Total Qty </span> : <span class="totalValue"> </span></p>
                    <p><span class="totalQty">No of Parcel </span> : <span class="totalValue"> </span></p>
                    <p><span class="totalQty">Dispatch Date </span> : <span class="totalValue"> </span></p>
                </div>
                <div>
                    <p>User Name: ONLINE</p>
                    <p>Security Signature</p>
                    <p class="totalValue"></p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</body>
<script>
    window.print();
</script>

</html>