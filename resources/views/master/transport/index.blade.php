@extends("Layouts.app")

@section("title", "Transport")

@section("header")
    <style>
    </style>
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Master
                </div>
                <h2 class="page-title">
                    Transport
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a class="btn btn-primary d-none d-sm-inline-block add-new-btn" data-bs-toggle="modal"
                        data-bs-target="#modal" href="#">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Create new Transport
                    </a>
                    <a class="btn btn-primary d-sm-none btn-icon add-new-btn" data-bs-toggle="modal" data-bs-target="#modal"
                        href="#" aria-label="Create new report">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
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
                    <h3 class="card-title">Transport</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-loader table-vcenter table-hover card-table" data-page_length="100" id="datatable">
                                <thead>
                                    <tr>
                                        <th data-name="id">Serial No </th>
                                        <th data-name="name">Name</th>
                                        <th data-name="branch">Branch</th>
                                        <th data-name="contact_no">Contact No</th>
                                        <th data-name="is_waybill">Way-Bill ICompulsory</th>
                                        <th data-name="created_by">Created By</th>
                                        <th data-name="created_at">Created At</th>
                                        <th data-name="updated_at">Last Update At</th>
                                        <th data-name="action" data-orderable="false">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form id="modal-form" action="{{ route('master.transport.store') }}" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="modal modal-blur fade" id="modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"> <span class="title">Add</span> Transport</h5>
                        <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3 align-items-end">
                            <div class="col-md-12 mb-4">
                                <label class="form-label">Name</label>
                                <input class="form-control" type="text" name="name" placeholder="Enter Transport Name" />
                            </div>
                            <div id="newRows"></div>
                            <button id="addRow" class="btn btn-primary">Add Row</button>
                            <div class="col-md-12 mb-4">
                                <label class="form-label">Remarks</label>
                                <textarea class="form-control" name='remark' placeholder="Enter Remarks......"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-check">
                                    <input class="form-check-input not-empty" type="checkbox" name='is_waybill' value='1' />
                                    <span class="form-check-label">Way bill is compulsory</span>
                                </label>
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
            const http = App.http.jqClient;
            const modal = $("#modal");
            var table = window.table(
                "#datatable",
                "{{ route('master.transport.getList') }}",
            );
            window.edit = false;

            $(".add-new-btn").click(function() {
                $("input[type=checkbox]").prop("checked", false);
                modal.find(".title").text("Add");
                modal.parents("form").attr("action", '{{ route("master.transport.store") }}');
                window.edit = false;
                var newRow = `
                    <div class="row addedRow">
                        <div class="col-md-7 mb-2">
                            <label class="form-label">Branch</label>
                            <input class="form-control" type="text" name="branch[]" placeholder="Enter Branch Name" />
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Contact No.</label>
                            <input class="form-control" type="text" name="contact_no[]" placeholder="Enter Contact No" />
                        </div>
                        <div class="col-md-1 pt-5">
                            <button class="btn btn-danger removeRow"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </div>`;

                // Append the new row to the container
                $('#newRows').append(newRow);
            });

            $("#modal-form").submit(function(e) {
                e.preventDefault();
                const formSubbmiter = $(e.originalEvent.submitter);
                const F = $(this)
                removeErrors();
                F.find(".save-loader").show();
                formSubbmiter.attr("disabled", "disabled");

                var U;
                if (window.edit) {
                    U = http.put(
                        F.attr("action"),
                        F.serialize(),
                    )
                } else {
                    U = http.post(
                        F.attr("action"),
                        F.serialize(),
                    )
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
                    formSubbmiter.removeAttr("disabled");
                    F.find(".save-loader").hide();
                })

            });

            $(document).on('click', '.edit-btn', function() {
                const id = $(this).data("id");
                const name = $(this).data("name");
                const remark = $(this).data("remark");
                const is_waybill = $(this).data("is_waybill");
                const branches = $(this).data("branches"); // Expect branches to be array of branch names
                const contactNos = $(this).data("contact_nos"); // Expect contact numbers to be array

                const edit_url = "{{ route('master.transport.update', ':id') }}";
                modal.parents("form").attr("action", edit_url.replace(":id", id));
                modal.find(".title").text("Edit");
                modal.find("input[name='name']").val(name);
                modal.find("textarea[name='remark']").val(remark);
                modal.find("input[name='is_waybill']").prop('checked', +is_waybill == 1);

                // Clear old rows before appending new ones
                $('#newRows').empty();

                // Append rows for each branch and contact number
                if (branches && contactNos && branches.length === contactNos.length && branches.length > 0) {
                    branches.forEach((branch, index) => {
                        var newRow = `
                            <div class="row addedRow">
                                <div class="col-md-7 mb-2">
                                    <label class="form-label">Branch</label>
                                    <input class="form-control" type="text" name="branch[]" value="${branch}" placeholder="Enter Branch Name" />
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Contact No.</label>
                                    <input class="form-control" type="text" name="contact_no[]" value="${contactNos[index]}" placeholder="Enter Contact No" />
                                </div>
                                <div class="col-md-1 pt-5">
                                    <button class="btn btn-danger removeRow"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </div>`;
                        $('#newRows').append(newRow);
                    });
                }else{
                    var newRow = `
                    <div class="row addedRow">
                        <div class="col-md-7 mb-2">
                            <label class="form-label">Branch</label>
                            <input class="form-control" type="text" name="branch[]" placeholder="Enter Branch Name" />
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Contact No.</label>
                            <input class="form-control" type="text" name="contact_no[]" placeholder="Enter Contact No" />
                        </div>
                        <div class="col-md-1 pt-5">
                            <button class="btn btn-danger removeRow"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </div>`;

                    // Append the new row to the container
                    $('#newRows').append(newRow);
                }

                modal.modal("show");
                window.edit = true;
            });
        });

        $(document).ready(function() {
            $('#addRow').click(function(e) {
                e.preventDefault();
                // Create a new row with a remove button
                var newRow = `
                    <div class="row addedRow">
                        <div class="col-md-7 mb-2">
                            <label class="form-label">Branch</label>
                            <input class="form-control" type="text" name="branch[]" placeholder="Enter Branch Name" />
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Contact No.</label>
                            <input class="form-control" type="text" name="contact_no[]" placeholder="Enter Contact No" />
                        </div>
                        <div class="col-md-1 pt-5">
                            <button class="btn btn-danger removeRow"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </div>`;

                // Append the new row to the container
                $('#newRows').append(newRow);

                // Scroll to the new row
                $('#newRows').children().last()[0].scrollIntoView({ behavior: 'smooth' });
            });

            // Delegate event listener to dynamically added remove buttons
            $(document).on('click', '.removeRow', function(e) {
                e.preventDefault();
                $(this).closest('.addedRow').remove(); // Remove only the clicked row
            });
        });


    </script>
@endpush
