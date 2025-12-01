@extends("Layouts.app")

@section("title", "Payment")

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
                    Payment
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
                        Create new Payment
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
                <div class="card-header">
                    <h3 class="card-title">Payment</h3>
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
                                        <th data-name="payment_type">Payment Type</th>
                                        <th data-name="type">Type</th>
                                        <th data-name="remark">Remark</th>
                                        <th data-name="number">Number</th>
                                        <th data-name="customer_id">Customer</th>
                                        <th data-name="bank_id">Bank</th>
                                        <th data-name="created_by">created by</th>
                                        <th data-name="created_at">Created At</th>
                                        <th data-name="updated_at">Last Update At</th>
                                        <th data-name="action" data-orderable="false">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form id="modal-form" action="{{ route('payment.store') }}" method="POST">
        @csrf
        <div class="modal modal-blur fade" id="payment-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"> <span class="title">Add</span> Payment</h5>
                        <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Customer <span class="text-danger">*</span></label>
                                <select class="form-select" id="customer_id" name="customer_id" required>
                                    <option value="">Select Customer</option>
                                    @foreach ($customers ?? [] as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name ?? '' }} -
                                            ({{ $customer->city->name ?? '' }} -
                                            {{ $customer->partyType->name ?? '' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Bank<span class="text-danger">*</span></label>
                                <select class="form-select bank" id="bank_id" name="bank_id">
                                    <option value="">Select Bank</option>
                                    @foreach ($banks ?? [] as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Payment Type</label>
                                <select class="form-select" id="paymentType" name="payment_type" required>
                                    <option value="">Select Payment Type</option>
                                    <option value="CASH">CASH</option>
                                    <option value="CREDIT_CARD">CREDIT CARD</option>
                                    <option value="DEBIT_CARD">DEBIT CARD</option>
                                    <option value="VOUCHER">VOUCHER</option>
                                    <option value="CHEQUE">CHEQUE</option>
                                </select>
                            </div>
                            <div class="col-md-4 pt-2">
                                <label class="form-label">Type</label>
                                <select class="form-select type" id="type" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="CREDIT" selected>CREDIT</option>
                                    <option value="DEBIT">DEBIT</option>
                                </select>
                            </div>
                            <div class="col-md-4 pt-2">
                                <label class="form-label">Date<span class="text-danger">*</span></label>
                                <input class="form-control" id="date" type="date" name="date" value="{{ date('Y-m-d') }}"
                                    required />
                            </div>
                            <div class="col-md-4 pt-2 cash-payment">
                                <label class="form-label">Transaction Number</label>
                                <input class="form-control number" type="text" name="number" />
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
            $("#customer_id").select2({
                dropdownParent: modal
            });
            $("#bank_id").select2({
                dropdownParent: modal
            });
            $("#paymentType").select2({
                dropdownParent: modal
            });
            $("#type").select2({
                dropdownParent: modal
            });
            window.edit = false;
            var table = window.table(
                "#Payment-table",
                "{{ route('payment.getList') }}",
            );

            $(".add-new-btn").click(function () {
                modal.modal("show");
                modal.find(".title").text("Add");
                modal.find("select").val("").trigger("change");
                modal.find(".type").val("CREDIT").trigger("change");
                modal.find("input,textarea").val("");
                $("#date").val("{{ date('Y-m-d') }}");
                modal.parents("form").attr("action", '{{ route("payment.store") }}');
                window.edit = false;
            });

            $(document).on('change', '.type', function () {
                const type = $(this).val();
                if (type == "DEBIT") {
                    $(".modal-header").css("background-color", "red");
                } else if (type == "CREDIT") {
                    $(".modal-header").css("background-color", "green");
                } else {
                    $(".modal-header").css("background-color", "green");
                }
            });
            $('.type').trigger('change');
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

            }); z

            $(document).on('click', '.edit-btn', function () {
                const {
                    id,
                    date,
                    amount,
                    remark,
                    type,
                    number,
                    payment_type,
                    bank_id,
                    customer_id
                } = $(this).data();
                const edit_url = "{{ route('payment.update', ':id') }}";
                modal.parents("form").attr("action", edit_url.replace(":id", id));
                modal.find(".title").text("Edit");
                modal.find("input[name='date']").val(date);
                modal.find("input[name='amount']").val(amount);
                modal.find("textarea[name='remark']").val(remark);
                modal.find("select[name='type']").val(type).trigger('change');
                modal.find("input[name='number']").val(number);
                modal.find("select[name='payment_type']").val(payment_type).trigger('change');
                modal.find("select[name='bank_id']").val(bank_id).trigger('change');
                modal.find("select[name='customer_id']").val(customer_id).trigger('change');
                modal.modal("show");
                window.edit = true;
            });

            $(document).on('change', '#paymentType', function () {
                const type = $(this).val();
                if (type == "CASH") {
                    modal.find(".number").val('');
                    modal.find(".cash-payment").hide();
                } else {
                    modal.find(".cash-payment").show();
                }
            });
        });
    </script>
@endpush