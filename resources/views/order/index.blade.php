@extends("Layouts.app")

@section("title", $pageTitle ??= "Order Listing")

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
                Order
            </div>
            <h2 class="page-title">
                {{$pageTitle}}
            </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                @php $createPermission = auth()->user()->hasPermissionTo("order-create") @endphp
                @if ($createPermission)
                <a class="btn btn-primary d-none d-sm-inline-block add-new-btn" href="{{ route('order.create') }}"
                    data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Create new Order"
                    data-bs-original-title="Create new Order">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Create new Order
                </a>
                @endif
                <a class="btn btn-primary d-sm-none btn-icon add-new-btn" href="{{ route('order.create') }}"
                    aria-label="Create new report" data-bs-toggle="tooltip" data-bs-placement="top"
                    aria-label="Create new Order" data-bs-original-title="Create new Order">
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
                    <button class="accordion-button" style="padding: 10px;" type="button" data-bs-toggle="collapse"
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
                                        <input type="date" value="" class="form-control" id="from">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="to" class="form-label">To Date</label>
                                        <input type="date" class="form-control" value="<?= date('Y-m-d'); ?>" id="to">
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
                                <div class="col-md-1">
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
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="item_id" class="form-label">Item</label>
                                        <select class="form-control select2" multiple="multiple" id="item_id">
                                            <option value="">Select Item</option>
                                            <?php foreach ($item as $item) { ?>
                                                <option value="<?= $item['id']; ?>">
                                                    <?= $item['name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1">
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
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="group_by" class="form-label">Group By</label>
                                        <select class="form-control select2" id="group_by">
                                            <option value="">Select Group</option>
                                            <option value="item">Item</option>
                                            <option value="print_type">Print Type</option>
                                            <option value="bill">Bill</option>
                                            <option value="customer">Customer</option>
                                            <option value="voucher">Voucher</option>
                                            <option value="created_user">Created User</option>
                                            <option value="party_group">Party Group</option>
                                            <option value="bill_print" selected>Bill And Print Type</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group pt-1">
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input group_by_checkbox" id="group_by_checkbox_bill_print" name="group_by_checkbox" value="bill_print" checked>
                                            <label class="form-check-label" for="group_by_checkbox_bill_print">Bill And Print Type</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input group_by_checkbox" id="group_by_checkbox_voucher" name="group_by_checkbox" value="voucher">
                                            <label class="form-check-label" for="group_by_checkbox_voucher">Voucher</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input group_by_checkbox" id="group_by_checkbox_none" name="group_by_checkbox" value="none">
                                            <label class="form-check-label" for="group_by_checkbox_none">None</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="blockFind" class="form-label">Block Find</label>
                                        <select class="form-control select2" id="blockFind">
                                            <option value="">Select Group</option>
                                            <option value="Yes">YES</option>
                                            <option value="No">NO</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="to" class="form-label">Printing Detail</label>
                                        <input type="text" class=" form-control to" value="" id="printing_detail">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-control select2" id="status">
                                            <option value="all">All</option>
                                            <option value="pending" selected>Pending Order</option>
                                            <option value="dispatched">Dispatched Order</option>
                                            <option value="cancelled">Cancelled Order</option>
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
                                    <div class="form-group">
                                        <button type="button" class="btn btn-outline-primary float-end" id="search">Search</button>
                                    </div>
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
@endsection

@push("javascript")
<script>
    $(document).ready(function() {

        function checkGroupTypes($type = '') {
            const groupByTypes = [
                'item', 'bill', 'customer', 'voucher', 'created_user', 'print_type', 'party_group', 'bill_print',
            ];
            return groupByTypes.includes($type);
        }

        $('#group_by').prop('disabled', true).css('opacity', 0.5);

        $(document).on("click", "#search", function(e) {
            e.preventDefault();
            $('#report').html(null);

            $('.group_by_checkbox').each(function() {
                if ($(this).is(':checked')) {
                    groupBy = $(this).val();
                }
            });

            if (groupBy === 'none') {
                var groupBy = $("#group_by option:selected").val();
                if (checkGroupTypes(groupBy) === false) {
                    $("#group_by").select2('open');
                    return false;
                }

                $('#group_by').prop('disabled', false).css('opacity', 0.5);
            }

            data = {
                "from_date": $("#from").val(),
                "to_date": $("#to").val(),
                "customer": $("#customer_id").val(),
                "party_type": $("#party_type option:selected").val(),
                "party_group": $("#party_group").val(),
                "item": $("#item_id").val(),
                "print_id": $("#print_id").val(),
                "block_find": $("#blockFind option:selected").val(),
                "printing_detail": $("#printing_detail").val(),
                "status": $("#status option:selected").val(),
                "parentUser": $("#parentUser option:selected").val(),
                "search": $("#search-field").val(),
                "group": groupBy,
                "url": "purchase",
            }

            $.ajax({
                showLoader: true,
                url: "{{ route('order.getList') }}",
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

                    // console.log(window.dataTableDefault.rowCallback);


                    // Apply Grouping Rules
                    if (groupBy == "bill") {
                        datatableconfig["columnDefs"] = [{
                            "orderable": false,
                            "targets": 0
                        }];
                    }

                    // Initialize DataTable
                    let table = $('.datatable').DataTable(datatableconfig);

                    // Move Footer to the Top
                    // $('.datatable tfoot').insertBefore($('.datatable thead'));

                    // Fix Sorting Issue for GroupBy
                    if (groupBy == "bill") {
                        $('.datatable thead th').eq(0).removeClass('sorting sorting_asc sorting_desc');
                    }

                    // Apply Select2 on Status Dropdown
                    $('.status').select2({
                        width: '100%',
                    });

                    // Ensure Header and Footer are Fixed
                    $('.dataTables_scrollHeadInner').css('width', '100%');
                    $('.dataTables_scrollFootInner').css('width', '100%');
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

        $(document).on('focus', '.status', function() {
            // Store the previous value before the change happens
            $(this).data('prev-val', $(this).val());
        });

        $(document).on('change', '.status', function() {
            var id = $(this).data('id');
            var status = $(this).val();
            var $this = $(this); // Cache the element for use in callbacks

            var confirmChange = confirm("Are you sure you want to change the status of this order?");

            if (confirmChange) {
                App.http.jqClient.post("{{ route('order.changeStatus') }}", {
                    id: id,
                    status: status
                }).then(res => {
                    $('#search').trigger('click');
                    if (res.success) {
                        sweetAlert("success", res.message);
                    } else {
                        sweetAlert("error", res.message);
                        $this.val($this.data('prev-val'));
                    }
                }).catch(() => {
                    $('#search').trigger('click');
                    sweetAlert("error", "There was an error processing the request.");
                    $this.val($this.data('prev-val'));
                });
            } else {
                $this.val($this.data('prev-val'));
            }
        });

        $(document).on('click', '.group_by_checkbox', function() {
            var value = $(this).val();
            if (value === 'none') {
                $('#group_by').prop('disabled', false).css('opacity', 0.5);
            } else {
                $('#group_by').prop('disabled', true).css('opacity', 1);
            }
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

    $(document).on('change', '.check-item', function(e) {
        const id = $(this).data("id");
        const item = $(this).prop("checked");
        alert_if(
            "Are you sure to change this?",
            () => {
                App.http.jqClient.post("{{ route('order.blockFind') }}", {
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
</script>
@endpush
