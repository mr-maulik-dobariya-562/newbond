@extends("Layouts.app")

@section("title", $pageTitle ??= "Quotation Listing")

@section("header")
<style>
    #nprogress .bar {
        z-index: 2000;
    }

    #nprogress .peg {
        box-shadow: 0 0 10px #29d, 0 0 5px #29d;
    }

    .btn-rounded {
        border-radius: 10em;
        padding: 6px 8px;
        font-size: small;
        text-transform: none;
        text-shadow: none !important;
        background: #eaeaea;
        border-color: transparent;
        border: none;
    }
</style>
<div class="page-header d-print-none">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                Quotations
            </div>
            <h2 class="page-title">
                {{$pageTitle}}
            </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a class="btn btn-primary d-none d-sm-inline-block add-new-btn" href="{{ route('quotation.create') }}" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Create new Quotation" data-bs-original-title="Create new Quotation">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Create new Quotation
                </a>
                <a class="btn btn-primary d-sm-none btn-icon add-new-btn" href="{{ route('quotation.create') }}" aria-label="Create new report" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Create new Quotation" data-bs-original-title="Create new Quotation">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
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
                                    <input type="date" value="<?= date('Y-m-01'); ?>" class=" form-control from" id="from">
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
                                    <select class="form-control ajax-customer" id="customer_id" required>
                                        <option value="">Select Customer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 ">
                                <div class="form-group">
                                    <label for="item_id" class="form-label">Item</label>
                                    <select class="form-control select2" id="item_id">
                                        <option value="">Select Item</option>
                                        <?php foreach ($item as $item) { ?>
                                            <option value="<?= $item['id']; ?>">
                                                <?= $item['name']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 ">
                                <div class="form-group">
                                    <label for="print_id" class="form-label">Print Type</label>
                                    <select class="form-control select2" id="print_id">
                                        <option value="">Select Print</option>
                                        <?php foreach ($prints as $print) { ?>
                                            <option value="<?= $print['id']; ?>">
                                                <?= $print['name']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="group_by" class="form-label">Group By</label>
                                    <select class="form-control select2" id="group_by">
                                        <option value="">Select Group</option>
                                        <option value="item">Item</option>
                                        <option value="print_type">Print Type</option>
                                        <option value="bill" selected>Bill</option>
                                        <option value="customer">Customer</option>
                                        <option value="voucher">Voucher</option>
                                        <option value="created_user">Created User</option>
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
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive" id="report"></div>
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

        function checkGroupTypes($type = '') {
            const groupByTypes = [
                'item', 'bill', 'customer', 'voucher', 'created_user', 'print_type'
            ];

            return groupByTypes.includes($type);
        }

        $(document).on("click", "#search", function(e) {
            e.preventDefault();
            $('#report').html(null);

            var groupBy = $("#group_by option:selected").val();

            if (checkGroupTypes(groupBy) === false) {
                // SweetAlert("warning", "Invalid Group type");
                $("#group_by").select2('open');
                return false;
            }

            data = {
                "from_date": $("#from").val(),
                "to_date": $("#to").val(),
                "customer": $("#customer_id option:selected").val(),
                "item": $("#item_id option:selected").val(),
                "print_id": $("#print_id option:selected").val(),
                "group": groupBy,
                "url": "purchase",
            }

            $.ajax({
                showLoader: true,
                url: "{{ route('quotation.getList') }}",
                timeout: 8000,
                type: "POST",
                processData: true,
                data: data,
                success: function(response) {
                    $('#report').html(response);
                    window.table(
                        ".datatable",
                        "",
                    );
                    $('.flex-wrap').addClass('d-none flex-wrap')
                }
            });
        });
        $('#search').trigger('click');

        $(document).on('click', '.print-button', function() {
            var form = $(this).closest('form');
            form.find('#type').val($(this).data('type'));
            form.submit();
        });

        $(document).on('click', '.all-check', function() {
            var checkbox = $('.print-check').prop('checked', $(this).prop('checked'));
        });
    });
</script>
<script>
    function previewImage(event) {
        var input = event.target;
        var reader = new FileReader();
        reader.onload = function() {
            var dataURL = reader.result;
            var imagePreview = document.getElementById('imagePreview');
            imagePreview.src = dataURL;
            imagePreview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }

    $(document).on('change', '.check-item', function() {
        const id = $(this).data("id");
        const item = $(this).prop("checked");
        App.http.jqClient.post("{{ route('quotation.blockFind') }}", {
            id: id,
            item: item
        }).then(res => {
            if (res.success) {
                sweetAlert("success", res.message);
            } else {
                sweetAlert("error", res.message);
            }
        })
    })
</script>
@endpush
