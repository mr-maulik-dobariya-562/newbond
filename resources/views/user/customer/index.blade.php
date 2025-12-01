@extends("Layouts.app")

@section("title", "Manage Customers")

@section("header")
@php
$statusList = [
["name" => "All", "count" => 2, "class" => "secondary"],
["name" => "Active", "count" => 5, "class" => "success"],
["name" => "Inactive", "count" => 6, "class" => "danger"],
]
@endphp
<style>
</style>
<div class="page-header d-print-none">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                Manage Users
            </div>
            <h2 class="page-title">
                Customers
            </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a class="btn btn-primary d-none d-sm-inline-block add-new-btn " href="{{ route("customer.create") }}">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Create Customers
                </a>
                <a class="btn btn-primary d-sm-none btn-icon add-new-btn" href="{{ route("customer.create") }}"
                    aria-label="Create new report">
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
                            <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label class="form-label">Party Name <span class="text-danger">*</span></label>
                                    <select class="form-control ajax-customer" data-tags="true" id="party_id"
                                        name="party_id">
                                        <option value="">Select Customer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="form-group">
                                    <label class="form-label">State <span class="text-danger">*</span></label>
                                    <?php
                                    \App\Helpers\Forms::select2(
                                        "state_id",
                                        [
                                            "configs" => [
                                                "ajax" => [
                                                    "type" => "POST",

                                                    "url" => route("common.getStateSelect2"),

                                                    "dataType" => "json",

                                                    "data" => []
                                                ],

                                                "allowClear" => true,

                                                "placeholder" => __("Select State"),
                                            ],
                                            "required" => true,
                                        ],
                                        [],
                                        true
                                    );
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="form-group">
                                    <label class="form-label">City <span class="text-danger">*</span></label>
                                    <?php
                                    \App\Helpers\Forms::select2(
                                        "city_id",
                                        [
                                            "configs" => [
                                                "ajax" => [
                                                    "type" => "POST",

                                                    "url" => route("common.getCitySelect2"),

                                                    "dataType" => "json",

                                                    "data" => [
                                                        "state_id" => "[name='state_id']",
                                                    ],
                                                ],

                                                "allowClear" => true,

                                                "placeholder" => __("Select City"),
                                            ],
                                            "required" => true,
                                        ],
                                        isset($customer) && !empty($customer->city_id) ? [$customer->city_id, $customer->city?->name] : [],
                                        true
                                    );
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="item_id" class="form-label">Party Group</label>
                                    <select class="form-control select2" id="party_group">
                                        <option value="">Select Item</option>
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
                                    <label for="item_id" class="form-label">Party Type</label>
                                    <select class="form-control select2" id="party_type">
                                        <option value="">Select Item</option>
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
                                    <label class='form-label'>Party Category:</label>
                                    <select class="form-control select2" id="bill_type">
                                        <option value="">All</option>
                                        @foreach ($partyCategorys as $partyCategory)
                                        <option value="{{ $partyCategory->id }}">{{ $partyCategory->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="sample" class='form-label'>Sample:</label>
                                    <select class="form-control select2" id="sample">
                                        <option value="">All</option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="from" class="form-label">From Date</label>
                                    <input type="date" value="" class=" form-control from" id="from">
                                </div>
                            </div>
                            <div class="col-md-2 ">
                                <div class="form-group">
                                    <label for="to" class="form-label">To Date</label>
                                    <input type="date" class=" form-control to" value="<?= date('Y-m-d'); ?>" id="to">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-control select2" id="status">
                                        <option value="">All</option>
                                        <option value="ACTIVE">ACTIVE</option>
                                        <option value="INACTIVE">INACTIVE</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class='form-label'>Discount:</label>
                                    <select class="form-control select2" id="discount">
                                        <option value="">All</option>
                                        @foreach ($discount as $discount)
                                        <option value="{{ $discount }}">{{ $discount }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control select2" id="status">
                                    <option value="">All</option>
                                    <option value="ACTIVE">ACTIVE</option>
                                    <option value="INACTIVE">INACTIVE</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-outline-primary float-end" id="search">Search</button>
                </div>
            </div>
        </div>
        <div class="card mt-1">
            <div class="card-status-top bg-primary"></div>
            <div class="card-header d-print-none justify-content-between">
                <h3 class="card-title">
                    Customers
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <form action="{{ route('customer.coverPrint') }}" method="post" target="_blank">
                                @csrf
                                <button type="button" class="print-button btn btn-outline-primary">Cover Print</button>
                                <table class="table table-vcenter table-hover card-table" id="customer-dataTable">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" class="all-check" name="select_all" value="1">
                                            </th>
                                            <th>S.No</th>
                                            <!-- <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Action &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th> -->
                                            <th style="padding-left: 25px !important;padding-right: 34px !important;">
                                                Action</th>
                                            <th style="min-width: 250px;">Name</th>
                                            <th>City</th>
                                            <th>Mobile</th>
                                            <th style="max-width: 200px;">Email</th>
                                            <th>State</th>
                                            <th>Country</th>
                                            <th>Address</th>
                                            <th>Coutact Person</th>
                                            <th>Address 2</th>
                                            <th>Pincode</th>
                                            <th>Password</th>
                                            <th>Pay Terms</th>
                                            <th>Party Type</th>
                                            <th>Party Category</th>
                                            <th>Party Group</th>
                                            <th>GST</th>
                                            <th>PAN</th>
                                            <th>Courier</th>
                                            <th>Transport</th>
                                            <th>Reason Remark</th>
                                            <th>Status</th>
                                            <th>Reference</th>
                                            <th>Parent Customer</th>
                                            <th>Created By</th>
                                            <th>Created At</th>
                                            <th>Updated At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </form>
                        </div>
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
        var table = $('#customer-dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('customer.getList') }}",
                type: 'POST',
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.party_type = $('#party_type').val();
                    d.party_group = $('#party_group').val();
                    d.status = $('#status').val();
                    d.bill_type = $('#bill_type').val();
                    d.sample = $('#sample').val();
                    d.fromDate = $('#from').val();
                    d.toDate = $('#to').val();
                    d.partyId = $('#party_id').val();
                    d.state = $('#select_state_id').val();
                    d.city = $('#select_city_id').val();
                    d.discount = $('#discount').val();
                },
            },
            columns: [{
                    data: 'checkbox',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, full, meta) {
                        return '<input type="checkbox" class="print-check" name="id[]" value="' + full.id + '">';
                    }
                },
                {
                    data: 'id'
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name'
                },
                {
                    data: 'city'
                },
                {
                    data: 'mobile'
                },
                {
                    data: 'email'
                },
                {
                    data: 'state'
                },
                {
                    data: 'country'
                },
                {
                    data: 'address'
                },
                {
                    data: 'contact_person'
                },
                {
                    data: 'area'
                },
                {
                    data: 'pincode'
                },
                {
                    data: 'password'
                },
                {
                    data: 'pay_terms'
                },
                {
                    data: 'party_type_id'
                },
                {
                    data: 'partyCategory'
                },
                {
                    data: 'partyGroup'
                },
                {
                    data: 'gst'
                },
                {
                    data: 'pan_no'
                },
                {
                    data: 'courier'
                },
                {
                    data: 'transport'
                },
                {
                    data: 'other_reason_remark'
                },
                {
                    data: 'status'
                },
                {
                    data: 'reference'
                },
                {
                    data: 'parent'
                },
                {
                    data: 'created_by'
                },
                {
                    data: 'created_at',
                    render: function(data, type, full, meta) {
                        return new Date(data).toLocaleString('en-GB', {
                            timeZone: 'Asia/Kolkata',
                            hour12: false
                        });
                    }
                },
                {
                    data: 'updated_at',
                    render: function(data, type, full, meta) {
                        return new Date(data).toLocaleString('en-GB', {
                            timeZone: 'Asia/Kolkata',
                            hour12: false
                        });
                    }
                },
            ],
            order: [
                [1, 'asc']
            ],
            responsive: false,
            lengthMenu: [10, 100, 500, 1000, 2000, 5000],
            pageLength: 100,
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>" +
                "B",
            buttons: ["excel"],
            rowCallback: function(row, data) {
                $(row).on('click', function() {
                    $('#customer-dataTable tbody tr').css('background-color', ''); // clear all first
                    $(this).css('background-color', '#FFCC99'); // highlight clicked row
                }).css('cursor', 'pointer');
            }
        });

        table
            .buttons()
            .container()
            .appendTo(`#datatable11_wrapper .col-md-6:eq(0)`);

        $(document).on('click', '.print-button', function() {
            var form = $(this).closest('form');
            form.find('#type').val($(this).data('type'));
            form.submit();
        });

        $(document).on('click', '.all-check', function() {
            $('.print-check').prop('checked', $(this).prop('checked'));
        });

        $(document).on('click', '#search', function() {
            table.draw();
        });
    });
</script>
@endpush
