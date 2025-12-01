@extends('Layouts.app')

@section('title', 'Analysis Printing')

@section('header')
<div class="page-header d-print-none">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                Overview
            </div>
            <h2 class="page-title">
                Analysis Printing
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
                    <label class="form-label">Operator Name</label>
                    <select class="form-select select2" id="operator" multiple="multiple">
                        @foreach ($operators as $operator)
                        <option value="{{ $operator->id }}">{{ $operator->name }}</option>
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
            var operator = $("#operator").val();

            $.ajax({
                url: "{{ route('manufacture-master.analysis-printing.getView') }}",
                type: "POST",

                data: {
                    fyYear: fyYear,
                    operator: operator,
                },
                success: function(data) {
                    $("#table").html(data.html);
                    const productionQty = data.productionQty.map(element => element.total_production_qty);
                    const rejectionQty = data.productionQty.map(element => element.total_rection_qty);
                    const months = data.productionQty.map(element => (new Intl.DateTimeFormat('en-US', {
                        month: 'long'
                    }).format(new Date(element.year, element.month - 1))) + ' ' + element.year);
                    updateChart(months, productionQty, rejectionQty, 'productionChart', 'Production QTY.', 'Rejection QTY.');
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
            const monthYear = date.toLocaleString('default', {
                month: 'short',
                year: 'numeric'
            });
            const value = `${date.getFullYear()}-${('0' + (date.getMonth() + 1)).slice(-2)}`;

            optionsHTML += `<option value="${value}">${monthYear}</option>`;
        }

        fyYearSelect.innerHTML = optionsHTML;
    });

    function updateChart($months, $amount, $amount1, chartId, title, title1) {
        const labels = $months;
        const footer = (tooltipItems) => {
            let sum = 0;

            tooltipItems.forEach(function(tooltipItem) {
                sum += tooltipItem.parsed.y;
            });
            return 'Sum: ' + sum;
        };
        const data = {
            labels: labels,
            datasets: [{
                    label: title,
                    backgroundColor: '#e38ebc9c',
                    borderColor: '#e38ebc9c',
                    type: 'bar',
                    data: $amount
                },
                {
                    label: title1,
                    backgroundColor: '#e34949',
                    borderColor: '#e34949',
                    type: 'line',
                    data: $amount1
                }
            ]
        };

        const config = {
            type: 'line',
            data: data,
            options: {
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            footer: footer,
                        }
                    }
                },
            }
        };

        const myChart = new Chart(
            document.getElementById(chartId),
            config
        );
    }

    function multiRowChart($months, $amount, $amount1, chartId, title, title1) {
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

    function circleChart($months, $amount, chartId, title, color) {
        const labels = $months;
        const data = {
            labels: labels,
            datasets: [{
                label: title,
                backgroundColor: color,
                borderWidth: 1,
                data: $amount
            }]
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

    $("#searchBtn").on("click", function() {
        // Fetch data from Laravel backend
        var fyYear = $("#fyYear").val();
        var operator = $("#operator").val();

        fetch('{{ route("manufacture-master.analysis-printing.weightChart") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    'fyYear': fyYear,
                    'operator': operator
                })
            })
            .then(response => response.json()) // Parse JSON response
            .then(data => {
                if (!Array.isArray(data.production) || !Array.isArray(data.rejection)) {
                    console.error("Expected data to be an array, but received:", data);
                    return;
                }

                // Extract 'amounts' and 'dates' from the data if it's an array
                const productiondates = data.production.map(item => item.date);
                const rejectiondates = data.production.map(item => item.date);
                const productionAmounts = data.production.map(item => item.amount);
                const rejectionAmounts = data.rejection.map(item => item.amount);
                const machine = data.machine.map(item => item.name);
                const rectionTotal = data.machine.map(item => item.rectionTotal);
                const productionTotal = data.machine.map(item => item.productionTotal);
                const machineType = data.machineType.map(item => item.name);
                const machTyperectionTotal = data.machineType.map(item => item.rectionTotal);
                const machTypeproductionTotal = data.machineType.map(item => item.productionTotal);
                const operator = data.operator.map(item => item.name);
                const operatorrectionTotal = data.operator.map(item => item.rectionTotal);
                const operatorproductionTotal = data.operator.map(item => item.productionTotal);

                // Call the function to render the chart with dynamic data
                setTimeout(() => {
                    lineChart(productiondates, productionAmounts, 'weightChart', 'Production QTY. ', '#8FBC8F');
                    lineChart(rejectiondates, rejectionAmounts, 'rejectionChart', 'Rejection QTY. ', '#E9967A');
                    lineOrRowChart(machine, rectionTotal, productionTotal, 'machineChart', 'Rejection QTY. ', '#E9967A');
                    lineOrRowChart(machineType, machTyperectionTotal, machTypeproductionTotal, 'machineTypeChart', '#E9967A');
                    lineOrRowChart(operator, operatorrectionTotal, operatorproductionTotal, 'OperatorChart', '#E9967A');
                }, 1000);
            })
            .catch(error => console.error('Error fetching chart data:', error));
    });

    function lineChart($date, $amount, chartId, title, color) {
        const ctx = document.getElementById(chartId);
        if (!ctx) {
            console.error(`Cannot find element with id '${chartId}'`);
            return;
        }

        // Use $amount instead of amounts and $date instead of dates
        const data = $amount.map((y, i) => ({
            x: i,
            y
        }));

        const totalDuration = 10000;
        const delayBetweenPoints = totalDuration / data.length;

        const previousY = (ctx) => ctx.index === 0 ? ctx.chart.scales.y.getPixelForValue(100) :
            ctx.chart.getDatasetMeta(ctx.datasetIndex).data[ctx.index - 1].getProps(['y'], true).y;

        const animation = {
            x: {
                type: 'number',
                easing: 'linear',
                duration: delayBetweenPoints,
                from: NaN,
                delay(ctx) {
                    if (ctx.type !== 'data' || ctx.xStarted) {
                        return 0;
                    }
                    ctx.xStarted = true;
                    return ctx.index * delayBetweenPoints;
                }
            },
            y: {
                type: 'number',
                easing: 'linear',
                duration: delayBetweenPoints,
                from: previousY,
                delay(ctx) {
                    if (ctx.type !== 'data' || ctx.yStarted) {
                        return 0;
                    }
                    ctx.yStarted = true;
                    return ctx.index * delayBetweenPoints;
                }
            }
        };

        const config = {
            type: 'line',
            data: {
                labels: $date, // Use the $date array for the labels
                datasets: [{
                    label: title,
                    borderColor: color,
                    borderWidth: 1,
                    radius: 0,
                    data: data // Use the mapped $amount as data points
                }]
            },
            options: {
                animation,
                interaction: {
                    intersect: false
                },
                scales: {
                    x: {
                        type: 'category',
                        labels: $date, // Use the $date for the x-axis labels
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        new Chart(ctx, config);
    }

    function lineOrRowChart($machine, $rejectionTotal, $productionTotal, chartId, color) {
        const ctx = document.getElementById(chartId);
        if (!ctx) {
            console.error(`Cannot find element with id '${chartId}'`);
            return;
        }

        // Use $rejectionTotal for the bar chart and $productionTotal for the line chart
        const dataRejection = $rejectionTotal.map((y, i) => ({
            x: i,
            y
        }));

        const dataProduction = $productionTotal.map((y, i) => ({
            x: i,
            y
        }));

        const totalDuration = 10000;
        const delayBetweenPoints = totalDuration / dataProduction.length;

        const previousY = (ctx) => ctx.index === 0 ?
            ctx.chart.scales.y.getPixelForValue(100) :
            ctx.chart.getDatasetMeta(ctx.datasetIndex).data[ctx.index - 1].getProps(['y'], true).y;

        const animation = {
            x: {
                type: 'number',
                easing: 'linear',
                duration: delayBetweenPoints,
                from: NaN,
                delay(ctx) {
                    if (ctx.type !== 'data' || ctx.xStarted) {
                        return 0;
                    }
                    ctx.xStarted = true;
                    return ctx.index * delayBetweenPoints;
                }
            },
            y: {
                type: 'number',
                easing: 'linear',
                duration: delayBetweenPoints,
                from: previousY,
                delay(ctx) {
                    if (ctx.type !== 'data' || ctx.yStarted) {
                        return 0;
                    }
                    ctx.yStarted = true;
                    return ctx.index * delayBetweenPoints;
                }
            }
        };

        const config = {
            type: 'bar', // The default type, bar chart for rejections
            data: {
                labels: $machine, // Use the $machine array for the labels
                datasets: [{
                        type: 'bar', // Bar chart for rejection totals
                        label: 'Rejections Qty.',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                        data: dataRejection // Rejection totals as bar chart data points
                    },
                    {
                        type: 'line', // Line chart for production totals
                        label: 'Production Qty.',
                        borderColor: color,
                        borderWidth: 1,
                        radius: 0,
                        fill: false,
                        data: dataProduction // Production totals as line chart data points
                    }
                ]
            },
            options: {
                animation,
                interaction: {
                    intersect: false
                },
                scales: {
                    x: {
                        type: 'category',
                        labels: $machine, // Use the $machine array for the x-axis labels
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Values'
                        }
                    }
                }
            }
        };

        new Chart(ctx, config);
    }
</script>
@endpush
