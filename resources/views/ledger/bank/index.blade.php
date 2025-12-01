@extends("Layouts.app")

@section("title", "Bank Ledger")

@section("header")
    <style>
        #nprogress .bar {
            z-index: 2000;
        }

        #nprogress .peg {
            box-shadow: 0 0 10px #29d, 0 0 5px #29d;
        }
    </style>
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Manage Order
                </div>
                <h2 class="page-title">
                    Bank Ledger
                </h2>
            </div>
        </div>
    </div>
@endsection
@section("content")
    <div class="row pt-2">
        <div class="col-md-12">
            <div class="card">
                <div class="card-status-top bg-primary"></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <label class="form-label">From Date</label>
                            <input type="date" class="form-control" id="fromDate">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">To Date</label>
                            <input type="date" class="form-control" id="toDate" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Customer</label>
                            <select class="form-select select2" name="customer" id="customer">
                                <option value="ALL">All</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name ?? '' }} -
                                        ({{ $customer->city->name ?? '' }} -
                                        {{ $customer->partyType->name ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Bank</label>
                            <select class="form-select select2" name="bank" id="bank">
                                <option value="ALL">All</option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label"></label></br>
                            <button class="btn btn-primary" id="searchBtn">Search</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-2">
                <div class="card-status-top bg-primary"></div>
                <div class="card-body">
                    <div class="row">
                        <div id="table"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("javascript")
    <script>
        $(document).ready(function () {

            $("#searchBtn").on("click", function () {
                var fromDate = $("#fromDate").val();
                var toDate = $("#toDate").val();
                var bank = $("#bank").val();
                var customer = $("#customer").val();

                $.ajax({
                    url: "{{ route('bank-ledger.getList') }}",
                    type: "POST",
                    data: {
                        fromDate: fromDate,
                        toDate: toDate,
                        bank: bank,
                        customer: customer
                    },
                    success: function (data) {
                        $("#table").html(data);
                    }
                })
            });
        });
    </script>
@endpush