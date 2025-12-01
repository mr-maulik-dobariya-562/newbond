<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inward List</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <h2 class="text-center mt-3">Daily Report - {{ date('d-m-Y', strtotime($date)) }}</h2>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Moulding</th>
                    <th>Production Qty (Pcs)</th>
                    <th>Production Weight (Kg)</th>
                    <th>Runner Waste (Kg)</th>
                    <th>Component Rejection (Kg)</th>
                </tr>
            </thead>
            <tbody>
                @php
                $productiontotal = 0;
                $productionweighttotal = 0;
                $runnerwastetotal = 0;
                $componentrejectiontotal = 0;
                @endphp
                @foreach($data as $key => $row)
                @php
                $productiontotal += ceil(@$row[0]->production_pieces_quantity_sum);
                $productionweighttotal += ceil(@$row[0]->production_weight_sum);
                $runnerwastetotal += ceil(@$row[0]->runner_waste_sum);
                $componentrejectiontotal += ceil(@$row[0]->component_rejection_sum);
                @endphp
                <tr>
                    <td>{{ $key }}</td>
                    <td>{{ ceil(@$row[0]->production_pieces_quantity_sum) }}</td>
                    <td>{{ ceil(@$row[0]->production_weight_sum) }}</td>
                    <td>{{ ceil(@$row[0]->runner_waste_sum) }}</td>
                    <td>{{ ceil(@$row[0]->component_rejection_sum) }}</td>
                </tr>
                @endforeach
                <tr>
                    <td><b>Total</b></td>
                    <td><b>{{ $productiontotal }}</b></td>
                    <td><b>{{ $productionweighttotal }}</b></td>
                    <td><b>{{ $runnerwastetotal }}</b></td>
                    <td><b>{{ $componentrejectiontotal }}</b></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="container pt-1">
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Inward</th>
                    @foreach ($printTypesData as $key => $print)
                    <th>{{ $key }}</th>
                    @endforeach
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                $productiontotal = 0;
                $inwardtotal = 0;
                @endphp
                <tr>
                    <td>Printing</td>
                    @foreach ($printTypesData as $key => $value)
                    @php
                    $productiontotal += ceil(@$value[0]->production_qty_sum);
                    @endphp
                    <td>{{ ceil(@$value[0]->production_qty_sum) ?? 0 }}</td>
                    @endforeach
                    <td><b>{{ $productiontotal }}</b></td>
                </tr>
                <tr style="border-top: 1px solid black;">
                    <td>Packing</td>
                    @foreach ($inward as $key => $value)
                    @php
                    $inwardtotal += ceil(@$value[0]->qty_sum);
                    @endphp
                    <td>{{ ceil(@$value[0]->qty_sum) ?? 0 }}</td>
                    @endforeach
                    <td><b>{{ $inwardtotal }}</b></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="container pt-1">
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Dispatch</th>
                    @foreach ($printTypesData as $key => $print)
                    <th>{{ $key }}</th>
                    @endforeach
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                $productiontotal = 0;
                $estimatetotal = 0;
                @endphp
                <tr>
                    <td>Qty (Pcs)</td>
                    @foreach ($estimate as $key => $value)
                    @php
                    $estimatetotal += ceil(@$value[0]->qty_sum);
                    @endphp
                    <td>{{ ceil(@$value[0]->qty_sum) ?? 0 }}</td>
                    @endforeach
                    <td><b>{{ $estimatetotal }}</b></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
<style>
    @media print {
        .no-print {
            display: none;
        }
    }
</style>
<div class="container text-center mt-5 no-print">
    <button class="btn btn-primary" onclick="window.print()">Print</button>
    <a href="{{ route('production.inward') }}" class="btn btn-primary">Back</a>
</div>
</html>
