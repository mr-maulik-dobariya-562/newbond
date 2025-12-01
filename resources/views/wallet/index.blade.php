@extends("Layouts.app")

@section("title", "Wallet")

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
                    Master
                </div>
                <h2 class="page-title">
                    Wallet
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a class="btn btn-primary d-none d-sm-inline-block add-new-btn" data-bs-toggle="modal"
                        data-bs-target="#city-modal" href="#">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Create new Wallet
                    </a>
                    <a class="btn btn-primary d-sm-none btn-icon add-new-btn" data-bs-toggle="modal"
                        data-bs-target="#city-modal" href="#" aria-label="Create new report">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
@section("content")
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-status-top bg-primary"></div>
                <div class="mt-3 p-2">
                    <div class="card-body">
                        <div class="container-fluid">
                            <div class="row row-cards">
                                <div class="col-md-2 ">
                                    <div class="form-group">
                                        <label for="from" class="form-label">From Date</label>
                                        <input type="date" value="<?= date('Y-m-01'); ?>" class=" form-control from"
                                            id="from">
                                    </div>
                                </div>
                                <div class="col-md-2 ">
                                    <div class="form-group">
                                        <label for="to" class="form-label">To Date</label>
                                        <input type="date" class=" form-control to" value="<?= date('Y-m-d'); ?>" id="to">
                                    </div>
                                </div>
                                <div class="col-md-2 ">
                                    <div class="form-group">
                                        <label for="customer_id" class="form-label">Party</label>
                                        <select class="form-control ajax-customer_all" id="customer_id" required>
                                            <option value="">Select Customer</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 ">
                                    <div class="form-group">
                                        <label for="item_id" class="form-label">Txn Type</label>
                                        <select class="form-control select2" id="txn_type">
                                            <option value="">Select Item</option>
                                            <?php foreach ($txnTypes as $txnType) { ?>
                                            <option value="<?= $txnType['id']; ?>"> <?= $txnType['name']; ?> </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="group_by" class="form-label">Type</label>
                                        <select class="form-control select2" id="type">
                                            <option value="">Select Type</option>
                                            <option value="CREDIT">CREDIT</option>
                                            <option value="DEBIT">DEBIT</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-outline-primary float-end" id="search">Search</button>
                    </div>
                </div>
            </div>
            <div class="card mt-2">
                <div class="card-status-top bg-primary"></div>
                <div class="card-header">
                    <h3 class="card-title">Wallet</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-loader table-vcenter card-table" id="Payment-table">
                                <thead>
                                    <tr>
                                        <th data-name="id">Serial No </th>
                                        <th data-name="date">Date</th>
                                        <th data-name="amount">Amount</th>
                                        <th data-name="balance">Balance</th>
                                        <th data-name="txn_type">Txn Type</th>
                                        <th data-name="type">Type</th>
                                        <th data-name="remark">Remark</th>
                                        <th data-name="created_by">Customer</th>
                                        <th data-name="created_at">Bank</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form id="modal-form" action="{{ route('wallet.store') }}" method="POST">
        @csrf
        <div class="modal modal-blur fade" id="payment-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"> <span class="title">Add</span> Bank</h5>
                        <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Customer <span class="text-danger">*</span></label>
                                <select class="form-select customer_id" name="customer_id" required>
                                    <option value="">Select Customer</option>
                                    @foreach ($customers ?? [] as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name ?? '' }} -
                                            ({{ $customer->city->name ?? '' }} -
                                            {{ $customer->partyType->name ?? '' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 pt-2">
                                <label class="form-label">Type</label>
                                <select class="form-select type" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="CREDIT">CREDIT</option>
                                    <option value="DEBIT">DEBIT</option>
                                </select>
                            </div>
                            <div class="col-md-4 pt-2">
                                <label class="form-label">Date<span class="text-danger">*</span></label>
                                <input class="form-control" id="date" type="date" name="date" value="{{ date('Y-m-d') }}"
                                    required />
                            </div>
                            <div class="col-md-4 pt-2">
                                <label class="form-label">Amount<span class="text-danger">*</span></label>
                                <input class="form-control" type="number" name="amount" placeholder="Enter Amount"
                                    required />
                            </div>
                            <div class="col-md-4 pt-2">
                                <label class="form-label">Remark</label>
                                <textarea class="form-control" name="remark"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn me-auto" data-bs-dismiss="modal" type="button">Close</button>
                        <button class="btn btn-primary" type="submit">
                            Save <i class="fa-solid fa-spinner fa-spin ms-1 save-loader" style="display:none"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push("javascript")
    <script>
        $(document).ready(function () {
            const modal = $("#payment-modal");
            $(".customer_id").select2({
                dropdownParent: modal
            });
            $(".type").select2({
                dropdownParent: modal
            });
            window.edit = false;
            var table = window.table(
                "#Payment-table",
                "{{ route('wallet.getList') }}",
                {
                    additionalData: () => {
                        return {
                            from_date: $("#from").val(),
                            to_date: $("#to").val(),
                            customer_id: $("#customer_id").val(),
                            type: $("#type").val(),
                            txn_type: $("#txn_type").val(),
                        };
                    },
                }
            );

            $(".add-new-btn").click(function () {
                modal.modal("show");
                modal.find(".title").text("Add");
                modal.find("select").val("").trigger("change");
                modal.find("input,textarea").val("");
                $("#date").val("{{ date('Y-m-d') }}");
                modal.parents("form").attr("action", '{{ route("wallet.store") }}');
                window.edit = false;
            });

            $("#modal-form").submit(function (e) {
                e.preventDefault();
                const F = $(this)
                removeErrors();
                F.find(".save-loader").show();
                const http = App.http.jqClient;
                var U;
                if (window.edit) {
                    U = http.put(
                        F.attr("action"),
                        F.serialize(),
                    );
                } else {
                    U = http.post(
                        F.attr("action"),
                        F.serialize(),
                    );
                }
                U.then(res => {
                    if (res.success) {
                        table.ajax.reload();
                        modal.modal("hide");
                        sweetAlert("success", res.message);
                    } else {
                        sweetAlert("error", res.message);

                    }
                }).always(() => {
                    F.find(".save-loader").hide()
                })

            });

            $(document).on("click", "#search", function () {
                table.ajax.reload();
            });
        });
    </script>
@endpush
