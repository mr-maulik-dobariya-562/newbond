@extends("Layouts.app")

@section("title", "Party Type")

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
                    Party Type
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <!-- <a class="btn btn-primary d-none d-sm-inline-block add-new-btn " data-bs-toggle="modal"
                        data-bs-target="#party-type-modal" href="#">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Create new party type
                    </a> -->
                    <a class="btn btn-primary d-sm-none btn-icon add-new-btn" data-bs-toggle="modal" data-bs-target="#party-type-modal"
                        href="#" aria-label="Create new report">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
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
                    <h3 class="card-title">Party Type</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-loader table-loader table-vcenter table-hover card-table" id="datatable">
                                <thead>
                                    <tr>
                                        <th data-name="id">Serial No </th>
                                        <th data-name="name">Name</th>
                                        <th data-name="item">Item Discount</th>
                                        <th data-name="item_price">Item Price</th>
                                        <th data-name="extra_price">Extra Price</th>
                                        <th data-name="created_by">Created By</th>
                                        <th data-name="action">Action</th>
                                        <th data-name="created_at">Created At</th>
                                        <th data-name="updated_at">Updated At</th>
                                        <!-- <th data-name="action" data-orderable="false">Action</th> -->
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
    <form id="modal-form" action="{{ route('master.party-type.store') }}" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="modal modal-blur fade" id="party-type-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="progress progress-sm" style="display:none">
                        <div class="progress-bar progress-bar-indeterminate bg-primary"></div>
                    </div>
                    <div class="modal-header">
                        <h5 class="modal-title"> <span class="title">Add</span> Party Type</h5>
                        <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3 align-items-end">
                            <div class="col">
                                <label class="form-label">Color</label>
                                <input class="form-control" type="color" name="color" />
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
            const modal = $("#party-type-modal");
            var table = window.table(
                "#datatable",
                "{{ route('master.party-type.getList') }}",
            );
            window.edit = false;

            $(".add-new-btn").click(function() {
                modal.find(".title").text("Add");
                modal.parents("form").attr("action", '{{ route("master.party-type.store") }}');
                window.edit = false;
            });

            $("#modal-form").submit(function(e) {
                e.preventDefault();
                const F = $(this)
                removeErrors();
                F.find(".save-loader,.progress").show();
                const http = App.http.jqClient;
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
                    F.find(".save-loader,.progress").hide()
                })

            });

            $(document).on('click', '.edit-btn', function() {
                const id = $(this).data("id");
                const color = $(this).data("color");
                const edit_url = "{{ route('master.party-type.update', ':id') }}";
                modal.parents("form").attr("action", edit_url.replace(":id", id));
                modal.find(".title").text("Edit");
                modal.find("input[name='color']").val(color);
                modal.modal("show");
                window.edit = true;
            })

            $(document).on('change', '.check-item', function() {
                const id = $(this).data("party_id");
                const item = $(this).prop("checked");
                App.http.jqClient.post("{{ route('master.party-type.checkItem') }}", {
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

            $(document).on('change', '.itemPrice', function() {
                const id = $(this).data("party_id");
                const item = $(this).val();
                App.http.jqClient.post("{{ route('master.party-type.itemPrice') }}", {
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

            $(document).on('change', '.extraPrice', function() {
                const id = $(this).data("party_id");
                const item = $(this).val();
                App.http.jqClient.post("{{ route('master.party-type.extraPrice') }}", {
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

            setTimeout(function() {
                $('.itemPrice').select2({
                    width: '100%'
                });

                $('.extraPrice').select2({
                    width: '100%'
                });
            }, 1000);
        });
    </script>
@endpush
