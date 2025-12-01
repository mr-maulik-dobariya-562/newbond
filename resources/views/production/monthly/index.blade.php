@extends("Layouts.app")

@section("title", "Monthly Inward")

@section("header")
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Production
                </div>
                <h2 class="page-title">
                    Monthly Inward
                </h2>
            </div>
        </div>
    </div>
@endsection
@section("content")
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-status-top bg-primary"></div>
                <div class="p-2">
                    <div class="card-body">
                        <div class="container-fluid">
                            <div class="row row-cards">
                                <div class="col-md-2 ">
                                    <div class="form-group">
                                        <label for="fromDate" class="form-label">From</label>
                                        <input type="date" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-01') }}"
                                            class="form-control from" name="fromDate" required>
                                    </div>
                                </div>
                                <div class="col-md-2 ">
                                    <div class="form-group">
                                        <label for="toDate" class="form-label">To</label>
                                        <input type="date" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}"
                                            class="form-control to" name="toDate" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-outline-primary float-end" id="search">Search</button>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-status-top bg-primary"></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="report"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("javascript")
    <script>
        $(document).ready(function () {


            $(document).on('click', '#printReportTable', function () {
                $("#printReportTable").hide(); // Hide the print button
                $(".td-check").text("-"); // Hide the check button
                let tableHtml = $('#report').html(); // get the rendered table

                let printWindow = window.open('', '', 'height=700,width=1000');

                printWindow.document.write(`
                        <html>
                            <head>
                                <title>Monthly Report Print</title>
                                <style>
                                    body {
                                        font - family: Arial, sans-serif;
                                    }
                                    table {
                                        width: 100%;
                                    border-collapse: collapse;
                                    margin-top: 20px;
                                    }
                                    table, th, td {
                                        border: 1px solid #000;
                                    }
                                    th, td {
                                        padding: 8px;
                                    text-align: center;
                                    }
                                    thead {
                                        background - color: #f0f0f0;
                                    }
                                    tfoot {
                                        background - color: #e0e0e0;
                                    font-weight: bold;
                                    }
                                    h2 {
                                        text - align: center;
                                    margin-bottom: 20px;
                                    }
                                    .all-date{
                                        min-width: 75px;
                                    }
                                </style>
                            </head>
                            <body>
                                ${tableHtml}
                                <script>
                                    window.onload = function() {
                                        window.print();
                                    window.onafterprint = function() {
                                        window.close();
                                        };
                                    };
                                    <\/script>
                            </body>
                        </html>
                    `);

                printWindow.document.close();
                $("#search").trigger("click"); // Re-trigger the search button to show the table again
            });


            $(document).on('click', '#search', function (e) {
                e.preventDefault();
                var fromDate = $('.from').val();
                var toDate = $('.to').val();
                if (fromDate > toDate) {
                    alert("From date should be less than To date");
                    return false;
                }
                $.ajax({
                    url: "{{ route('production.monthlyInward.getList') }}",
                    type: "POST",
                    showLoader: true,
                    data: {
                        fromDate: fromDate,
                        toDate: toDate,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        $('#report').html(response).find('table')
                    },
                    error: function (xhr, status, error) {
                        // Handle errors here
                        console.error(xhr.responseText);
                    }
                })
            });

            $(document).on("click", ".check", function () {
                $.ajax({
                    url: "{{ route('production.monthlyInward.monthlyCheck') }}",
                    type: "POST",
                    showLoader: true,
                    data: {
                        _token: "{{ csrf_token() }}",
                        date: $(this).closest("tr").find("td:first").text()
                    },
                    success: function (response) {
                        $("#search").trigger("click");
                    },
                });
            });
        });
    </script>
@endpush