@extends("Layouts.app")

@section("title", "Cities")

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
                    City
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a class="btn btn-primary d-none d-sm-inline-block add-new-btn" data-bs-toggle="modal"
                        data-bs-target="#city-modal" href="#">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Create new city
                    </a>
                    <a class="btn btn-primary d-sm-none btn-icon add-new-btn" data-bs-toggle="modal"
                        data-bs-target="#city-modal" href="#" aria-label="Create new report">
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
                        <h3 class="card-title">City</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-loader table-vcenter card-table" data-page_length="100" id="city-table">
                                    <thead>
                                        <tr>
                                            <th data-name="id">Serial No </th>
                                            <th data-name="name">Name</th>
                                            <th data-name="state_id">state</th>
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
    <form id="modal-form" action="{{ route('master.city.store') }}" method="POST">
        @csrf
        <div class="modal modal-blur fade" id="city-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"> <span class="title">Add</span> City</h5>
                        <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3 align-items-end">
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Country <span class="text-danger">*</span></label>
                                <select class="form-select select2" id="country" name="country_id">
                                    <option value="">Select Country</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 mb-2">
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

                                                "data" => [

                                                    "country_id" =>  "[name='country_id']"
                                                ]
                                            ],

                                            "allowClear" => true,

                                            "dropdownParent" => "#city-modal",

                                            "placeholder" => __("Select State"),
                                        ],
                                        // "required" => false,
                                    ],
                                    isset($city) && !empty($city->province_id) ? [$city->province_id, $city->province->name] : false,
                                );
                                ?>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="name" placeholder="Enter State Name" />
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
                "#city-table",
                "{{ route('master.city.getList') }}",
            );

            $('#country').select2({
                dropdownParent: modal
            });

            $(".add-new-btn").click(function() {
                modal.find(".title").text("Add");
                modal.find("select").val("");
                modal.parents("form").attr("action", '{{ route("master.city.store") }}');
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
                    country_id,
                    state_name,
                    state_id
                } = $(this).data();
                const edit_url = "{{ route('master.city.update', ':id') }}";
                modal.parents("form").attr("action", edit_url.replace(":id", id));
                modal.find(".title").text("Edit");
                modal.find("input[name='name']").val(name);
                modal.find("[name='country_id']").val(country_id);
                const state = new Option(state_name, state_id, true, true);
                $('#select_state_id').append(state);
                modal.modal("show");
                window.edit = true;
            });

            // var country = $("[name='country_id']");
            // var state = $('#select_state_id');

            // country
            //     .on('change',
            //         function(e, trigger) {
            //             var country_id = $(this).find(":selected").val();
            //             var SelectConfig = state.data('options');
            //             SelectConfig.ajax.data = function(params) {
            //                 return {
            //                     search: params.term,
            //                     country_id,
            //                 };
            //             };
            //             if (!trigger?.programmatically)
            //                 state.empty();
            //             state.select2(SelectConfig);
            //         })
            //     .trigger('change', {
            //         programmatically: true
            //     });
        });
    </script>
@endpush
