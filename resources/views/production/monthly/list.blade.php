<button id="printReportTable" class="btn btn-primary mb-3">Print Table</button>
<div class="table-responsive">
    <table class="table table-sm table-bordered table-hover">
        {{-- Header --}}
        <thead>
            <tr>
                <th class="text-center" rowspan="2" style="vertical-align: middle">Date</th>
                <th class="text-center" colspan="{{ count($floorLocation) + 1 }}">Prod Qty(Pcs)</th>
                <th class="text-center" colspan="{{ count($floorLocation) + 1 }}">Prod Weight(Kg)</th>
                <th class="text-center" colspan="{{ count($floorLocation) + 1 }}" style="border-right: 3px solid #000;">
                    Runner Waste(Kg)</th>
                <th class="text-center" style="border-right: 3px solid #000;">Packing</th>
                <th class="text-center" colspan="{{ count($printTypes) }}" style="border-right: 3px solid #000;">Inward
                    Printing (Pcs)</th>
                <th class="text-center" colspan="{{ count($printTypes) + 2 }}" style="border-right: 3px solid #000">
                    Dispatch(Pcs)</th>
                <th class="text-center" rowspan="2" style="vertical-align: middle; border-left: 3px solid #000">Check By
                </th>
            </tr>
            <tr>
                @foreach ($floorLocation as $floor)
                    <th class="text-center">{{ $floor->name }}</th>
                @endforeach
                <th class="text-center">Total</th>
                @foreach ($floorLocation as $floor)
                    <th class="text-center">{{ $floor->name }}</th>
                @endforeach
                <th class="text-center">Total</th>
                @foreach ($floorLocation as $floor)
                    <th class="text-center">{{ $floor->name }}</th>
                @endforeach
                <th class="text-center" style="border-right: 3px solid #000;">Total</th>
                <th class="text-center" style="border-left: 3px solid #000;border-right: 3px solid #000;">Qty(Pcs)</th>
                @foreach ($printTypes as $printType)
                    @if ($printType->name != 'W/P')
                        <th class="text-center">{{ $printType->name }}</th>
                    @endif
                @endforeach
                <th class="text-center" style="border-right: 3px solid #000">Total</th>
                @foreach ($printTypes as $printType)
                    @if ($printType->name != 'W/P')
                        <th class="text-center">{{ $printType->name }}</th>
                    @endif
                @endforeach
                <th class="text-center">Printing Total</th>
                <th class="text-center">W/P</th>
                <th class="text-center">Total</th>
            </tr>
        </thead>
        {{-- Body --}}
        <tbody>
            @foreach ($dates as $date)
                <tr>
                    {{-- Date --}}
                    <td class="all-date" style="min-width: 120px">{{ $date }}</td>

                    {{-- Prod Qty (Pcs) --}}
                    @php $prodQtyTotal = 0; @endphp
                    @foreach ($floorLocation as $floor)
                        @php
                            $floorName = $floor->name;
                            $qty = $data[$floorName][$date]['production_pieces_quantity'] ?? 0;
                            $prodQtyTotal += $qty;
                        @endphp
                        <td>{{ $qty }}</td>
                    @endforeach
                    <td><strong>{{ $prodQtyTotal }}</strong></td>

                    {{-- Prod Weight (Kg) --}}
                    @php $prodWeightTotal = 0; @endphp
                    @foreach ($floorLocation as $floor)
                        @php
                            $floorName = $floor->name;
                            $weight = round($data[$floorName][$date]['production_weight']) ?? 0;
                            $prodWeightTotal += $weight;
                        @endphp
                        <td>{{ $weight }}</td>
                    @endforeach
                    <td><strong>{{ $prodWeightTotal }}</strong></td>

                    {{-- Runner Waste (Kg) --}}
                    @php $runnerTotal = 0; @endphp
                    @foreach ($floorLocation as $floor)
                        @php
                            $floorName = $floor->name;
                            $runner = round($data[$floorName][$date]['runner_waste']) ?? 0;
                            $runnerTotal += $runner;
                        @endphp
                        <td>{{ $runner }}</td>
                    @endforeach
                    <td style="border-right: 3px solid #000;"><strong>{{ $runnerTotal }}</strong></td>

                    {{-- Packing Qty(Pcs) --}}
                    <td style="border-left: 3px solid #000;border-right: 3px solid #000;">
                        {{ $data['Packing'][$date]['qty'] ?? 0 }}
                    </td>

                    {{-- Inward Printing --}}
                    @php $inwardTotal = 0; @endphp
                    @foreach ($printTypes as $type)
                        @if ($type->name != 'W/P')
                            @php
                                $inwardQty = $printTypesData[$type->name][$date]['production_qty_sum'] ?? 0;
                                $inwardTotal += $inwardQty;
                            @endphp
                            <td>{{ $inwardQty }}</td>
                        @endif
                    @endforeach
                    <td style="border-right: 3px solid #000"><strong>{{ $inwardTotal }}</strong></td>

                    {{-- Dispatch Estimates --}}
                    @php $dispatchTotal = 0; @endphp
                    @foreach ($printTypes as $type)
                        @if ($type->name != 'W/P')
                            @php
                                $qty = $estimate[$type->name][$date]['qty_sum'] ?? 0;
                                $dispatchTotal += $qty;
                            @endphp
                            <td>{{ $qty }}</td>
                        @endif
                    @endforeach

                    {{-- Printing Total --}}
                    @php
                        $printTotal = 0;
                        foreach ($printTypes as $type) {
                            if ($type->name != 'W/P') {
                                $printTotal += $estimate[$type->name][$date]['qty_sum'] ?? 0;
                            }
                        }
                    @endphp
                    <td>{{ $printTotal }}</td>

                    {{-- W/P Inward --}}
                    @php
                        $wp = $estimate['W/P'][$date]['qty_sum'] ?? 0;
                    @endphp
                    <td>{{ $wp }}</td>

                    {{-- Grand Total (Printing + W/P) --}}
                    <td style="border-right: 3px solid #000"><strong>{{ $printTotal + $wp }}</strong></td>

                    {{-- Check By --}}
                    @if($data['check'][$date]['check'] != 0)
                        {{-- <td><span class="badge bg-success">Checked</span></td> --}}
                        <td class="text-center">{{$data['createdBy'][$date]['created_by']}}</td>
                    @else
                        {{-- <td class="text-center td-check"><button class="btn btn-sm btn-primary check">Check</button></td> --}}
                        @php
                        $otherPermission = auth()->user()->hasPermissionTo("printing-monthly-other");
                        @endphp
                        @if ($otherPermission)
                        <td class="text-center td-check"><button class="btn btn-sm btn-primary check">Check</button></td>
                        @endif
                    @endif
                </tr>
            @endforeach
            <tr>
                <th>Total</th>

                {{-- Total Prod Qty (Pcs) --}}
                @php $totalProdQty = 0; @endphp
                @foreach ($floorLocation as $floor)
                    @php
                        $floorName = $floor->name;
                        $totalQty = round(array_sum(array_column($data[$floorName], 'production_pieces_quantity')));
                        $totalProdQty += $totalQty;
                    @endphp
                    <th>{{ $totalQty }}</th>
                @endforeach
                <th><strong>{{ $totalProdQty }}</strong></th>

                {{-- Total Prod Weight (Kg) --}}
                @php $totalProdWeight = 0; @endphp
                @foreach ($floorLocation as $floor)
                    @php
                        $floorName = $floor->name;
                        $totalWeight = round(array_sum(array_column($data[$floorName], 'production_weight')));
                        $totalProdWeight += $totalWeight;
                    @endphp
                    <th>{{ $totalWeight }}</th>
                @endforeach
                <th><strong>{{ $totalProdWeight }}</strong></th>

                {{-- Total Runner Waste (Kg) --}}
                @php $totalRunner = 0; @endphp
                @foreach ($floorLocation as $floor)
                    @php
                        $floorName = $floor->name;
                        $totalRunnerWaste = round(array_sum(array_column($data[$floorName], 'runner_waste')));
                        $totalRunner += $totalRunnerWaste;
                    @endphp
                    <th>{{ $totalRunnerWaste }}</th>
                @endforeach
                <th style="border-right: 3px solid #000;"><strong>{{ $totalRunner }}</strong></th>

                {{-- Total Packing Qty(Pcs) --}}
                @php $totalPacking = round(array_sum(array_column($data['Packing'], 'qty'))); @endphp
                <th style="border-left: 3px solid #000;border-right: 3px solid #000;">{{ $totalPacking }}</th>

                {{-- Total Inward Printing --}}
                @php $totalInward = 0; @endphp
                @foreach ($printTypes as $type)
                    @if ($type->name != 'W/P')
                        @php
                            $inwardQty = round(array_sum(array_column($printTypesData[$type->name], 'production_qty_sum')));
                            $totalInward += $inwardQty;
                        @endphp
                        <th>{{ $inwardQty }}</th>
                    @endif
                @endforeach
                <th style="border-right: 3px solid #000">{{ $totalInward }}</th>

                {{-- Total Dispatch Estimates --}}
                @php $totalDispatch = 0; @endphp
                @foreach ($printTypes as $type)
                    @if ($type->name != 'W/P')
                        @php
                            $dispatchQty = round(array_sum(array_column($estimate[$type->name], 'qty_sum')));
                            $totalDispatch += $dispatchQty;
                        @endphp
                        <th>{{ $dispatchQty }}</th>
                    @endif
                @endforeach

                {{-- Total Printing --}}
                @php $totalPrint = 0; @endphp
                @foreach ($printTypes as $type)
                    @if ($type->name != 'W/P')
                        @php
                            $printQty = round(array_sum(array_column($estimate[$type->name], 'qty_sum')));
                            $totalPrint += $printQty;
                        @endphp
                    @endif
                @endforeach
                <th>{{ $totalPrint }}</th>

                {{-- Total W/P Inward --}}
                @php $totalWP = round(array_sum(array_column($estimate['W/P'], 'qty_sum'))); @endphp
                <th>{{ $totalWP }}</th>

                {{-- Total Grand Total (Printing + W/P) --}}
                <th style="border-right: 3px solid #000"><strong>{{ $totalPrint + $totalWP }}</strong></th>

                {{-- Check By --}}
                <th></th>
            </tr>
        </tbody>
    </table>
</div>