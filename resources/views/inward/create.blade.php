@extends("Layouts.app")

@section("title", isset($inward) ? __("Edit Inward", ["inward" => $inward->id]) : "Create New Inward")
@php
$actionRoute = isset($inward) ? route("inward.update", ["inward" => $inward->id]) : route("inward.store");
@endphp

@section("header")
<style>
    .no-spinners::-webkit-outer-spin-button,
    .no-spinners::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .no-spinners {
        -moz-appearance: textfield;
    }

    .table-responsive {
        position: relative;
    }

    .table-responsive thead {
        position: sticky;
        top: 0;
        z-index: 100;
        background-color: white;
        /* Ensures the header has a background */
    }
</style>
<div class="page-header d-print-none ">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                Manage Inward
            </div>
            <h2 class="page-title">
                {{ isset($inward) ? "Edit Inward" : "Create New Inward" }}
            </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a class="btn btn-primary d-none d-sm-inline-block add-new-btn" href="{{ route('inward.index') }}">
                    <i class="fa-solid fa-arrow-left me-2"></i>
                    Back
                </a>
                <a class="btn btn-primary d-sm-none btn-icon add-new-btn" href="{{ route('inward.index') }}" aria-label="Return to List">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section("content")
<div class="row">
    <div class="col-md-12">
        <form id="customerForm" action="{{ $actionRoute }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if (isset($inward))
            @method("PUT")
            @else
            @method("POST")
            @endif
            <div class="card">
                <div class="card-status-top bg-primary"></div>
                <div class="progress progress-sm" style="display:none">
                    <div class="progress-bar progress-bar-indeterminate bg-primary"></div>
                </div>
                <div class="card-body">
                    <div class="row pb-2">
                        <!-- <div class="col-md-12 mb-3"> -->
                        <!-- <div class="row"> -->
                        <div class="col-md-3">
                            <label class='form-label required'>Date : </label>
                            <!-- <div class="col"> -->
                            <input class='form-control date @error("date") is-invalid @enderror' value='{{ old("date", $inward?->date ?? date("Y-m-d")) }}' name="detail_date" placeholder="Date" type="date" required>
                            <x-input-error class="mt-2" :messages="$errors->get('date')" />
                            <!-- </div> -->
                        </div>
                        <div class="pt-4">
                            <div class="col-md-12">
                                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                    <table class="estimate-add-product table card-table table-vcenter text-nowrap text-nowrap" id="targetTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <td>Item</td>
                                                <td>Parcel</td>
                                                <td>Quantity</td>
                                                <td>Remarks</td>
                                                <td>Is Special</td>
                                                <td>Action</td>
                                            </tr>
                                        </thead>
                                        <tbody class="append-here">
                                            @if (isset($inwardDetail) && count($inwardDetail) > 0)
                                            @foreach ($inwardDetail as $key => $detail)
                                            <tr>
                                                <input type="hidden" class="inward_detail_id" name="inward_detail_id[]" value='{{ $detail?->id ?? "" }}'>
                                                <td style="padding: 2px !important">
                                                    <?php
                                                    \App\Helpers\Forms::select2(
                                                        "item_id[]",
                                                        [
                                                            "configs" => [
                                                                "width" => "100%",

                                                                "ajax" => [


                                                                    "type" => "POST",

                                                                    "url" => route("inward.getItem"),

                                                                    "dataType" => "json"
                                                                ],

                                                                "allowClear" => false,

                                                                "placeholder" => __("Select Item"),
                                                            ],
                                                            "id" => false,
                                                            "required" => true,
                                                            "class" => "item_id"
                                                        ],
                                                        isset($detail) ? [$detail->item_id, $detail->item->name . ' - ' . $detail->item->packing] : false,
                                                    );
                                                    ?>
                                                </td>
                                                <td style="padding: 2px !important">
                                                    <input class='form-control parcel no-spinners @error("parcel") is-invalid @enderror' value='{{ old("parcel", $detail?->parcel ?? "") }}' name="parcel[]" placeholder="Parcel" step="any" type="number" required>
                                                </td>
                                                <td style="padding: 2px !important">
                                                    <input class='form-control qty no-spinners @error("qty") is-invalid @enderror' value='{{ old("qty", $detail?->qty ?? "") }}' name="qty[]" placeholder="Quantity" step="any" type="number" disabled>
                                                </td>
                                                <td style="padding: 2px !important">
                                                    <input class='form-control remark @error("remark") is-invalid @enderror' value='{{ old("remark", $detail?->remark ?? "") }}' name="remark[]" placeholder="Remark" type="text">
                                                </td>
                                                <td style="text-align: center;">
                                                    <input class="form-check-input is_special_checkbox @error('is_special') is-invalid @enderror" {{ old("is_special", $detail?->is_special ?? "") == 1 ? "checked" : "" }} type="checkbox">
                                                    <input class="form-check-input is_special" name="is_special[]" value="{{$detail?->is_special}}" type="hidden">
                                                </td>
                                                <td style="padding: 2px !important">
                                                    <button type="button" class="btn btn-danger remove-btn"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr class="mainRow">
                                                <input type="hidden" class="inward_detail_id" name="inward_detail_id[]" value=''>
                                                <td style="padding: 2px !important">
                                                    <?php
                                                    \App\Helpers\Forms::select2(

                                                        "item_id[]",

                                                        [
                                                            "configs" => [
                                                                "width" => "100%",

                                                                "ajax" => [

                                                                    "type" => "POST",

                                                                    "url" => route("inward.getItem"),

                                                                    "dataType" => "json",
                                                                ],

                                                                "allowClear" => false,

                                                                "placeholder" => __("Select Item"),
                                                            ],
                                                            "id" => false,
                                                            "required" => true,

                                                            "class" => "item_id"
                                                        ],
                                                    );
                                                    ?>
                                                </td>
                                                <td style="padding: 2px !important">
                                                    <input class='form-control parcel no-spinners @error("parcel") is-invalid @enderror' value='{{ old("parcel", $detail?->parcel ?? "") }}' name="parcel[]" placeholder="Parcel" step="any" type="number" required>
                                                </td>
                                                <td style="padding: 2px !important">
                                                    <input class='form-control qty no-spinners @error("qty") is-invalid @enderror' value='{{ old("qty", $detail?->qty ?? "") }}' name="qty[]" placeholder="Quantity" step="any" type="number" disabled>
                                                </td>
                                                <td style="padding: 2px !important">
                                                    <input class='form-control remark @error("remark") is-invalid @enderror' value='{{ old("remark", $detail?->remark ?? "") }}' name="remark[]" placeholder="Remark" type="text">
                                                </td>
                                                <td style="text-align: center;">
                                                    <input class="form-check-input is_special_checkbox @error('is_special') is-invalid @enderror" type="checkbox">
                                                    <input class="form-check-input is_special" name="is_special[]" value="0" type="hidden">
                                                </td>
                                                <td style="padding: 2px !important">
                                                    <button type="button" class="btn btn-danger form-control remove-btn"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Total Qty</th>
                                                <th></th>
                                                <th id="total_qty">
                                                    @if (isset($inward) && !empty($inward))
                                                    {{ $quantityTotal }}
                                                    @endif
                                                </th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="text-left pt-1">
                                    <button type="button" class="btn btn-success addButton"><i class="fas fa-plus"></i> &nbsp;Add</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <button class="btn btn-primary ms-auto" type="submit">
                            Submit <i class="fa-solid fa-spinner fa-spin ms-1 save-loader" style="display:none"></i>
                        </button>
                        <a class="btn btn-warning me-2" href="{{ route('inward.index') }}">Back</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push("javascript")
<script>
    const selector = {
        customer: $('#customer')

    }
    selector.customer.change(function(...args) {
        const option = $(this).find("option:selected").data()
    });
    $(document).ready(function() {
        $("#customerForm").submit(function(e) {
            e.preventDefault();
            $('.qty').prop('disabled', false);
            const F = $(this)
            removeErrors();
            F.find(".save-loader").show();
            const http = App.http.jqClient;
            http[window.edit ? 'put' : 'post'](
                F.attr("action"),
                F.serialize()
            ).then(res => {
                if (res.success) {
                    sweetAlert("success", res.message);
                    setTimeout(() => {
                        window.location = "{{ route('inward.index') }}";
                    }, 1000);
                } else {
                    $('.qty').prop('disabled', true);
                    sweetAlert("error", res.message);
                }
            }).always(() => {
                F.find(".save-loader").hide()
            }).catch(function() {
                $('html, body').animate({
                    scrollTop: 0
                }, 'slow');
            });
        });
    });
</script>

<script>
    window.edit = <?php echo isset($inward) ? "true" : "false"; ?>;
</script>

<script>
    // Store the main row to clone
    var mainRow = $('.append-here tr').first().clone();
    $(document).ready(function() {

        $(document).on('click', '.addButton', function() {
            // Get the last selected item_id in the table
            var lastGroupSelected = $('.append-here tr').last().find('.narration').val();

            // Clone the stored main row
            var clonedRow = mainRow.clone();

            // Clear the input fields in the cloned row
            clonedRow.find('.qty').val('');
            clonedRow.find('.remark').val('');
            clonedRow.find('.inward_detail_id').val('');
            clonedRow.find('.item_id').val('').trigger('change');
            // Append the cloned row to the table
            $(".append-here").append(clonedRow);
            $(".append-here tr").last().find('.dungdt-select2-field').trigger('re-select2');
            clonedRow.find('.item_id').select2('open');

            scrollToBottom();
        });

        function scrollToBottom() {
            var $tableContainer = $('#targetTable').closest('.table-responsive');
            $tableContainer.animate({
                scrollTop: $tableContainer[0].scrollHeight
            }, 500);
        }

        $(document).on('click', '.remove-btn', function() {
            var $row = $(this).closest('tr');
            var $tbody = $row.closest('tbody');

            if ($tbody.find('tr').length > 1) {
                $row.remove();
            } else {
                sweetAlert("error", 'Last row cannot be deleted.');
            }
            $('.qty').trigger('keyup');
        });

        $(document).on('keyup', '.item_id, .parcel, .qty', function(e) {
            total($(this));
        });

        function total(ref) {
            var sum = 0;

            $('.qty').each(function() {
                sum += +$(this).val();
            });

            $('#total_qty').text(sum);
        }

        $(document).on('select2:select keyup', '.item_id, .parcel, .qty', function(e) {
            let el = $(this).closest('tr');
            let parcel = el.find('.parcel').val();
            let qty = el.find('.qty').val();
            let itemId = el.find('.item_id').val();

            if (e.type === 'select2:select' && itemId) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('inward.getItemData') }}",
                    data: {
                        itemId: itemId
                    },
                    success: function(response) {
                        let packing = response.packing;

                        el.data('packing', packing);

                        if (el.find('.is_special_checkbox').is(':checked')) {
                            el.find('.qty').prop('disabled', false);
                        } else {
                            el.find('.parcel').val() > 1 ? el.find('.qty').val(packing * parcel) : el.find('.qty').val(packing);
                        }
                    }
                });
            }

            let packing = el.data('packing');
            if (packing) {
                if (el.find('.is_special_checkbox').is(':checked')) {
                    el.find('.qty').prop('disabled', false);
                } else {
                    if ($(e.target).hasClass('parcel')) {
                        el.find('.qty').val(parcel > 1 ? packing * parcel : packing);
                    }
                }
                if ($(e.target).hasClass('qty')) {
                    el.find('.parcel').val(qty / packing);
                }
            }
        });

        $(document).on('change', '.is_special_checkbox', function() {
            if ($(this).is(':checked')) {
                $(this).parents('tr').find('.is_special').val(1);
            } else {
                $(this).parents('tr').find('.is_special').val(0);
            }
        });
    });
</script>
@endpush
