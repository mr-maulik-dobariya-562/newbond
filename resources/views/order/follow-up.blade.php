@extends("Layouts.app")

@section("title", "Follow Ups")

@section("header")
    <link rel="stylesheet" href="https://adminlte.io/themes/v3/plugins/fullcalendar/main.css" />
    @push('styles')
        <style>
            #full_calendar_events .table-bordered td,
            #full_calendar_events .table-bordered th {
                border: 1px solid #dee2e6;
            }
        </style>
    @endpush
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Follow Ups
                </h2>
            </div>
            @can('follow-up-create')
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a class="btn btn-primary d-none d-sm-inline-block add-new-btn" data-bs-toggle="modal"
                            data-bs-target="#modal" href="#">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg>
                            Create new follow ups
                        </a>
                        <a class="btn btn-primary d-sm-none btn-icon add-new-btn" data-bs-toggle="modal" data-bs-target="#modal"
                            href="#" aria-label="Create new report">
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
            @endcan
        </div>
    </div>
@endsection
@section("content")
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-status-top bg-primary"></div>
                <div class="card-header justify-content-between">
                    <h3 class="card-title">Follow Ups List</h3>
                    <div class="btn-list ms-auto">
                        <a class="btn btn-primary d-none d-sm-inline-block calendar" data-bs-toggle="modal"
                            data-bs-target="#calendar-model" href="#">
                            Follow Up Calendar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-loader table-vcenter table-hover card-table" id="table"
                                orderable='desc'>
                                <thead>
                                    <tr>
                                        <th data-name="id">Serial No </th>
                                        <th data-name="customer_id">Customer</th>
                                        <th data-name="date">Follow-Up date</th>
                                        <th data-name="status">Status</th>
                                        <th data-name="remark">Remark</th>
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
    <form id="country-form" action="{{ route('follow-up.store') }}" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="modal modal-blur fade" id="modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog  modal-dialog-scrollable modal-xl" role="document">
                <div class="modal-content">
                    <div class="progress progress-sm" style="display:none">
                        <div class="progress-bar progress-bar-indeterminate bg-primary"></div>
                    </div>
                    <div class="modal-header">
                        <h5 class="modal-title"> <span class="title">Add</span> Follow Up</h5>
                        <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-1 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Customer <span class="text-danger">*</span></label>
                                <select class="form-select select2" id="customer_id" name="customer_id">
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name ?? '' }} -
                                            ({{ $customer->city->name ?? '' }} - {{ $customer->partyType->name ?? '' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select select2" id="status" name="status">
                                    <option value="">Select Status</option>
                                    @foreach(config("status.followup") as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 pt-3">
                                <label class="form-label">Next Follow Up date <span class="text-danger">*</span></label>
                                <input class="form-control" type="date" id="date" name="date" />
                            </div>
                            <div class="col-md-12 pt-3">
                                <label class="form-label">Remark <span class="text-secondary">(Optional)</span></label>
                                <textarea class="form-control" type="text" name="remark"
                                    placeholder="Enter Reason for remakr follow Up"></textarea>
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
    <div class="modal modal-blur fade" id="calendar-model" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content">
                <div class="progress progress-sm" style="display:none">
                    <div class="progress-bar progress-bar-indeterminate bg-primary"></div>
                </div>
                <div class="modal-header">
                    <h5 class="modal-title"> <span class="title">Add</span> Follow Up</h5>
                    <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="full_calendar_events"></div>
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
@endsection

@push("javascript")
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://adminlte.io/themes/v3/plugins/fullcalendar/main.js"></script>
    <script>
        $(document).ready(function () {

            $("#customer_id").select2({
                dropdownParent: modal
            });
            $("#status").select2({
                dropdownParent: modal
            });

            var SITEURL = "{{ url('/') }}";

            var Calendar = FullCalendar.Calendar;
            var calendarEl = document.getElementById('full_calendar_events');

            var date = new Date()
            var d = date.getDate(),
                m = date.getMonth(),
                y = date.getFullYear()


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function calendarRender() {
                var calendar = new Calendar(calendarEl, {
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    themeSystem: 'bootstrap',
                    events: function (fetchInfo, successCallback, failureCallback) {
                        $.ajax({
                            url: '/calendar-event',
                            method: 'GET',
                            dataType: 'json',
                            data: {
                                start: fetchInfo.startStr,
                                end: fetchInfo.endStr
                            },
                            success: function (response) {
                                if (response && response.length > 0) {
                                    successCallback(response);
                                } else {
                                    successCallback([]);
                                }
                            },
                            error: function () {
                                failureCallback();
                            }
                        });
                    },
                    editable: false,
                    droppable: false,
                    drop: function (info) {
                        if (checkbox.checked) {
                            info.draggedEl.parentNode.removeChild(info.draggedEl);
                        }
                    }
                });
                calendar.render()
            }

            const calendarModel = $("#calendar-model");
            $(".calendar").click(function () {
                calendarModel.modal("show");
                $(".page-loader").fadeIn();
                setTimeout(() => {
                    calendarRender();
                    $(".page-loader").fadeOut();
                }, 500);
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            const modal = $("#modal");
            var table = window.table(
                "#table",
                "{{ route('follow-up.getList') }}",
            );
            window.edit = false;
            $('.status', modal).select2({
                dropdownParent: modal
            });
            $(".add-new-btn").click(function () {
                $('#imagePreview').hide();
                $("#customer_id").val('').trigger('change')
                $("#status").val('').trigger('change')
                modal.find("input,textarea").val("");
                $("#date").val("{{ date('Y-m-d') }}");
                modal.find(".title").text("Add");
                modal.parents("form").attr("action", '{{ route("follow-up.store") }}');
                window.edit = false;
            });

            $("#country-form").submit(function (e) {
                e.preventDefault();
                removeErrors();
                const F = $(this);
                const summitter = F.find("button[type=submit]");
                summitter.prop('disabled', true);
                F.find(".save-loader,.progress").show();
                var actionUrl = F.attr("action");
                var RT = window.edit ? 'put' : 'post';
                const http = App.http.jqClient;
                http[RT](
                    F.attr("action"),
                    F.serialize(),
                ).then(res => {
                    if (res.success) {
                        table.ajax.reload();
                        modal.modal("hide");
                        sweetAlert("success", res.message);
                    } else {
                        sweetAlert("error", res.message);

                    }
                }).always(() => {
                    summitter.prop('disabled', false);
                    F.find(".save-loader,.progress").hide()
                })

            });

            $(document).on('click', '.edit-btn', function () {
                const {
                    date,
                    customer_id,
                    customer,
                    remark,
                    status,
                    id
                } = $(this).data();
                const edit_url = "{{ route('follow-up.update', ':id') }}";
                modal.parents("form").attr("action", edit_url.replace(":id", id));
                modal.find(".title").text("Edit");
                modal.find("#date").val(date);
                modal.find("[name='remark']").val(remark);
                modal.find("#customer_id").val(customer_id).trigger('change');
                modal.find("#status").val(status).trigger('change');
                // const status = new Option(status, status, true, true);
                // $('.status').append(status);
                modal.modal("show");
                window.edit = true;
            })


        });
    </script>
    <script>
        function previewImage(event) {
            var input = event.target;
            var reader = new FileReader();
            reader.onload = function () {
                var dataURL = reader.result;
                var imagePreview = document.getElementById('imagePreview');
                imagePreview.src = dataURL;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    </script>
@endpush
`