@extends("Layouts.app")

@section("title", $pageTitle ??= "Estimate Listing")

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

    b {
        font-weight: bolder;
        color: #000000;
    }
</style>
<div class="page-header d-print-none">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                Estimate
            </div>
            <h2 class="page-title">
                {{$pageTitle}}
            </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                @php $createPermission = auth()->user()->hasPermissionTo("estimate-create") @endphp
                @if ($createPermission)
                <a class="btn btn-primary d-none d-sm-inline-block add-new-btn" href="{{ route("estimate.create") }}"
                    data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Create new estimate"
                    data-bs-original-title="Create new estimate">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Create new estimate
                </a>
                @endif
                <a class="btn btn-primary d-sm-none btn-icon add-new-btn" href="{{ route("estimate.create") }}"
                    aria-label="Create new report" data-bs-toggle="tooltip" data-bs-placement="top"
                    aria-label="Create new estimate" data-bs-original-title="Create new estimate">
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
        <div class="accordion card" id="filterAccordion">
            <div class="card-status-top bg-primary"></div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" style="padding: 10px;" data-bs-toggle="collapse"
                        data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Filter Options
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                    data-bs-parent="#filterAccordion">
                    <div class="accordion-body" style="padding: 0px;">
                        <div class="card-body" style="padding: 10px;">
                            <div class="row row-cards">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="from" class="form-label">From Date</label>
                                        <input type="date" value="<?= date('Y-m-01'); ?>" class=" form-control from"
                                            id="from">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="to" class="form-label">To Date</label>
                                        <input type="date" class=" form-control to" value="<?= date('Y-m-d'); ?>"
                                            id="to">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="customer_id" class="form-label">Party</label>
                                        <select class="form-control ajax-customer" multiple="multiple" id="customer_id"
                                            required>
                                            <option value="">Select Customer</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="item_id" class="form-label">Party Types</label>
                                        <select class="form-control select2" id="party_type">
                                            <option value="">Select Type</option>
                                            <?php foreach ($partyTypes as $partyType) { ?>
                                                <option value="<?= $partyType['id']; ?>">
                                                    <?= $partyType['name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="item_id" class="form-label">Party Groups</label>
                                        <select class="form-control select2" multiple="multiple" id="party_group">
                                            <option value="">Select Group</option>
                                            <?php foreach ($partyGroups as $partyGroup) { ?>
                                                <option value="<?= $partyGroup['id']; ?>">
                                                    <?= $partyGroup['name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 ">
                                    <div class="form-group">
                                        <label for="item_id" class="form-label">Item</label>
                                        <select class="form-select select2" id="item_id" multiple="multiple">
                                            <option value="">Select Item</option>
                                            <?php foreach ($item as $item) { ?>
                                                <option value="<?= $item['id']; ?>"><?= $item['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 ">
                                    <div class="form-group">
                                        <label for="print_id" class="form-label">Print Type</label>
                                        <select class="form-control select2" multiple="multiple" id="print_id">
                                            <option value="">Select Print</option>
                                            <?php foreach ($prints as $print) { ?>
                                                <option value="<?= $print['id']; ?>">
                                                    <?= $print['name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="print_id" class="form-label">Invoice</label>
                                        <select class="form-control select2" id="invoice">
                                            <option value="">Select Print</option>
                                            <?php foreach ($invoices as $invoice) { ?>
                                                <option value="<?= $invoice['id']; ?>">
                                                    <?= $invoice['name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="print_id" class="form-label">Bill Generated</label>
                                        <select class="form-control select2" id="bill_generated">
                                            <option value="">Select</option>
                                            <option value="YES">YES</option>
                                            <option value="NO">NO</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="group_by" class="form-label">Group By</label>
                                        <select class="form-control select2" id="group_by">
                                            <option value="">Select Group</option>
                                            <option value="item">Item</option>
                                            <option value="bill" selected>Bill</option>
                                            <option value="customer">Customer</option>
                                            <option value="voucher">Voucher</option>
                                            <option value="created_user">Created User</option>
                                            <option value="print_type">Print Type</option>
                                            <option value="party_group">Party Group</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="transports" class="form-label">Transports</label>
                                        <select class="form-control select2" id="transports">
                                            <option value="">Select Transports</option>
                                            <?php foreach ($transports as $transport) { ?>
                                                <option value="<?= $transport->id ?? ''; ?>">
                                                    <?= $transport->name ?? ''; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="to" class="form-label">Item Search</label>
                                        <input type="text" class="form-control" value="" id="search-field">
                                    </div>
                                </div>
                                <div class="col-md-1 pt-4">
                                    <button type="button" class="btn btn-outline-primary float-end" id="search">Search</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-2">
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
<!-- <form id="lr-form-data" action="{{ route('estimate.lrPhoto') }}" enctype="multipart/form-data" method="POST">
                            @csrf
                            <div class="modal modal-blur fade" id="lr-form" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"> <span class="title">Add</span> LR Photo</h5>
                                            <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="estimateid" id="estimateid" value="" />
                                            <div class="row mb-1 align-items-end">
                                                <div class="col-md-6 pt-2">
                                                    <label class="form-label">LR Image</label>
                                                    <input class="form-control" type="file" name="image" id="image" onchange="previewImage(event)" />
                                                </div>
                                                <div class="col-md-6"></div>
                                                <div class="col-md-12 mt-3">
                                                    <img id="imagePreview" src="" alt="Image Preview" style="display: none; max-width: 30%;" />
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
                        </form> -->
@endsection

@push("javascript")
<script>
    $(document).ready(function() {
        function checkGroupTypes($type = '') {
            const groupByTypes = [
                'item', 'bill', 'customer', 'voucher', 'created_user', 'print_type', 'party_group'
            ];

            return groupByTypes.includes($type);
        }

        $(document).on("click", "#search", function(e) {
            e.preventDefault();
            $('#report').html(null);

            var groupBy = $("#group_by option:selected").val();

            if (checkGroupTypes(groupBy) === false) {
                $("#group_by").select2('open');
                return false;
            }

            data = {
                "from_date": $("#from").val(),
                "to_date": $("#to").val(),
                "customer": $("#customer_id").val(),
                "party_type": $("#party_type option:selected").val(),
                "party_group": $("#party_group").val(),
                "item": $("#item_id").val(),
                "print_id": $("#print_id").val(),
                "bill_generated": $("#bill_generated option:selected").val(),
                "invoice": $("#invoice option:selected").val(),
                "transports": $("#transports option:selected").val(),
                "group": groupBy,
                "search": $("#search-field").val(),
                "url": "purchase",
            }

            $.ajax({
                showLoader: true,
                url: "{{ route('estimate.getList') }}",
                timeout: 8000,
                type: "POST",
                processData: true,
                data: data,
                success: function(response) {
                    $('#report').html(response);

                    // DataTable Configuration
                    let datatableconfig = {
                        "scrollY": "600px", // Adjust height
                        "scrollX": true, // Enable horizontal scrolling
                        "scrollCollapse": true,
                        "paging": false, // Disable pagination
                        "fixedHeader": true, // Fix header
                        'createdRow': window.dataTableDefault.rowCallback
                    };

                    if (groupBy == "bill") {
                        datatableconfig["columnDefs"] = [{
                            "orderable": false,
                            "targets": 0
                        }]
                    }
                    // Initialize DataTable
                    let table = $('.datatable').DataTable(datatableconfig);
                    $('.flex-wrap').addClass('d-none flex-wrap')
                    if (groupBy == "bill") {
                        $('.datatable thead th').eq(0).removeClass('sorting sorting_asc sorting_desc');
                    }
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

        $(document).on('click', '.estimate-excel', function() {
            var form = $(this).closest('form');
            form.find('#type').val($(this).data('type'));
            $.ajax({
                showLoader: true,
                url: "{{ route('export.getEstimateExcel') }}",
                timeout: 8000,
                type: "POST",
                processData: true,
                data: form.serialize(),
                success: function(response) {
                    sweetAlert("success", 'Excel Download Successfully');
                }
            });
        });

        $(document).on('click', '.summary', function() {
            var confirmation = confirm("Are you sure want to download?");

            if (!confirmation) {
                var form = $(this).closest('form');
                form.find('#type').val($(this).data('type'));
                form.find('#status').val('0');
                form.submit();
                return false;
            }

            var form = $(this).closest('form');
            form.find('#type').val($(this).data('type'));
            form.find('#status').val('1');
            form.submit();
        });
    });
</script>
<script>
    $(document).on('change', '.check-item', function(e) {
        const id = $(this).data("id");
        const item = $(this).prop("checked");
        const url = `{{ route('estimate.billGenerated', ['id' => ':id']) }}`.replace(':id', id);
        alert_if(
            "Are you sure to change this?",
            () => {
                App.http.jqClient.post("{{ route('estimate.billGenerated') }}", {
                    id: id,
                    item: item
                }).then(res => {
                    if (res.success) {
                        sweetAlert("success", res.message);
                    } else {
                        sweetAlert("error", res.message);
                    }
                })
            },
            () => {
                $(this).prop('checked', !item);
            }
        );
    })
    $(document).on('click', '.edit-btn', function() {
        const countryModal = $("#lr-form");
        const id = $(this).data("id");
        const image = $(this).data("image");
        const edit_url = "{{ route('estimate.lrPhoto') }}";
        countryModal.find(".title").text("Edit");
        countryModal.find("#estimateid").val(id);

        // Set the image preview
        if (image) {
            $('#imagePreview').attr('src', image).show();
        } else {
            $('#imagePreview').hide();
        }
        countryModal.modal("show");
        window.edit = true;
    })

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
</script>
@endpush
