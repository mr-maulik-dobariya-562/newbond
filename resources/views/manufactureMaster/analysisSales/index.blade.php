@extends('Layouts.app')

@section('title', 'Analysis Sales')

@section('header')
<div class="page-header d-print-none">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                Overview
            </div>
            <h2 class="page-title">
                Analysis Sales
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
@endsection
@push("javascript")
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        $("#searchBtn").on("click", function() {
            var fyYear = $("#fyYear").val();
            $.ajax({
                url: "{{ route('manufacture-master.analysis-sales.getView') }}",
                type: "POST",
                data: {
                    fyYear: fyYear,
                },
                success: function(data) {
                    $("#table").html(data.html);
                    const customers = data.topCustomers.map(item => item.name);
                    const customersOrder = data.topCustomers.map(item => item.total_orders);
                    const item = data.topItems.map(item => item.name);
                    const itemSold = data.topItems.map(item => item.total_quantity_sold);
                    const sales = data.sales.map(item => item.name);
                    const salesSold = data.sales.map(item => item.total_quantity_sold);
                    const order = data.order.map(item => item.name);
                    const orderSold = data.order.map(item => item.total_quantity_sold);
                    const partyGroup = data.saleGroup.map(item => item.name);
                    const partyGroupSold = data.saleGroup.map(item => item.total_quantity_sold);
                    rowchart(customers, customersOrder, 'topCustomer', 'Top Customers');
                    rowchart(item, itemSold, 'topItem', 'Top Items');
                    rowchart(sales, salesSold, 'salesGraph', 'Sales');
                    rowchart(order, orderSold, 'orderGraph', 'Order');
                    rowchart(partyGroup, partyGroupSold, 'salePartyGroup', 'Sale Party Group');
                }
            })
        });

        function rowchart($months, $amount, chartId, title) {
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
</script>
@endpush
