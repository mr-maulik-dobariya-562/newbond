@extends("Layouts.app")

@section("title", isset($quotation) ? __("Edit Quotation", ["quotation" => $quotation->id]) : "Create New Quotation")
@php
$actionRoute = isset($quotation) ? route("quotation.update", ["quotation" => $quotation->id]) : route("quotation.store");
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
                Quotation
            </div>
            <h2 class="page-title">
                {{ isset($quotation) ? "Edit Quotation" : "Create New Quotation" }}
            </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a class="btn btn-primary d-none d-sm-inline-block add-new-btn" href="{{ route('quotation.index') }}">
                    <i class="fa-solid fa-arrow-left me-2"></i>
                    Back
                </a>
                <a class="btn btn-primary d-sm-none btn-icon add-new-btn" href="{{ route('quotation.index') }}" aria-label="Create new report">
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
            @if (isset($quotation))
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
                    <div class="row">
                        <!-- <div class="col-md-12 mb-3"> -->
                        <!-- <div class="row"> -->
                        <div class="col-md-3">
                            <label class="form-label required">Party Name :</label>
                            <!-- <div class="col"> -->
                            <select class="form-control ajax-customer" id="customer" name="customer_id" required>
                            <option value="{{ optional($quotation ?? null)->customer?->id }}" selected >{{ optional($quotation ?? null)->customer?->name }}</option>
                            </select>
                            <!-- </div> -->
                        </div>

                        <div class="col-md-3">
                            <label class="form-label required">Print Type : </label>
                            <!-- <div class="col"> -->
                            <select class="form-control printType" name="print_type_id">
                                <option value="0">All </option>
                                @foreach ($printTypes ?? [] as $printType)
                                <option value="{{ $printType->id }}" {{ isset($quotation->print_type_id) && $printType->id == $quotation?->print_type_id ? 'selected' : '' }}> {{ $printType->name }} </option>
                                @endforeach
                            </select>
                            <!-- </div> -->
                        </div>

                        <!-- <div class="col-md-2"> -->
                            <!-- <label class='col-4 col-form-label'>Po No : </label> -->
                            <!-- <div class="col"> -->
                            <!-- <input class='form-control po_no @error("po_no") is-invalid @enderror' value='{{ old("po_no", $quotation?->po_no ?? "") }}' name="po_no" placeholder="Po No" type="text" required>
                            <x-input-error class="mt-2" :messages="$errors->get('po_no')" /> -->
                            <!-- </div> -->
                        <!-- </div> -->

                        <div class="col-md-3">
                            <label class='form-label required'>Date : </label>
                            <!-- <div class="col"> -->
                            <input class='form-control date @error("date") is-invalid @enderror' value='{{ old("date", $quotation?->date ?? date("Y-m-d")) }}' name="detail_date" placeholder="Date" type="date" required>
                            <x-input-error class="mt-2" :messages="$errors->get('date')" />
                            <!-- </div> -->
                        </div>

                        <div class="col-md-3">
                            <label class='form-label'>Company Name : </label>
                            <!-- <div class="col"> -->
                            <input class='form-control company_name @error("company_name") is-invalid @enderror' value='{{ old("company_name", $quotation?->company_name ?? "") }}' name="company_name" placeholder="Company Name" type="text">
                            <x-input-error class="mt-2" :messages="$errors->get(' company_name')" />
                            <!-- </div> -->
                        </div>

                        <div class="col-md-3">
                            <label class='form-label'>Discription : </label>
                            <!-- <div class="col"> -->
                            <input class='form-control discription @error("discription") is-invalid @enderror' value='{{ old("discription", $quotation?->discription ?? "") }}' name="discription" placeholder="Discription" type="text">
                            <x-input-error class="mt-2" :messages="$errors->get('discription')" />
                            <!-- </div> -->
                        </div>

                        <div class="col-md-3">
                            <label class='form-label required'>Delivery Date : </label>
                            <!-- <div class="col"> -->
                            <input class='form-control delivery_date @error("delivery_date") is-invalid @enderror' value='{{ old("delivery_date", $quotation?->date ?? date("Y-m-d")) }}' name="delivery_date" placeholder="Date" type="date" required>
                            <x-input-error class="mt-2" :messages="$errors->get('delivery_date')" />
                            <!-- </div> -->
                        </div>

                        <div class="col-md-4">
                            <label class='form-label required'>Address : </label>
                            <!-- <div class="col-2" style="text-align: right;">
                                        <button type="button" class="btn btn-success" id="addAddress"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                    </div> -->
                            <!-- <div class="col"> -->
                            <textarea class='form-control @error("address") is-invalid @enderror' id="address" name="address" placeholder="Address" type="text" required>{{ old("address", $quotation?->address ?? "") }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('address')" />
                            <!-- </div> -->
                        </div>
                    </div>

                    <div class="row pt-4">
                        <div class="col-md-12">
                            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                <table class="estimate-add-product table card-table table-vcenter text-nowrap text-nowrap" id="targetTable">
                                    <thead class="thead-light">
                                        <thead>
                                            <tr>
                                                <td>Item</td>
                                                <td>Quantity</td>
                                                <td>Rate</td>
                                                <td>Block</td>
                                                <td>Printing Detail</td>
                                                <td>Remarks</td>
                                                <td>Transport</td>
                                                <td>Amount</td>
                                                <td>Design</td>
                                                <td>Discount</td>
                                                <td>Action</td>
                                            </tr>
                                        </thead>
                                    <tbody class="append-here">
                                        @if (isset($quotationDetail) && count($quotationDetail) > 0)
                                        @foreach ($quotationDetail as $key => $detail)
                                        <tr>
                                            <input type="hidden" class="quotation_detail_id" name="quotation_detail_id[]" value='{{ $detail?->id ?? "" }}'>
                                            <td style="padding: 2px !important">
                                                <?php
                                                \App\Helpers\Forms::select2(

                                                    "item_id[]",

                                                    [
                                                        "configs" => [
                                                            "width" => "100%",

                                                            "ajax" => [


                                                                "type" => "POST",

                                                                "url" => route("quotation.getItem"),

                                                                "dataType" => "json",

                                                                "data" => [

                                                                    "print_type_id" =>  "[name='print_type_id']"
                                                                ]
                                                            ],

                                                            "allowClear" => false,

                                                            "placeholder" => __("Select Item"),
                                                        ],
                                                        "id" => false,
                                                        "required" => true,
                                                        "class" => "item_id"
                                                    ],
                                                    isset($detail) && !empty($detail->item_name) ? [$detail->item_id . ' - ' . $detail->print_type_id . ',' . $detail->item_name, $detail->item_name] : false,
                                                );
                                                ?>
                                            </td>
                                            <td style="padding: 2px !important">
                                                <input class='form-control qty no-spinners @error("qty") is-invalid @enderror' value='{{ old("qty", $detail?->qty ?? "") }}' name="qty[]" placeholder="Quantity" min="0" step="any" type="number" required>
                                            </td>
                                            <td style="padding: 2px !important">
                                                <input class='form-control rate no-spinners @error("rate") is-invalid @enderror' value='{{ old("rate", $detail?->rate ?? "") }}' name="rate[]" placeholder="Rate" min="0" step="any" type="number" required>
                                            </td>
                                            <td style="padding: 2px !important">
                                                <select class="form-select block @error(' block') is-invalid @enderror" required name="block[]">
                                                    @foreach (['OLD','NEW','CHANG'] as $block)
                                                    <option value="{{ $block }}" <?= $detail?->block == $block  ? 'selected' : '' ?>>
                                                        {{ $block }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td style="padding: 2px !important">
                                                <input class='form-control narration @error("narration") is-invalid @enderror' value='{{ old("narration", $detail?->narration ?? "") }}' name="narration[]" placeholder="Printing Detail" type="text">
                                            </td>
                                            <td style="padding: 2px !important">
                                                <input class='form-control remark @error("remark") is-invalid @enderror' value='{{ old("remark", $detail?->remark ?? "") }}' name="remark[]" placeholder="Remark" type="text">
                                            </td>
                                            <td style="padding: 2px !important">
                                                <select class="form-select transport_id @error(' transport_id') is-invalid @enderror" data-tags="true" name="transport_id[]">
                                                    <option value="">Select Transport</option>
                                                    @for ($i=0; $i < count($transport); $i++) <option value="{{ $transport[$i]->id }}" <?= $detail?->transport_id == $transport[$i]->id ? 'selected' : '' ?>>
                                                        {{ $transport[$i]->name }}
                                                        </option>
                                                        @endfor
                                                </select>
                                            </td>
                                            <td style="padding: 2px !important">
                                                <input class='form-control amount no-spinners @error("amount") is-invalid @enderror' value='{{ old("amount", $detail?->amount ?? "") }}' name="amount[]" placeholder="Amount" min="0" step="any" type="number" required>
                                            </td>
                                            <td style="padding: 2px !important">
                                                <h4>Image</h4>
                                            </td>
                                            <td style="padding: 2px !important">
                                                <input class='form-control discount @error("discount") is-invalid @enderror' value='{{ old("discount", $detail?->discount ?? "") }}' name="quotation_discount[]" placeholder="Discount" min="0" step="any" type="number" required>
                                            </td>
                                            <td style="padding: 2px !important">
                                                <button type="button" class="btn btn-danger remove-btn"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr class="mainRow">
                                            <input type="hidden" class="quotation_detail_id" name="quotation_detail_id[]" value=''>
                                            <td style="padding: 2px !important">
                                                <?php
                                                \App\Helpers\Forms::select2(

                                                    "item_id[]",

                                                    [
                                                        "configs" => [
                                                            "width" => "100%",

                                                            "ajax" => [

                                                                "type" => "POST",

                                                                "url" => route("quotation.getItem"),

                                                                "dataType" => "json",

                                                                "data" => [

                                                                    "print_type_id" =>  "[name='print_type_id']"
                                                                ]
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
                                                <input class='form-control qty no-spinners @error("qty") is-invalid @enderror' value='{{ old("qty", $detail?->qty ?? "") }}' name="qty[]" placeholder="Quantity" min="0" step="any" type="number" required>
                                            </td>
                                            <td style="padding: 2px !important">
                                                <input class='form-control rate no-spinners @error("rate") is-invalid @enderror' value='{{ old("rate", $detail?->rate ?? "") }}' name="rate[]" placeholder="Rate" min="0" step="any" type="number" required>
                                            </td>
                                            <td style="padding: 2px !important">
                                                <select class="form-select block @error(' block') is-invalid @enderror" name="block[]">
                                                    <option value="">Select Block</option>
                                                    @foreach (['OLD','NEW','CHANG'] as $block)
                                                    <option value="{{ $block }}">
                                                        {{ $block }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td style="padding: 2px !important">
                                                <input class='form-control narration @error("narration") is-invalid @enderror' value='{{ old("narration", $detail?->narration ?? "") }}' name="narration[]" placeholder="Printing Detail" type="text">
                                            </td>
                                            <td style="padding: 2px !important">
                                                <input class='form-control remark @error("remark") is-invalid @enderror' value='{{ old("remark", $detail?->remark ?? "") }}' name="remark[]" placeholder="Remark" type="text">
                                            </td>
                                            <td style="padding: 2px !important">
                                                <select class="form-select transport_id @error(' transport_id') is-invalid @enderror" data-tags="true" name="transport_id[]">
                                                    <option value="">Select Transport</option>
                                                    @foreach ($transport as $transport)
                                                    <option value="{{ $transport->id }}">
                                                        {{ $transport->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td style="padding: 2px !important">
                                                <input class='form-control amount no-spinners @error("amount") is-invalid @enderror' value='{{ old("amount", $detail?->amount ?? "") }}' name="amount[]" placeholder="Amount" min="0" step="any" type="number" required>
                                            </td>
                                            <td style="padding: 2px !important">
                                                <h4>Image</h4>
                                            </td>
                                            <td style="padding: 2px !important">
                                                <input class='form-control discount no-spinners @error("discount") is-invalid @enderror' value='{{ old("discount", $detail?->discount ?? "") }}' name="quotation_discount[]" placeholder="Discount" min="0" step="any" type="number" required>
                                            </td>
                                            <td style="padding: 2px !important">
                                                <button type="button" class="btn btn-danger form-control remove-btn"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-left pt-1">
                                <button type="button" class="btn btn-success addButton"><i class="fas fa-plus"></i> &nbsp;Add</button>
                            </div>
                        </div>
                        <div class="row pt-2">
                            <div class="row col-md-10">
                                <div class="row col-md-6 pb-1" style="display: none;">
                                    <label class="col-4 col-form-label required">Payment Date :</label>
                                    <div class="col">
                                        <input class="form-control" type="date" name="payment_date" value='{{ old("payment_date", $quotation?->payment_date ?? date("Y-m-d")) }}' placeholder="Enter Date" />
                                    </div>
                                </div>
                                <div class="row col-md-6 pb-1">
                                    <label class="col-4 col-form-label required">Total Amount :</label>
                                    <div class="col">
                                        <input class="form-control no-spinners" id="total" min="0" step="any" type="number" name="total_amount" value='{{ old("total_amount", $quotation?->total_amount ?? "") }}' placeholder="Enter Amount" />
                                    </div>
                                </div>

                                <!-- <div class="row col-md-6 pb-1">
                                    <label class="col-4 col-form-label">Payment Amount :</label>
                                    <div class="col">
                                        <input class="form-control no-spinners" min="0" step="any" type="number" name="payment_amount" value='{{ old("payment_amount", $quotation?->payment_amount ?? "") }}' placeholder="Enter Amount" />
                                    </div>
                                </div> -->
                                <div class="row col-md-6 pb-1">
                                    <label class="col-4 col-form-label required">Dis :</label>
                                    <div class="col">
                                        <input class="form-control no-spinners" min="0" step="any" id="discount" type="number" name="discount" value='{{ old("discount", $quotation?->discount ?? "0") }}' placeholder="Enter Discount" />
                                    </div>
                                    <div class="col">
                                        <input class="form-control no-spinners" min="0" step="any" id="discount_amount" type="number" name="discount_amount" value='{{ old("discount_amount", $quotation?->discount_amount ?? "0") }}' placeholder="Enter Discount" />
                                    </div>
                                </div>

                                <div class="row col-md-6">
                                    <label class="col-4 col-form-label">Comments :</label>
                                    <div class="col">
                                        <textarea class="form-control" type="text" name="comments" placeholder="Enter Comments"> {{ old("comments", $quotation?->comments ?? "") }} </textarea>
                                    </div>
                                </div>
                                <div class="row col-md-6" style="display: none;">
                                    <label class="col-4 col-form-label">Redeem Coin :</label>
                                    <div class="col">
                                        <input class="form-control no-spinners" id="redeem_coin" min="0" step="any" type="number" name="redeem_coin" value='{{ old("redeem_coin", $quotation?->redeem_coin ?? "") }}' placeholder="Enter Coin" />
                                    </div>
                                </div>

                                <div class="row col-md-6" style="display: none;">
                                    <label class="col-4 col-form-label"> Payment Verified : </label>
                                    <div class="col" style="padding: 10px 11px;">
                                        <input class="form-check-input" type="checkbox" name='is_verified' {{ old("is_verified", $quotation?->is_verified ?? "") == 1 ? "checked" : "" }} value='1' />
                                    </div>
                                </div>
                                <div class="row col-md-6">
                                    <label class="col-4 col-form-label required">Net Amount :</label>
                                    <div class="col">
                                        <input class="form-control no-spinners" id="net_amount" min="0" step="any" type="number" name="net_amount" value='{{ old("net_amount", $quotation?->net_amount ?? "") }}' placeholder="Enter Amount" />
                                    </div>
                                </div>
                            </div>
                            <div class="row col-md-2" style="display: none;">
                                <div class="col-md-12 pb-1">
                                    <div class="form-group">
                                        <label class="form-label">Cash Back Coin</label>
                                        <input class="form-control no-spinners" id="cash_back_coin" min="0" step="any" type="number" name="cash_back_coin" value='{{ old("cash_back_coin", $quotation?->cash_back_coin ?? "") }}' placeholder="Enter Coin" />
                                    </div>
                                </div>
                                <div class="col-md-12 pb-1">
                                    <div class="form-group">
                                        <label class="form-label">Block Find</label>
                                        <select class="form-select block @error('block_find') is-invalid @enderror" name="block_find">
                                            <option value="Yes" {{ old("block_find", $quotation?->block_find ?? "") == "Yes" ? "selected" : "" }}> Yes </option>
                                            <option value="No" {{ old("block_find", $quotation?->block_find ?? "") == "No" ? "selected" : "" }}> No </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- </div> -->
                    </div>
                    <div class="card-footer text-center">
                        <button class="btn btn-primary ms-auto" type="submit">
                            Submit <i class="fa-solid fa-spinner fa-spin ms-1 save-loader" style="display:none"></i>
                        </button>
                        <a class="btn btn-warning me-2" href="{{ route('quotation.index') }}">Back</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal modal-blur fade" id="country-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> <span class="title">Add</span> Item Category</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <div id="country-modal-body"></div>
            <div class="modal-footer">
                <button class="btn me-auto" data-bs-dismiss="modal" type="button">Close</button>
                <button class="btn btn-primary" id="submitAddress" type="submit">
                    Save <i class="fa-solid fa-spinner fa-spin ms-1 save-loader" style="display:none"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push("javascript")
<script>
    const selector = {
        customer: $('#customer'),
        city: $('#select_city_id'),
        party_type: $('#party_type'),
        address: $('#address'),
        courier: $('#courier'),
        transport: $('#transport'),

    }
    selector.customer.change(function(...args) {
        const option = $(this).find("option:selected").data()
        if (option) {
            selector.transport.val(option.other_transport_id).trigger('change');
            selector.address.val(option.address);
        }
    });
    $(document).ready(function() {
        $("#customerForm").submit(function(e) {
            e.preventDefault();
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
                        window.location = "{{ route('quotation.index') }}";
                    }, 1000);
                } else {
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
    window.edit = <?php echo isset($quotation) ? "true" : "false"; ?>;
</script>

<script>
    // Store the main row to clone
    var mainRow = $('.append-here tr').first().clone();
    $(document).ready(function() {
        $('.transport_id').select2({
            width: '100%'
        });

        $('.block').select2({
            width: '100%'
        });

        // $('#customer').select2({
        //     width: '100%'
        // });

        $('.printType').select2({
            width: '100%'
        });

        $(document).on('click', '.addButton', function() {
            // Get the last selected item_id in the table
            var lastGroupSelected = $('.append-here tr').last().find('.narration').val();

            // Clone the stored main row
            var clonedRow = mainRow.clone();

            clonedRow.find('.narration').val(lastGroupSelected);
            // Clear the input fields in the cloned row
            clonedRow.find('.qty').val('');
            clonedRow.find('.rate').val('');
            clonedRow.find('.remark').val('');
            clonedRow.find('.amount').val('');
            clonedRow.find('.discount').val('');
            clonedRow.find('.quotation_detail_id').val('');
            clonedRow.find('.transport_id').select2();
            clonedRow.find('.transport_id').val('').trigger('change');
            clonedRow.find('.item_id').val('').trigger('change');
            clonedRow.find('.block').select2();
            clonedRow.find('.block').val('OLD').trigger('change');
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
            $('.qty, .rate, .discount, #discount').trigger('keyup');
        });

        $(document).on('select2:select', '.item_id', function(e) {
            var row = $(this).parents('tr');
            var customer = $('#customer').val();
            var item_id = $(this).val();
            if (customer && item_id) {
                const http = App.http.jqClient;
                http.post(
                    "{{ route('quotation.getRate') }}", {
                        customer_id: customer,
                        item_id: item_id
                    },
                ).then(function(result) {
                    if (result) {
                        row.find('.rate').val(result.rate);
                        row.find('.discount').val(result.discount);
                    }
                })
            } else {
                $(this).val(null).trigger('change');
                sweetAlert("error", "Please select customer and item.");
            }
        });

        $(document).on('keyup', '.qty, .rate, .discount, #discount', function(e) {
            total($(this));
        });

        function total(ref) {
            var sum = 0;
            var qty = ref.closest('tr').find('.qty').val();
            var rate = ref.closest('tr').find('.rate').val();
            var discount = ref.closest('tr').find('.discount').val();
            if (!isNaN(qty) && !isNaN(rate)) {
                var total = qty * rate;
                var totalDiscount = total * (discount / 100);
                ref.closest('tr').find('.amount').val(total - totalDiscount);
            }
            $('.amount').each(function() {
                sum += +$(this).val();
            });
            $('#total').val(parseFloat(sum).toFixed(2));

            var discount = $('#discount').val();
            if (!isNaN(discount) && discount) {
                var netAmount = sum - (sum * (discount / 100));
            } else {
                var netAmount = sum;
            }

            $('#discount_amount').val(parseFloat(sum * (discount / 100)).toFixed(2));
            if (netAmount > 0) {
                $.ajax({
                    url: "{{ route('common.getRedeemCoin') }}",
                    method: "POST",
                    data: {
                        customer_id: $('#customer').val()
                    },
                    success: function(data) {
                        var redeemCoin = (netAmount * 20) / 100;
                        if (data.balance >= redeemCoin) {
                            $('#redeem_coin').val(parseFloat(redeemCoin).toFixed(2));
                        } else {
                            $('#redeem_coin').val(data.balance);
                        }
                        netAmount = netAmount - redeemCoin;
                        var cashBackCoin = (netAmount * 10) / 100;
                        $('#net_amount').val(parseFloat(netAmount).toFixed(2));
                        $('#cash_back_coin').val(parseFloat(cashBackCoin).toFixed(2));
                    }
                })
            }
        }

        $('#customer').on('change', function() {
            var customer = $(this);
            if (!window.edit || customer?.select2('data')?.length) {
                var data = (customer.select2('data')[0]);
                var discount = data.discount;
                $('#discount').val(discount);
            }
            const http = App.http.jqClient;
            http.post(
                "{{ route('quotation.getAddress') }}", {
                    customer_id: $(this).val()
                },
            ).then(function(result) {
                $('#address').val(result)
            })
        });

        // const countryModal = $("#country-modal");
        // $(document).on('click', '#addAddress', function() {
        //     var customer = $('#customer').find('option:selected').val();
        //     if (customer) {
        //         $('#address').html('');
        //         const http = App.http.jqClient;
        //         http.post(
        //             "{{ route('quotation.getAddress') }}", {
        //                 customer_id: customer
        //             },
        //         ).then(function(result) {
        //             if (result) {
        //                 $('#country-modal-body').html(result)
        //                 countryModal.modal("show");
        //             }
        //         })
        //     } else {
        //         sweetAlert("error", "Please select customer.");
        //     }
        // });

        // $(document).on('click', '#submitAddress', function() {
        //     var address = $('.addressMulti').val();
        //     if (address) {
        //         var isChecked = $('.addressMulti:checked').val();
        //         if (!isChecked) {
        //             sweetAlert("error", "Please select address.");
        //             return false;
        //         }
        //         $('#address').val(isChecked);
        //         countryModal.modal("hide");
        //     } else {
        //         sweetAlert("error", "Please select address.");
        //     }
        // });
    });
</script>
@endpush
