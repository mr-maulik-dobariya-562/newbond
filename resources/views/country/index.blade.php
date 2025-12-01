@extends("Layouts.app")

@section("title", "Country")

@section("header")
    <style>
        #nprogress .bar {
            z-index: 2000;
            /* Adjust higher than the modal's z-index */
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
                    Master
                </div>
                <h2 class="page-title">
                    Country
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a class="btn btn-primary d-none d-sm-inline-block add-new-btn" data-bs-toggle="modal"
                        data-bs-target="#country-modal" href="#">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Create new country
                    </a>
                    <a class="btn btn-primary d-sm-none btn-icon add-new-btn" data-bs-toggle="modal" data-bs-target="#country-modal"
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
                    <h3 class="card-title">Country</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-loader table-vcenter table-hover card-table" data-page_length="100" id="country-table">
                                <thead>
                                    <tr>
                                        <th data-name="id">Serial No </th>
                                        <th data-name="name">Name</th>
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
    <form id="country-form" action="{{ route("master.country.store") }}" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="modal modal-blur fade" id="country-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"> <span class="title">Add</span> Country</h5>
                        <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3 align-items-end">
                            <div class="col">
                                <label class="form-label">Name</label>
                                <input class="form-control" type="text" name="name"
                                    placeholder="Enter Country Name" />
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
            const countryModal = $("#country-modal");
            var table = window.table(
                "#country-table",
                "{{ route('master.country.getList') }}",
            );
            window.edit = false;

            $(".add-new-btn").click(function() {
                countryModal.find(".title").text("Add");
                countryModal.parents("form").attr("action", '{{ route("master.country.store") }}');
                window.edit = false;
            });

            $("#country-form").submit(function(e) {
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
                        countryModal.modal("hide");
                        sweetAlert("success", res.message);
                    } else {
                        sweetAlert("error", res.message);

                    }
                }).always(() => {
                    F.find(".save-loader").hide()
                })

            });

            $(document).on('click', '.edit-btn', function() {
                const id = $(this).data("id");
                const name = $(this).data("name");
                const edit_url = "{{ route('master.country.update', ':id') }}";
                countryModal.parents("form").attr("action", edit_url.replace(":id", id));
                countryModal.find(".title").text("Edit");
                countryModal.find("input[name='name']").val(name);
                countryModal.modal("show");
                window.edit = true;
            })
        });
    </script>
@endpush
