<!DOCTYPE html>
<html>

<head>
    <title>Quotation</title>
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
                size: a4 Portrait;
                margin-left: 80px;
                margin-right: 80px;
            }
        }
    </style>
</head>

<body>
    @foreach ($quotations as $data)
    <div style="height: 100%;width: 100%;justify-content: center;">
        <h3 style="text-align: center;padding-top: 50px;"><u>Quotation</u></h3>
        <table class="items">
            <thead>
                <tr>
                    <th colspan="3" style="border-right: none;border-bottom:none;"><span>{{ $data->customer->name }}</span></th>
                    <td colspan="3" style="border-left: none;border-bottom:none;text-align: right;padding-right: 60px">Date : {{ date('d/m/Y',strtotime($data->date)) }}</td>
                </tr>
                <tr>
                    <th colspan="3" style="border-right: none;border-bottom:none;border-top:none;">{{ $data->customer->city->name }}</th>
                    <td colspan="3" style="border-left: none;border-bottom:none;border-top:none;text-align: right;padding-right: 103px">Page No : 1</td>
                </tr>
                <tr>
                    <th style="text-align: center;">SR.</th>
                    <th style="text-align: center;width: 100%">DESCRIPTION</th>
                    <th style="text-align: center;">QTY</th>
                    <th style="text-align: center;">RATE</th>
                    <th style="text-align: center;">EX DISC</th>
                    <th style="text-align: center;width: 40%">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                <?php $qtyTotal = 0; ?>
                <?php $amountTotal = 0; ?>
                @foreach ($data->quotationDetail as $detail)
                <?php $amountTotal += $detail->amount; ?>
                <?php $qtyTotal += $detail->qty; ?>
                <tr>
                    <td style="border-bottom: none;text-align: center;">1</td>
                    <td style="border-bottom: none;">
                        <span style="display: inline-block;text-align: left;"> Afferell <br> -Bharuch <br> -0 </span>
                        <span style="display: inline-block;text-align: right;"> {{ $detail->item->name }} {{ $detail->printType->name }}</span>
                    </td>
                    <td style="border-bottom: none;text-align: right;">{{ $detail->qty }}</td>
                    <td style="border-bottom: none;text-align: center;">{{ $detail->rate }}</td>
                    <td style="border-bottom: none;text-align: center;">{{ $detail->discount. '%' }}</td>
                    <td style="border-bottom: none;text-align: center;">{{ $detail->amount }}</td>
                </tr>
                @if (count($data->quotationDetail) < 5 && $loop->iteration == 4)
                    <tr>
                        <td style="border-top: none; border-bottom: none;"></td>
                        <td style="border-top: none; border-bottom: none;"></td>
                        <td style="border-top: none; border-bottom: none;"></td>
                        <td style="border-top: none; border-bottom: none;"></td>
                        <td style="border-top: none; border-bottom: none;"></td>
                        <td style="border-top: none; border-bottom: none;"></td>
                    </tr>
                    <tr style="height: 300px;">
                        <td style="border-top: none;"></td>
                        <td style="border-top: none;"></td>
                        <td style="border-top: none;"></td>
                        <td style="border-top: none;"></td>
                        <td style="border-top: none;"></td>
                        <td style="border-top: none;"></td>
                    </tr>
                    @endif

                    @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="text-align: right;"><b>TOTAL</b></td>
                    <td colspan="3" style="text-align: left;"><b>{{ $qtyTotal }}</b></td>
                    <td style="text-align: center;"><b>{{ $amountTotal }}</b></td>
                </tr>
                <tr>
                    <td rowspan="3" colspan="2">
                        <br>
                        <b>Transport : </b> G DALA <br><br>
                        <b>LR No : </b>{{ $data->lr_no }} <b>&nbsp;&nbsp;&nbsp;Parcel : </b> 50
                    </td>
                    <?php $disAmount = $amountTotal * 10 / 100 ?>
                    <td colspan="3" style="text-align: left;">DISC 10%</td>
                    <td style="text-align: center;">{{ $disAmount }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: left;">OTHER CHAG</td>
                    <td style="text-align: center;">3111</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: left;"><b>TOTAL</b></td>
                    <td style="text-align: center;">{{ $amountTotal - $disAmount }}</td>
                </tr>
                <tr>
                    <td colspan="6"><b>Note : </b> armoda bill</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endforeach
</body>
<script>
    window.print();
</script>

</html>