@extends("Layouts.app")

@section("title", "Stock")

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
                Manage Stock
            </div>
            <h2 class="page-title">
                Stock
            </h2>
        </div>
    </div>
</div>
@endsection
@section("content")
<div class="row pt-2">
    <div class="col-md-12">
        <div class="card mt-2">
            <div class="card-status-top bg-primary"></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered" id="table">
                            <thead>
                                <th>id</th>
                                <th>name</th>
                                <th>Date</th>
                                <th>Credit Qty</th>
                                <th>Debit Qty</th>
                                <th>Balance</th>
                            </thead>
                            <tbody>
                                <?php $balance = 0; ?>
                                @foreach ($data as $row)
                                <?php
                                if ($row->source == 'order') {
                                    $balance -= $row->qty;
                                } else {
                                    $balance += $row->qty;
                                }
                                ?>
                                <tr>
                                    <td>{{ $row->detail_id }}</td>
                                    <td>{{ $row->customerName }}</td>
                                    <td>{{ $row->date }}</td>
                                    <td>{{ $row->source == 'inward' && $row->qty > 0 ? $row->qty : 0 }}</td>
                                    <td>{{ $row->source == 'order' && $row->qty > 0 ? $row->qty : 0 }}</td>
                                    <td>{{ $balance }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-right">Total</td>
                                    <td style="color : <?= $balance > 0 ? 'green' : 'red' ?>">
                                        <h3><b>{{ $balance > 0 ? $balance.' CR' : $balance.' DR' }}</b></h3>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push("javascript")
<script>
    $(document).ready(function() {
        $("#searchBtn").on("click", function() {
            var fromDate = $("#fromDate").val();
            var toDate = $("#toDate").val();
            var item = $("#item").val();

            $.ajax({
                url: "{{ route('stock.getListParcel') }}",
                type: "POST",
                data: {
                    fromDate: fromDate,
                    toDate: toDate,
                    item: item
                },
                success: function(data) {
                    $("#table").html(data);
                }
            })
        });
    });
</script>
@endpush
