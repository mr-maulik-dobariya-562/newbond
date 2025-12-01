@extends('Layouts.app')

@section('title', 'Analysis Molding')

@section('header')
<div class="page-header d-print-none">
	<div class="row g-2 align-items-center">
	<div class="col">
		<div class="page-pretitle">
			Overview
		</div>
		<h2 class="page-title">
			Analysis Molding
		</h2>
	</div>
	</div>
</div>
@endsection
@section('content')
<div class="row row-deck row-cards">
	<div class="card mt-3">
		<div class="card-status-top bg-primary"></div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-2">
						<label class="form-label">FY Month</label>
						<select class="form-select select2" id="fyYear" multiple="multiple"></select>
					</div>
					<div class="col-md-2">
						<label class="form-label">Machine No</label>
						<select class="form-select select2" id="machine_no" multiple="multiple">
							@foreach ($machines as $machine)
							<option value="{{ $machine->id }}">{{ $machine->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-2">
						<label class="form-label">Location</label>
						<select class="form-select select2" id="location" multiple="multiple">
							@foreach ($locations as $location)
							<option value="{{ $location->id }}">{{ $location->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-2">
						<label class="form-label">Shift</label>
						<select class="form-select select2" id="shift" multiple="multiple">
							@foreach ($shifts as $shift)
							<option value="{{ $shift->id }}">{{ $shift->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-2">
						<label class="form-label">Product Type</label>
						<select class="form-select select2" id="productType" multiple="multiple">
							@foreach ($productTypes as $productType)
							<option value="{{ $productType->id }}">{{ $productType->name }}</option>
							@endforeach
						</select>
					</div>
                    <div class="col-md-2 mt-4">
                        <button class="btn btn-primary" id="searchBtn">Search</button>
                    </div>
				</div>
			</div>
		</div>
	</div>

    <div class="card mt-3">
        <div class="card-status-top bg-primary"></div>
        <div class="card-body">
            <div class="row">
                <div id="table"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push("javascript")
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        $("#searchBtn").on("click", function() {
            var fyYear = $("#fyYear").val();
            var machine_no = $("#machine_no").val();
            var location = $("#location").val();
            var shift = $("#shift").val();
            var productType = $("#productType").val();

            $.ajax({
                url: "{{ route('manufacture-master.analysis-molding.getView') }}",
                type: "POST",
                data: {
                    fyYear: fyYear,
                    machine_no: machine_no,
                    location: location,
                    shift: shift,
                    product: productType
                },
                success: function(data) {
                    $("#table").html(data.html);
                    updateChart(data.months,data.productionPiecesQtyChart,'productionChart','Production Pieces Quantity');
                    updateChart(data.months,data.productionweightChart,'productionweight','Production Weight(Kgs.)');
                    updateChart(data.machineName,data.machineTotal,'Machine','Production Weight(Kgs.)');
                    multiRowChart(data.months,data.componentRejectionChart,data.runnerWasteChart,'componentRunner','Component Rejection(Kgs.)','Runner Waste (Kgs.)');
                    circleChart(data.comlocationAll,data.compercentage,'locationComponent','',['rgb(80 19 214 / 87%)','rgb(214 19 78 / 87%)','rgb(19 206 214 / 87%)']);
                    circleChart(data.runnerWasteLocation,data.runnerpercentage,'locationRunner','',['rgb(80 19 214 / 87%)','rgb(214 19 78 / 87%)','rgb(19 206 214 / 87%)']);
                    lineChart(data.dateAll,data.productionWeightAll,data.productionQtytAll,'dailyChart','Production Weight(Kgs.)','Production Pieces Quantity',['rgb(80 19 214 / 87%)','rgb(214 19 78 / 87%)','rgb(19 206 214 / 87%)']);
                }
            })

        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const fyYearSelect = document.getElementById('fyYear');
        let optionsHTML = '<option value="">Select FY Year</option>';

        const today = new Date();

        for (let i = 0; i < 12; i++) {
            const date = new Date(today.getFullYear(), today.getMonth() - i, 1);
            const monthYear = date.toLocaleString('default', { month: 'short', year: 'numeric' });
            const value = `${date.getFullYear()}-${('0' + (date.getMonth() + 1)).slice(-2)}`;

            optionsHTML += `<option value="${value}">${monthYear}</option>`;
        }

        fyYearSelect.innerHTML = optionsHTML;
    });

    function updateChart($months,$amount,chartId,title) {
        const labels = $months;
        const data = {
            labels: labels,
            datasets: [{
                label: title,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                data: $amount
            }]
        };

        const config = {
            type: 'bar', // You can change this to 'bar' or other types of charts
            data: data,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        const myChart = new Chart(
            document.getElementById(chartId),
            config
        );
    }

    function multiRowChart($months,$amount,$amount1,chartId,title,title1) {
        const labels = $months;
        const data = {
            labels: labels,
            datasets: [{
                label: title,
                backgroundColor: 'rgb(229, 115, 115)',
                borderColor: 'rgb(229, 115, 115)',
                borderWidth: 1,
                data: $amount
            },
            {
                label: title1,
                backgroundColor: 'rgb(244, 67, 54)',
                borderColor: 'rgb(244, 67, 54)',
                borderWidth: 1,
                data: $amount1
            }
        ]
        };

        const config = {
            type: 'bar',
            data: data,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        const myChart = new Chart(
            document.getElementById(chartId),
            config
        );
    }

    function circleChart($months,$amount,chartId,title,color) {
        const labels = $months;
        const data = {
            labels: labels,
            datasets: [
                {
                    label: title,
                    backgroundColor: color,
                    borderWidth: 1,
                    data: $amount
                }
            ]
        };

        const config = {
            type: 'pie', // You can change this to 'bar' or other types of charts
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false,
                        text: 'Pie Chart'
                    }
                }
            }
        };

        const myChart = new Chart(
            document.getElementById(chartId),
            config
        );
    }

    function lineChart($date,$amount,$amount1,chartId,title,title1,color) {
        const labels = $date;
        const data = {
            labels: labels,
            datasets: [
                {
                    label: title,
                    backgroundColor: color,
                    borderWidth: 1,
                    data: $amount
                },
                {
                    label: title1,
                    backgroundColor: color,
                    borderWidth: 1,
                    data: $amount1
                }
            ]
        };

        const config = {
            type: 'line', // You can change this to 'bar' or other types of charts
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false,
                        text: 'Line Chart'
                    }
                }
            }
        };

        const myChart = new Chart(
            document.getElementById(chartId),
            config
        );
    }
</script>
@endpush
