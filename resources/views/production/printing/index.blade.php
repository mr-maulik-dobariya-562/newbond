@extends("Layouts.app")

@section("title", "Printing")

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
                Production
            </div>
            <h2 class="page-title">
                Printing
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
                    Create new Printing
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
            <div class="mt-3 p-2">
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="row row-cards">
                            <div class="col-md-2 ">
                                <div class="form-group">
                                    <label for="from" class="form-label">From Date</label>
                                    <input type="date" value="<?= date('Y-m-01'); ?>" class="form-control from" id="from">
                                </div>
                            </div>
                            <div class="col-md-2 ">
                                <div class="form-group">
                                    <label for="to" class="form-label">To Date</label>
                                    <input type="date" class="form-control to" value="<?= date('Y-m-d'); ?>" id="to">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Operator Name <span class="text-danger">*</span></label>
                                <select class="form-select select2" id="operator_id">
                                    <option value="">Select Operator Name</option>
                                    @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Print Type <span class="text-danger">*</span></label>
                                <select class="form-select select2" id="print_type_id">
                                    <option value="">Select Shift</option>
                                    @foreach ($printTypes as $printType)
                                    <option value="{{ $printType->id }}">{{ $printType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Machine Number <span class="text-danger">*</span></label>
                                <select class="form-select select2" id="machine_id">
                                    <option value="">Select Machine</option>
                                    @foreach ($machineData as $machineD)
                                    <option value="{{ $machineD->id }}">{{ $machineD->name.' '.$machineD->machineType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Working Hours <span class="text-danger">*</span></label>
                                <select class="form-select select2" id="working_hours_id">
                                    <option value="">Select Row Material</option>
                                    @foreach ($workingHours as $workingHour)
                                    <option value="{{ $workingHour->id }}">{{ $workingHour->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="group_by" class="form-label">Group By</label>
                                    <select class="form-control select2" id="group_by" required>
                                        <option value="">Select Group</option>
                                        <option value="operator">Operator</option>
                                        <option value="machine">Machine</option>
                                        <option value="voucher" selected>Voucher</option>
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
        <div class="card mt-3">
            <div class="card-status-top bg-primary"></div>
            <div class="card-header">
                <h3 class="card-title">Printing List</h3>
            </div>
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
<form id="modal-form" action="{{ route('production.printing.store') }}" method="POST">
    @csrf
    <div class="modal modal-blur fade" id="city-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> <span class="title">Add</span> Printing</h5>
                    <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input class="form-control date" type="date" name="date" placeholder="Enter date" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Type of Printing <span class="text-danger">*</span></label>
                            <select class="form-select select2" name="print_type_id">
                                <option value="">Select Printing</option>
                                @foreach ($printTypes as $printType)
                                <option value="{{ $printType->id }}">{{ $printType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Machine Number <span class="text-danger">*</span></label>
                            <select class="form-select select2" name="machine_id">
                                <option value="">Select Machine</option>
                                @foreach ($machines as $machine)
                                <option value="{{ $machine->id }}">{{ $machine->name.' - '.$machine->machineType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Operator Name <span class="text-danger">*</span></label>
                            <select class="form-select select2" multiple="multiple" name="operator_id[]">
                                @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mt-3">
                            <label class="form-label">Production Qty.(Pieces.) <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="production_qty" placeholder="Enter Production Qty" />
                        </div>
                        <div class="col-md-3 mt-3">
                            <label class="form-label">Rejection Qty.(Pieces.)</label>
                            <input class="form-control" type="text" name="rejection_qty" placeholder="Enter Rejection Qty" />
                        </div>
                        <div class="col-md-3 mt-3">
                            <label class="form-label">Working Hours <span class="text-danger">*</span>(Hours-Minutes)</label>
                            <select class="form-select working_hours" name="working_hours_id">
                                <option value="">Select Row Material</option>
                                @foreach ($workingHours as $workingHour)
                                <option value="{{ $workingHour->id }}">{{ $workingHour->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mt-3">
                            <label class="form-label">Rejection Qty.(Kgs.)</label>
                            <input class="form-control" type="text" name="rection_qty" placeholder="Enter Production Qty" />
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">Rejection Reason</label>
                            <input class="form-control" type="text" name="rejection_reason" placeholder="Enter Rejection Reason" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Remarks</label>
                            <input class="form-control" type="text" name="remarks" placeholder="Enter Remarks" />
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

        $(".select2", modal).select2({
            dropdownParent: modal
        });

        $(".working_hours").select2({
            tags: true,
            dropdownParent: modal
        });

        function checkGroupTypes($type = '') {
            const groupByTypes = [
                'operator', 'machine', 'voucher',
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
                "operator_id": $("#operator_id option:selected").val(),
                "printing_type_id": $("#print_type_id option:selected").val(),
                "machine_id": $("#machine_id option:selected").val(),
                "working_hours_id": $("#working_hours_id option:selected").val(),
                "group": groupBy,
                "url": "purchase",
            }

            $.ajax({
                showLoader: true,
                url: "{{ route('production.printing.getList') }}",
                timeout: 8000,
                type: "POST",
                processData: true,
                data: data,
                success: function(response) {
                    $('#report').html(response).find('table').DataTable({
						rowCallback: function(row, data) {
                        $(row).on('click', function() {
                                $('table tbody tr',$('#report')).css('background-color', '');
                                $(this).css('background-color', '#FFCC99');
                            })
                            .css('cursor', 'pointer');
                        }
				    });
                }
            });
        });

        $('#search').trigger('click');

        $(".add-new-btn").click(function() {
            modal.find(".title").text("Add");
            modal.find("select").val("").trigger("change");
            modal.find("input").val("");
            modal.find('.date').val("{{ date('Y-m-d') }}");
            modal.parents("form").attr("action", '{{ route("production.printing.store") }}');
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
                    $('#search').trigger('click');
                    modal.modal("hide");
                    sweetAlert("success", res.message);
                    location.reload();
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
                date,
                print_type_id,
                machine_id,
                operator_id,
                production_qty,
                rection_qty,
                working_hours_id,
                rejection_qty,
                rejection_reason,
                remarks
            } = $(this).data();
            const edit_url = "{{ route('production.printing.update', ':id') }}";
            modal.parents("form").attr("action", edit_url.replace(":id", id));
            modal.find(".title").text("Edit");
            modal.find("input[name='date']").val(date);
            modal.find("select[name='print_type_id']").val(print_type_id).trigger("change");
            modal.find("select[name='machine_id']").val(machine_id).trigger("change");
            const operator_ids = typeof operator_id === "string" ? operator_id.split(",") : [operator_id];
            modal.find("select[name='operator_id[]']").val(operator_ids).trigger("change");
            modal.find("select[name='working_hours_id']").val(working_hours_id).trigger("change");
            modal.find("input[name='rection_qty']").val(rection_qty);
            modal.find("input[name='production_qty']").val(production_qty);
            modal.find("input[name='rejection_qty']").val(rejection_qty);
            modal.find("input[name='rejection_reason']").val(rejection_reason);
            modal.find("input[name='remarks']").val(remarks);
            modal.modal("show");
            window.edit = true;
        });
    });
</script>
@endpush
