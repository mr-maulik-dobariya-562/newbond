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
            }
        }
    </style>
</head>

<body>
    <div style="height: 100%;width: 100%;justify-content: center;">
        <h3 style="text-align: center;padding-top: 50px;"></h3>
        <tr>
            <td colspan="7" style="background-color: #4CAF50; color: white; text-align: center;vertical-align: middle; font-size: 16pt; font-weight: bold; height: 50px;">
                {{ date('d-F-Y') }}
            </td>
        </tr>
        @foreach ($data as $key => $item)
        @php $newprint = []; @endphp
        @foreach ($item as $itemKey => $value)
        @foreach ($value as $innerItem => $innerValue)
        @if ($innerItem != "item_name" && $innerItem != "item_id" && $innerItem != "amount")
        @if (!in_array($innerItem , $newprint))

        @php $newprint[] = $innerItem ; @endphp

        @endif
        @endif
        @endforeach
        @endforeach
        <?php $newprinttotal = [];
        foreach ($newprint as $k => $v) {
            $newprinttotal[$v]['amount'] = 0;
            $newprinttotal[$v]['qty'] = 0;
        }  ?>

        @php $totalQty = 0; @endphp
        @php $totalAmount = 0; @endphp
        <table class="items" style="margin-bottom: 20px;">
            <thead>
                <tr>
                    <th colspan="2" style="border-top: none;border-right: none;border-left: none;text-align: left;"><b>Product Name</b></th>
                    @foreach ($newprint as $value)
                    <th>{{ $value }}</th>
                    @endforeach
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="{{ count($item)+1 }}" style="text-align: center;">{{ isset($key) ? $key : '' }}</td>
                </tr>
                @foreach ($item as $key => $value)
                <tr>
                    <td>{{ $value['item_name'] }}</td>
                    @php
                    $rowTotal = 0;
                    @endphp

                    @foreach ($newprint as $k => $v)

                    @if (isset($value[$v]))
                    @php
                    $rowTotal += $value[$v]['qty'];

                    $newprinttotal[$v]['amount'] +=$value[$v]['amount'];
                    $newprinttotal[$v]['qty'] +=$value[$v]['qty'];
                    $totalQty += $value[$v]['qty'];
                    $totalAmount += $value[$v]['amount'];

                    // Add to overall totals
                    if (!isset($overallTotals[$v])) {
                        $overallTotals[$v] = ['qty' => 0, 'amount' => 0];
                    }
                    $overallTotals[$v]['qty'] += $value[$v]['qty'];
                    $overallTotals[$v]['amount'] += $value[$v]['amount'];
                    @endphp
                    <td>{{ $value[$v]['qty'] }}</td>
                    @else
                    <td>0</td>
                    @endif
                    @endforeach

                    <td>{{ $rowTotal }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="2" rowspan="2">Total</td>
                    @foreach ($newprint as $k => $v)

                    <td><b>{{ $newprinttotal[$v]['qty'] }} <br> {{ $status == '1' ? $newprinttotal[$v]['amount'] : ''}}</b></td>

                    @endforeach
                    <td><b>{{ $totalQty }}<br>{{ $status == '1' ? $totalAmount : ''}}</b></td>
                </tr>
            </tbody>
        </table>
        @endforeach

        <!-- New Table for Type-Wise Totals -->
        <table class="items">
            <thead>
                <tr>
                    <th colspan="2"></th>
                    @foreach ($overallTotals as $type => $printtype)
                    <th>{{ $type }}</th>
                    @endforeach
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2" rowspan="2">
                    Total Quntity
                    <br>
                    Total Amount
                    </td>
                    @php
                        $allQtyTotal = 0;
                        $allAmountTotal = 0;
                    @endphp
                    @foreach ($overallTotals as $type => $totals)
                    @php
                        $allAmountTotal += $totals['amount'];
                        $allQtyTotal += $totals['qty'];
                    @endphp
                    <td>{{ $totals['qty'] }}<br>{{ $status == '1' ? $totals['amount'] : '' }}</td>
                    @endforeach
                    <td>{{ $allQtyTotal }}<br>{{ $status == '1' ? $allAmountTotal : '' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
<script>
    window.print();
</script>

</html>
