@extends("Layouts.app")

@section("title", "Employee")

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
                Manufacture Master
            </div>
            <h2 class="page-title">
                Employee
            </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a class="btn btn-primary d-none d-sm-inline-block add-new-btn" data-bs-toggle="modal" data-bs-target="#city-modal" href="#">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Create new Employee
                </a>
                <a class="btn btn-primary d-sm-none btn-icon add-new-btn" data-bs-toggle="modal" data-bs-target="#city-modal" href="#" aria-label="Create new report">
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
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <label class="form-label">Location</label>
                        <select class="form-select select2" multiple name="location_id[]" id="location_id" required>
                            <option value="">Select Location</option>
                            @foreach ($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
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
            <div class="card-header">
                <h3 class="card-title">Employee</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-loader table-vcenter card-table" id="bank-table">
                            <thead>
                                <tr>
                                    <th data-name="id">Serial No </th>
                                    <th data-name="name">Name</th>
                                    <th data-name="email">Email</th>
                                    <th data-name="mobile">Mobile</th>
                                    <th data-name="status">Status</th>
                                    <th data-name="address">Address</th>
                                    <th data-name="location">Location</th>
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
<form id="modal-form" action="" method="POST">
    @csrf
    <div class="modal modal-blur fade" id="city-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> <span class="title">Add</span> Employee</h5>
                    <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="name" placeholder="Enter Name" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input class="form-control" type="text" name="email" placeholder="Enter Email" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mobile</label>
                            <input class="form-control" type="number" name="mobile" placeholder="Enter Number" required />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select class="form-select select2" name="status" required>
                                <option value="">Select Status</option>
                                @foreach (['ACTIVE','INACTIVE'] as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Employee Type <span class="text-danger">*</span></label>
                            <select class="form-select select2" name="type" required>
                                <option value="">Select Type</option>
                                @foreach (['Printing','Molding','Both'] as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Location</label>
                            <select class="form-select select2" multiple name="location[]" required>
                                <option value="">Select Location</option>
                                @foreach ($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" placeholder="Enter Address"></textarea>
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
    $(document).ready(function() {
        const modal = $("#city-modal");
        window.edit = false;
        var table = window.table(
            "#bank-table",
            "{{ route('manufacture-master.employee.getList') }}",
            {
                additionalData: () => {
                    return {
                        location_id: $("#location_id").val(),
                    };
                },
            }
        );

        $('#searchBtn').on('click', function() {
            table.ajax.reload();
        });

        $(".add-new-btn").click(function() {
            modal.find(".title").text("Add");
            modal.find("select").val("").trigger("change");
            modal.find("textarea").val("");
            modal.parents("form").attr("action", '{{ route("manufacture-master.employee.store") }}');
            window.edit = false;
        });

        $("#modal-form").submit(function(e) {
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

        $(document).on('click', '.edit-btn', function() {
            const {
                id,
                name,
                email,
                mobile,
                address,
                status,
                type,
                location
            } = $(this).data();
            const edit_url = "{{ route('manufacture-master.employee.update', ':id') }}";
            modal.parents("form").attr("action", edit_url.replace(":id", id));
            modal.find(".title").text("Edit");
            modal.find("input[name='name']").val(name);
            modal.find("input[name='email']").val(email);
            modal.find("input[name='mobile']").val(mobile);
            modal.find("textarea[name='address']").val(address);
            modal.find("select[name='status']").val(status).trigger("change");
            modal.find("select[name='type']").val(type).trigger("change");

            const location_ids = typeof location === "string" ? location.split(",") : [location];
            modal.find("select[name='location[]']").val(location_ids).trigger("change");
            modal.modal("show");
            window.edit = true;
        });
    });
</script>
@endpush
