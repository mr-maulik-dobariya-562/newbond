@extends("Layouts.app")

@section("title", "Molding")

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
                Molding
            </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a class="btn btn-primary d-none d-sm-inline-block add-new-btn" data-bs-toggle="modal"
                    data-bs-target="#city-modal" href="#">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Create new Molding
                </a>
                <a class="btn btn-primary d-sm-none btn-icon add-new-btn" data-bs-toggle="modal"
                    data-bs-target="#city-modal" href="#" aria-label="Create new report">
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
                            <div class="col-md-2 ">
                                <div class="form-group">
                                    <label for="from" class="form-label">From Date</label>
                                    <input type="date" value="<?= date('Y-m-01'); ?>" class="form-control from"
                                        id="from">
                                </div>
                            </div>
                            <div class="col-md-2 ">
                                <div class="form-group">
                                    <label for="to" class="form-label">To Date</label>
                                    <input type="date" class="form-control to" value="<?= date('Y-m-d'); ?>" id="to">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Operator Name</label>
                                <select class="form-select filterSelect2" id="operator_id">
                                    <option value="">Select Operator Name</option>
                                    @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Shift</label>
                                <select class="form-select filterSelect2" id="shift_id">
                                    <option value="">Select Shift</option>
                                    @foreach ($shifts as $shift)
                                    <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Machine Number</label>
                                <select class="form-select filterSelect2" id="machine_id">
                                    <option value="">Select Machine</option>
                                    @foreach ($machineData as $machineDa)
                                    <option value="{{ $machineDa->id }}">
                                        {{ $machineDa->name . ' ' . $machineDa->location->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mt-3">
                                <label class="form-label">Product Type</label>
                                <select class="form-select filterSelect2" id="product_type_id">
                                    <option value="">Select Operator Name</option>
                                    @foreach ($productTypes as $productType)
                                    <option value="{{ $productType->id }}">{{ $productType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Row Material Code</label>
                                <select class="form-select filterSelect2" id="row_material_id">
                                    <option value="">Select Row Material</option>
                                    @foreach ($rowMaterials as $rowMaterial)
                                    <option value="{{ $rowMaterial->id }}">{{ $rowMaterial->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="group_by" class="form-label">Group By</label>
                                    <select class="form-control filterSelect2" id="group_by" required>
                                        <option value="">Select Group</option>
                                        <option value="operator">Operator</option>
                                        <option value="machine">Machine</option>
                                        <option value="voucher" selected>Voucher</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Locations</label>
                                <select class="form-select filterSelect2" id="location_id">
                                    <option value="">Select Location</option>
                                    @foreach ($location as $r)
                                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mt-3">
                                <label class="form-label">Product Name</label>
                                <select class="form-select filterSelect2" id="product_id">
                                    <option value="">Select Product Name</option>
                                    @foreach ($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
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
        <div class="card mt-3">
            <div class="card-status-top bg-primary"></div>
            <div class="card-header">
                <h3 class="card-title">Molding</h3>
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
<form id="modal-form" action="{{ route('production.molding.store') }}" method="POST">
    @csrf
    <div class="modal modal-blur fade" id="city-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> <span class="title">Add</span> Molding</h5>
                    <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input class="form-control date" type="date" name="date" placeholder="Enter date" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Shift <span class="text-danger">*</span></label>
                            <select class="form-select select2" name="shift_id">
                                <option value="">Select Shift</option>
                                @foreach ($shifts as $shift)
                                <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Machine Number <span class="text-danger">*</span></label>
                            <select class="form-select modalSelect2" id="machine_id" name="machine_id">
                                <option value="">Select Machine</option>
                                @foreach ($machines as $machine)
                                <option value="{{ $machine->id }}">{{ $machine->name . ' ' . $machine->location->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Operator Name <span class="text-danger">*</span></label>
                            <select class="form-select modalSelect2" id="operator_id" multiple="multiple"
                                name="operator_id[]"></select>
                        </div>
                        <div class="col-md-3 mt-3">
                            <label class="form-label">Product Type <span class="text-danger">*</span></label>
                            <select class="form-select select2" name="product_type_id">
                                <option value="">Select Product Type</option>
                                @foreach ($productTypes as $productType)
                                <option value="{{ $productType->id }}">{{ $productType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Row Material Code <span class="text-danger">*</span></label>
                            <select class="form-select select2" name="row_material_id">
                                <option value="">Select Row Material</option>
                                @foreach ($rowMaterials as $rowMaterial)
                                <option value="{{ $rowMaterial->id }}">{{ $rowMaterial->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Color Type <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="color_type" placeholder="Enter Color Type" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Number of Cavity <span class="text-danger">*</span></label>
                            <select class="form-select select2" name="cavity_id">
                                <option value="">Select Cavity</option>
                                @foreach ($cavitys as $cavity)
                                <option value="{{ $cavity->id }}">{{ $cavity->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <select class="form-select select2" name="item_id">
                                <option value="">Select Product Name</option>
                                @foreach ($items as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">Machine Counter - Shot Number <span
                                    class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="machine_counter"
                                placeholder="Enter Machine Counter" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Production Weight <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="production_weight"
                                placeholder="Enter Production Weight" />
                        </div>
                        <div class="col-md-2 mt-3">
                            <label class="form-label">Production Qty(Pcs) <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="production_pieces_quantity"
                                placeholder="Enter Production Quantity" />
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Runner Waste(Kg) <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="runner_waste"
                                placeholder="Enter Runner Waste" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Component Rejection(Kg) <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="component_rejection"
                                placeholder="Enter Component Rejection" />
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Remark</label>
                            <input class="form-control" type="text" name="remark" placeholder="Remark" />
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
                "product_type_id": $("#product_type_id option:selected").val(),
                "row_material_id": $("#row_material_id option:selected").val(),
                "shift_id": $("#shift_id option:selected").val(),
                "machine_id": $("#machine_id option:selected").val(),
                "location_id": $('#location_id').val(),
                "product_id": $('#product_id').val(),
                "group": groupBy,
                "url": "purchase",
            }

            $.ajax({
                showLoader: true,
                url: "{{ route('production.molding.getList') }}",
                timeout: 8000,
                type: "POST",
                processData: true,
                data: data,
                success: function(response) {
                    $('#report').html(response);
                }
            });
        });

        $('#search').trigger('click');

        $(".select2", modal).select2({
            dropdownParent: modal
        })

        $('.filterSelect2').select2({
            dropdownParent: $('.row-cards')
        });

        $(".add-new-btn").click(function() {
            $('.modalSelect2').select2({
                dropdownParent: modal
            });
            modal.find(".title").text("Add");
            modal.find("select").val("").trigger("change");
            modal.find("input").val("");
            modal.find('.date').val("{{ date('Y-m-d') }}");
            modal.parents("form").attr("action", '{{ route("production.molding.store") }}');
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
                shift_id,
                machine_id,
                operator_id,
                product_type_id,
                row_material_id,
                cavity_id,
                item_id,
                machine_counter,
                production_weight,
                production_pieces_quantity,
                runner_waste,
                component_rejection,
                color_type,
                remark,
            } = $(this).data();
            const edit_url = "{{ route('production.molding.update', ':id') }}";
            modal.parents("form").attr("action", edit_url.replace(":id", id));
            modal.find(".title").text("Edit");
            modal.find("input[name='date']").val(date);
            modal.find("select[name='shift_id']").val(shift_id).trigger("change");
            modal.find("select[name='machine_id']").val(machine_id).trigger("change");
            modal.find("select[name='product_type_id']").val(product_type_id).trigger("change");
            modal.find("select[name='row_material_id']").val(row_material_id).trigger("change");
            modal.find("select[name='item_id']").val(item_id).trigger("change");
            modal.find("select[name='cavity_id']").val(cavity_id).trigger("change");
            modal.find("input[name='machine_counter']").val(machine_counter);
            modal.find("input[name='production_weight']").val(production_weight);
            modal.find("input[name='production_pieces_quantity']").val(production_pieces_quantity);
            modal.find("input[name='runner_waste']").val(runner_waste);
            modal.find("input[name='component_rejection']").val(component_rejection);
            modal.find("input[name='remark']").val(remark);
            modal.find("input[name='color_type']").val(color_type);
            setTimeout(() => {
                const operator_ids = typeof operator_id === "string" ? operator_id.split(",") : [operator_id];
                modal.find("#operator_id").val(operator_ids).trigger("change");
            }, 500);
            modal.modal("show");
            window.edit = true;
        });

        $(document).on('change', '#machine_id', function() {
            const machine_id = $(this).val();
            const url = "{{ route('production.molding.getoperator') }}";
            $.ajax({
                showLoader: true,
                url: url,
                timeout: 8000,
                type: "POST",
                processData: true,
                data: {
                    "machine_id": machine_id,
                },
                success: function(response) {
                    const select = modal.find("#operator_id");
                    select.find('option').remove().end();
                    $.each(response, function(key, value) {
                        select.append($("<option></option>").attr("value", key).text(value));
                    });
                }
            });
        });
    });
</script>
@endpush
