@extends("Layouts.app")

@section("title", isset($order) ? __("Edit Order", ["order" => $order->id]) : "Create New Order")
@php
$actionRoute = isset($order) ? route("order.update", ["order" => $order->id]) : route("order.store");
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
        max-height: 300px;
        overflow-y: auto;

    }

    .table-responsive thead {
        position: sticky;
        top: 0;
        z-index: 100;
        background-color: white;
        /* Ensures the header has a background */
    }

    .table-responsive tfoot {
        position: sticky;
        bottom: 0;
        z-index: 100;
        background-color: white;
        /* Ensures the header has a background */
    }

    [id^="select2-item_id-"] li.select2-results__option[aria-selected=true]:hover {
        background-color: #b3eba2 !important;
    }

    [id^="select2-item_id-"] .select2-results__option--highlighted {
        color: #000 !important;
        background-color: #b3eba2 !important;
    }

    label {
        white-space: nowrap;
    }
</style>
<div class="page-header d-print-none ">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                Order
            </div>
            <h2 class="page-title">
                {{ isset($order) ? "Edit Order" : "Create New Order" }}
            </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a class="btn btn-primary d-none d-sm-inline-block add-new-btn" href="{{ route('order.index') }}">
                    <i class="fa-solid fa-arrow-left me-2"></i>
                    Back
                </a>
                <a class="btn btn-primary d-sm-none btn-icon add-new-btn" href="{{ route('order.index') }}"
                    aria-label="Create new report">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section("content")
<div class="row party-color">
    <div class="col-md-12">
        <form id="customerForm" action="{{ $actionRoute }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if (isset($order))
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
                        <div class="col-md-4">
                            <label class="form-label">Party Name : <span class="text-danger">*</span>&nbsp; <span
                                    style="color: red;" id="partyDetail"></span></label>
                            <select class="form-select ajax-customer" id="customer" disabled name="customer_id" required>
                                <option value="{{ optional($order ?? null)->customer?->id }}"
                                    data-color="{{ optional($order ?? null)->customer?->partyType?->color }}"
                                    data-balance="{{optional($order ?? null)->customer?->balance}}"
                                    data-party_type_name="{{optional($order ?? null)->customer?->partyType?->name}}"
                                    data-group_name="{{optional($order ?? null)->customer?->partyGroup?->name}}"
                                    data-category_name="{{optional($order ?? null)->customer?->PartyCategory?->name}}"
                                    selected>
                                    {{ optional($order ?? null)->customer?->name . ' - (' . optional($order ?? null)->customer?->city?->name . ' - ' . optional($order ?? null)->customer?->partyType?->name . ')' }}
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label required">Print Type : </label>
                            <select class="form-select printType" name="print_type_id">
                                <option value="0">All </option>
                                @foreach ($printTypes ?? [] as $printType)
                                <option value="{{ $printType->id }}" {{ isset($order->print_type_id) && $printType->id == $order?->print_type_id ? 'selected' : '' }}> {{ $printType->name}}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class='form-label required'>Address : </label>
                            <textarea class='form-control @error("address") is-invalid @enderror' id="address"
                                name="address" placeholder="Address" type="text"
                                required>{{ old("address", $order?->address ?? "") }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('address')" />
                        </div>

                        <div class="col-md-2">
                            <label class='form-label'>Po No : </label>
                            <input class='form-control po_no @error("po_no") is-invalid @enderror'
                                value='{{ old("po_no", $order?->po_no ?? "") }}' name="po_no" placeholder="Po No"
                                type="text" disabled>
                            <x-input-error class="mt-2" :messages="$errors->get('po_no')" />
                        </div>

                        <div class="col-md-2 pt-2">
                            <label class='form-label required'>Date : </label>
                            <input class='form-control date @error("date") is-invalid @enderror'
                                value='{{ old("date", $order?->date ?? date("Y-m-d")) }}' name="detail_date"
                                placeholder="Date" type="date" required>
                            <x-input-error class="mt-2" :messages="$errors->get('date')" />
                        </div>

                        <div class="col-md-2 pt-2">
                            <label class='form-label'>Company Name : </label>
                            <input class='form-control company_name @error("company_name") is-invalid @enderror'
                                value='{{ old("company_name", $order?->company_name ?? "") }}' name="company_name"
                                placeholder="Company Name" type="text">
                            <x-input-error class="mt-2" :messages="$errors->get(' company_name')" />
                        </div>

                        <div class="col-md-4 pt-2">
                            <label class='form-label'>Discription : </label>
                            <input class='form-control discription @error("discription") is-invalid @enderror'
                                value='{{ old("discription", $order?->discription ?? "") }}' name="discription"
                                placeholder="Discription" type="text">
                            <x-input-error class="mt-2" :messages="$errors->get('discription')" />
                        </div>

                        <div class="col-md-2 pt-2">
                            <label class='form-label required'>Delivery Date : </label>
                            <input class='form-control delivery_date @error("delivery_date") is-invalid @enderror'
                                value='{{ old("delivery_date", $order?->date ?? date("Y-m-d")) }}' name="delivery_date"
                                placeholder="Date" type="date" required>
                            <x-input-error class="mt-2" :messages="$errors->get('delivery_date')" />
                        </div>

                        <div class="col-md-2 pt-2">
                            <label class="form-label">Other Print Type: </label>
                            <select class="form-select select2" id="printTypeExtra" name="print_type_other_id">
                                <option value="" {{ !isset($order->print_type_extra_id) ? 'selected' : '' }}>select
                                    other print type </option>
                                @foreach ($printTypeExtras ?? [] as $printTypeExtra)
                                <option value="{{ $printTypeExtra->id }}" {{ isset($order->print_type_extra_id) && $printTypeExtra->id == $order?->print_type_extra_id ? 'selected' : '' }}>
                                    {{ $printTypeExtra->name}}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="pt-4">
                            <div class="col-md-12">
                                <div class="table-responsive" style="max-height: 370px; overflow-y: scroll;">
                                    <table
                                        class="estimate-add-product table card-table table-vcenter text-nowrap text-nowrap"
                                        id="targetTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <td style="width: 20% !important;">Item</td>
                                                <td>Quantity</td>
                                                <td>Rate</td>
                                                <td>Block</td>
                                                <td>Printing Detail</td>
                                                <td>Item Remarks</td>
                                                <td>Other Remark</td>
                                                <td>Transport</td>
                                                <td>Amount</td>
                                                <td>Design</td>
                                                <td>Ex.Dis</td>
                                                <td style="padding: 0% !important;">Is Spec</td>
                                                <td>Action</td>
                                            </tr>
                                        </thead>
                                        <tbody class="append-here">
                                            @if (isset($orderDetail) && count($orderDetail) > 0)
                                            @foreach ($orderDetail as $key => $detail)
                                            <tr>
                                                <input type="hidden" class="order_detail_id" name="order_detail_id[]"
                                                    value='{{ $detail?->id ?? "" }}'>
                                                <input type="hidden" class="print_type_other_id"
                                                    name="print_type_other_id[]"
                                                    value='{{ $detail?->print_type_other_id ?? "" }}'>
                                                <td style="padding: 2px !important; width: 20% !important;">
                                                    <?php
                                                    \App\Helpers\Forms::select2(

                                                        "item_id[]",

                                                        [
                                                            "configs" => [
                                                                "width" => "100%",

                                                                "ajax" => [


                                                                    "type" => "POST",

                                                                    "url" => route("order.getItem"),

                                                                    "dataType" => "json",

                                                                    "data" => [

                                                                        "print_type_id" => "[name='print_type_id']"
                                                                    ]
                                                                ],

                                                                "allowClear" => false,

                                                                "placeholder" => __("Select Item"),
                                                            ],
                                                            "id" => false,
                                                            "required" => true,
                                                            "class" => "item_id",
                                                            "disabled" => true,
                                                        ],
                                                        isset($detail) && !empty($detail->item_name) ? [$detail->item_id . ' - ' . $detail->print_type_id . ',' . $detail->item_name, $detail->item_name] : false,
                                                    );
                                                    ?>
                                                </td>
                                                <td style="padding: 2px !important">
                                                    <input
                                                        class='form-control qty no-spinners @error("qty") is-invalid @enderror'
                                                        value='{{ old("qty", $detail?->qty ?? "") }}' name="qty[]"
                                                        placeholder="Quantity" min="0" step="any" type="number"
                                                        required>
                                                </td>
                                                <td style="padding: 2px !important">
                                                    <input
                                                        class='form-control rate no-spinners @error("rate") is-invalid @enderror'
                                                        value='{{ old("rate", $detail?->rate ?? "") }}' name="rate[]"
                                                        placeholder="Rate" min="0" step="any" type="number" required>
                                                </td>
                                                <td style="padding: 2px !important">
                                                    <select
                                                        class="form-select block @error('block') is-invalid @enderror"
                                                        name="block[]">
                                                        <option value="">NON</option>
                                                        @foreach (['OLD', 'NEW', 'CHANG'] as $block)
                                                        <option value="{{ $block }}" <?= $detail?->block == $block ? 'selected' : '' ?>>
                                                            {{ $block }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td style="padding: 2px !important">
                                                    <input
                                                        class='form-control narration @error("narration") is-invalid @enderror'
                                                        value='{{ old("narration", $detail?->narration ?? "") }}'
                                                        name="narration[]" placeholder="Printing Detail"
                                                        style="width:200px" type="text">
                                                </td>
                                                <td style="padding: 2px !important">
                                                    <input
                                                        class='form-control remark @error("remark") is-invalid @enderror'
                                                        value='{{ old("remark", $detail?->remark ?? "") }}'
                                                        name="remark[]" placeholder="Remark" type="text">
                                                </td>
                                                <td style="padding: 2px !important">
                                                    <input
                                                        class='form-control other_remark @error("remark") is-invalid @enderror'
                                                        value='{{ old("other_remark", $detail?->other_remark ?? "") }}'
                                                        name="other_remark[]" placeholder="Other Remark" type="text">
                                                </td>
                                                <td style="padding: 2px !important">
                                                    <select
                                                        class="form-select transport_id @error(' transport_id') is-invalid @enderror"
                                                        data-tags="true" name="transport_id[]">
                                                        <option value="">Select Transport</option>
                                                        @for ($i = 0; $i < count($transport); $i++)
                                                            <option value="{{ $transport[$i]->id }}"
                                                            <?= $detail?->transport_id == $transport[$i]->id ? 'selected' : '' ?>>
                                                            {{ $transport[$i]->name }}
                                                            </option>
                                                            @endfor
                                                    </select>
                                                </td>
                                                <td style="padding: 2px !important">
                                                    <input
                                                        class='form-control amount no-spinners @error("amount") is-invalid @enderror'
                                                        value='{{ old("amount", $detail?->amount ?? "") }}'
                                                        name="amount[]" placeholder="Amount" min="0" step="any"
                                                        type="number" required>
                                                </td>
                                                <td style="padding: 2px !important">
                                                    <h4>Image</h4>
                                                </td>
                                                <td style="padding: 2px !important">
                                                    <input
                                                        class='form-control discount @error("discount") is-invalid @enderror'
                                                        value='{{ old("discount", $detail?->discount ?? "") }}'
                                                        name="order_discount[]" placeholder="Discount" min="0"
                                                        step="any" type="number" required>
                                                </td>
                                                <td style="padding: 0px !important;text-align: center;">
                                                    <input
                                                        class="form-check-input is_special_checkbox @error('is_special') is-invalid @enderror"
                                                        {{ old("is_special", $detail?->is_special ?? "") == 1 ? "checked" : "" }} type="checkbox">
                                                    <input class="form-check-input is_special" name="is_special[]"
                                                        value="{{$detail?->is_special}}" type="hidden">
                                                </td>
                                                <td style="padding: 2px !important">
                                                </td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr class="mainRow">
                                                <input type="hidden" class="order_detail_id" name="order_detail_id[]"
                                                    value=''>
                                                <input type="hidden" class="print_type_other_id"
                                                    name="print_type_other_id[]" value=''>
                                                <td style="padding: 2px !important;width: 20% !important;">
                                                    <?php
                                                    \App\Helpers\Forms::select2(

                                                        "item_id[]",

                                                        [
                                                            "configs" => [
                                                                "width" => "100%",

                                                                "ajax" => [

                                                                    "type" => "POST",

                                                                    "url" => route("order.getItem"),

                                                                    "dataType" => "json",

                                                                    "data" => [

                                                                        "print_type_id" => "[name='print_type_id']"
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
                                                    <input
                                                        class='form-control qty no-spinners @error("qty") is-invalid @enderror'
                                                        value='{{ old("qty", $detail?->qty ?? "") }}' name="qty[]"
                                                        placeholder="Quantity" min="0" step="any" type="number"
                                                        required>
                                                </td>
                                                <td style="padding: 2px !important">
                                                    <input
                                                        class='form-control rate no-spinners @error("rate") is-invalid @enderror'
                                                        value='{{ old("rate", $detail?->rate ?? "") }}' name="rate[]"
                                                        placeholder="Rate" min="0" step="any" type="number" required>
                                                </td>
                                                <td style="padding: 2px !important">
                                                    <select
                                                        class="form-select block @error(' block') is-invalid @enderror"
                                                        name="block[]">
                                                        <option value="">NON</option>
                                                        @foreach (['OLD', 'NEW', 'CHANG'] as $block)
                                                        <option value="{{ $block }}">
                                                            {{ $block }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td style="padding: 2px !important">
                                                    <input
                                                        class='form-control narration @error("narration") is-invalid @enderror'
                                                        value='{{ old("narration", $detail?->narration ?? "") }}'
                                                        name="narration[]" placeholder="Printing Detail"
                                                        style="width:200px" type="text">
                                                </td>
                                                <td style="padding: 2px !important">
                                                    <input
                                                        class='form-control remark @error("remark") is-invalid @enderror'
                                                        value='{{ old("remark", $detail?->remark ?? "") }}'
                                                        name="remark[]" placeholder="Remark" type="text">
                                                </td>
                                                <td style="padding: 2px !important">
                                                    <input
                                                        class='form-control other_remark @error("other_remark") is-invalid @enderror'
                                                        value='{{ old("other_remark", $detail?->other_remark ?? "") }}'
                                                        name="other_remark[]" placeholder="Other Remark" type="text">
                                                </td>
                                                <td style="padding: 2px !important;width: 5% !important;">
                                                    <select
                                                        class="form-select transport_id @error(' transport_id') is-invalid @enderror"
                                                        data-tags="true" name="transport_id[]">
                                                        <option value="">Select Transport</option>
                                                        @foreach ($transport as $transport)
                                                        <option value="{{ $transport->id }}">
                                                            {{ $transport->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td style="padding: 2px !important">
                                                    <input
                                                        class='form-control amount no-spinners @error("amount") is-invalid @enderror'
                                                        value='{{ old("amount", $detail?->amount ?? "") }}'
                                                        name="amount[]" placeholder="Amount" min="0" step="any"
                                                        type="number" required>
                                                </td>
                                                <td style="padding: 2px !important">
                                                    <h4>Image</h4>
                                                </td>
                                                <td style="padding: 2px !important">
                                                    <input
                                                        class='form-control discount no-spinners @error("discount") is-invalid @enderror'
                                                        value='{{ old("discount", $detail?->discount ?? "") }}'
                                                        name="order_discount[]" placeholder="Discount" min="0"
                                                        step="any" type="number" required>
                                                </td>
                                                <td style="padding: 0px !important;text-align: center;">
                                                    <input
                                                        class="form-check-input is_special_checkbox @error('is_special') is-invalid @enderror"
                                                        type="checkbox">
                                                    <input class="form-check-input is_special" name="is_special[]"
                                                        value="0" type="hidden">
                                                </td>
                                                <td style="padding: 2px !important">
                                                </td>
                                            </tr>
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <tr style="font-weight: bolder;">
                                                <td>Total</td>
                                                <td colspan="3" id="total_qty"></td>
                                                <td>Block Find : {{ old("block_find", $order?->block_find ?? "No") }}
                                                </td>
                                                <td colspan="8"></td>

                                        </tfoot>
                                    </table>
                                </div>

                                {{-- <div class="col-md-2 pt-2">
                                        <label class='form-label'>Company Name : </label>
                                        <input class='form-control company_name @error("company_name") is-invalid @enderror'
                                            value='{{ old("company_name", $order?->company_name ?? "") }}'
                                name="company_name" placeholder="Company Name" type="text">
                                <x-input-error class="mt-2" :messages="$errors->get(' company_name')" />
                            </div> --}}

                            <div class="row col-md-12">

                                <div class="col-md-1 pb-1">
                                    <label class="form-label required" id="total_label">Total Amount
                                        :</label>

                                    <input class="form-control no-spinners" id="total" min="0" step="any"
                                        type="number" name="total_amount"
                                        value='{{ old("total_amount", $order?->total_amount ?? "") }}'
                                        placeholder="Enter Amount" readonly />
                                </div>

                                <div class="row col-md-6 pb-1 d-none">
                                    <label class="col-4 col-form-label d-none">Payment Amount :</label>
                                    <div class="col">
                                        <input class="form-control no-spinners d-none" min="0" step="any"
                                            type="number" name="payment_amount"
                                            value='{{ old("payment_amount", $order?->payment_amount ?? "") }}'
                                            placeholder="Enter Amount" />
                                    </div>
                                </div>

                                <div class="col-md-1 pb-1" style="    width: 90px;">
                                    <label class="form-label" id="discount_label">Dis(%) :</label>

                                    <input class="form-control no-spinners" min="0" step="any" id="discount"
                                        type="number" name="discount"
                                        value='{{ old("discount", $order?->discount ?? "0") }}'
                                        placeholder="Enter Discount" readonly />

                                </div>
                                <div class="col-md-1 pb-1">
                                    <label class="form-label">Dis Amount :</label>

                                    <input class="form-control no-spinners" min="0" step="any" id="discount_amount"
                                        type="number" name="discount_amount"
                                        value='{{ old("discount_amount", $order?->discount_amount ?? "0") }}'
                                        placeholder="Enter Discount" readonly />

                                </div>


                                <div class="col-md-1">
                                    <label class="form-label" id="redeem_coin_label">Redeem Coin :</label>

                                    <input class="form-control no-spinners" id="redeem_coin" min="0" step="any"
                                        type="number" name="redeem_coin"
                                        value='{{ old("redeem_coin", $order?->redeem_coin ?? "") }}'
                                        placeholder="Enter Coin" readonly />

                                </div>

                                <div class="row col-md-6 d-none">
                                    <label class="col-4 col-form-label d-none"> Payment Verified : </label>
                                    <div class="col" style="padding: 10px 11px;">
                                        <input class="form-check-input d-none" type="checkbox" name='is_verified' {{ old("is_verified", $order?->is_verified ?? "") == 1 ? "checked" : "" }}
                                            value='1' />
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label required" id="net_amount_label">Net Amount
                                        :</label>

                                    <input class="form-control no-spinners" id="net_amount" min="0" step="any"
                                        type="number" name="net_amount"
                                        value='{{ old("net_amount", $order?->net_amount ?? "") }}'
                                        placeholder="Enter Amount" readonly />

                                </div>

                                <div class="col-md-1">

                                    <label class="form-label">Cash Back Coin</label>
                                    <input class="form-control no-spinners" id="cash_back_coin" min="0" step="any"
                                        type="number" name="cash_back_coin"
                                        value='{{ old("cash_back_coin", $order?->cash_back_coin ?? "") }}'
                                        placeholder="Enter Coin" readonly />

                                </div>

                                <div class="col-md-2 pt-2" style="margin-top: 1.5rem;"></div>

                                <div class="col-md-4">
                                    @if (!empty($orderPayments) && count($orderPayments) > 0)
                                    @foreach ($orderPayments as $payment)
                                    <div class="row col-md-12 payment-row">
                                        <input type="hidden" name="payment_id[]" value="{{ $payment->id }}" />
                                        <div class="col-md-5 pb-1">
                                            <label class="form-label required">Pay. Date :</label>

                                            <input class="form-control" type="date" name="paymentDate[]"
                                                value='{{ $payment->date }}' placeholder="Enter Date" />

                                        </div>
                                        <div class="col-md-3 pb-1">
                                            <label class="form-label required">Amount :</label>

                                            <input class="form-control" name="paymentAmount[]"
                                                placeholder="Enter Amount" min="0" step="any"
                                                value='{{ $payment->amount }}' type="number">

                                        </div>

                                        <div class="col-md-2" style="margin-top: 1.9rem;">
                                            <button type="button" class="btn btn-success form-control"
                                                id="addPayment"><i class="fas fa-plus"></i>Add</button>
                                        </div>
                                        <div class="col-md-2" style="margin-top: 1.9rem;">
                                        </div>
                                    </div>
                                    @endforeach
                                    @else

                                    <div class="row col-md-12">
                                        <input type="hidden" name="payment_id[]" value="" />
                                        <div class="col-md-5 pb-1">
                                            <label class="form-label required">Pay. Date :</label>

                                            <input class="form-control" type="date" name="paymentDate[]"
                                                value='{{ date("Y-m-d") }}' placeholder="Enter Date" />

                                        </div>
                                        <div class="col-md-3 pb-1">
                                            <label class="form-label required">Amount :</label>

                                            <input class="form-control" name="paymentAmount[]"
                                                placeholder="Enter Amount" min="0" step="any" value=''
                                                type="number">

                                        </div>
                                    </div>
                                    @endif
                                    <div id="additionalPayments"></div>

                                </div>

                                <div class="col-md-6 d-none">
                                    <label class="col-4 col-form-label  d-none">Comments :</label>
                                    <div class="col">
                                        <textarea class="form-control  d-none" type="text" name="comments"
                                            placeholder="Enter Comments"> {{ old("comments", $order?->comments ?? "") }} </textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-footer text-center d-none">

                    <div class="row pt-2">


                        <div class="row col-md-2 d-none">

                            <div class="form-group">
                                <label class="form-label">Block Find</label>
                                <select class="form-select block @error('block_find') is-invalid @enderror"
                                    name="block_find">
                                    <option value="Yes" {{ old("block_find", $order?->block_find ?? "") ==
        "Yes" ? "selected" : "" }}> Yes </option>
                                    <option value="No" {{ old("block_find", $order?->block_find ?? "") ==
        "No" ? "selected" : "" }} {{ !isset($order->block_find) ? "selected"
        : "" }}> No
                                    </option>
                                </select>
                            </div>

                        </div>
                    </div>

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
    window.edit = <?php echo isset($order) ? "true" : "false"; ?>;
</script>
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
            $('#submitData').prop('disabled', true);
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
                        window.location = "{{ route('order.index') }}";
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

        $(document).on('blur', '.qty', function() {

            var checkqty = $(this).val();
            if (checkqty < 1) {
                $(this).focus();
            }

        });

        $(document).on('keyup', '.qty', function() {



            const sum = $('.qty').get().reduce((total, qty) => total + Number($(qty).val()), 0);
            $('#total_qty').text(sum);
        });
        if (window.edit) {
            const sum = $('.qty').get().reduce((total, qty) => total + Number($(qty).val()), 0);
            $('#total_qty').text(sum);
        }
    });

    $('#addPayment').click(function() {
        // Define the HTML structure for new payment row
        var newPaymentRow = `
                                                                                                                                                                                                                                                    <div class="row col-md-12 payment-row">
                                                                                                                                                                                                                                                        <input type="hidden" name="payment_id[]" value="" />
                                                                                                                                                                                                                                                        <div class="col-md-5 pb-1">
                                                                                                                                                                                                                                                            <label class="form-label required">Date :</label>

                                                                                                                                                                                                                                                                <input class="form-control" type="date" name="paymentDate[]" value='{{ date("Y-m-d") }}' placeholder="Enter Date" />

                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                        <div class="col-md-5">
                                                                                                                                                                                                                                                            <label class="form-label required">Amount :</label>

                                                                                                                                                                                                                                                                <input class="form-control" name="paymentAmount[]" placeholder="Enter Amount" min="0" step="any" value='' type="number">

                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                        <div class="col-md-2" style="margin-top: 1.9rem;">
                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                    `;

        // Append the new payment row to the container
        $('#additionalPayments').append(newPaymentRow);
    });

    // Event delegation to handle dynamically added remove buttons
    $(document).on('click', '.removePayment', function() {
        $(this).closest('.row').remove(); // Remove the entire row
    });

    $(document).on('click', '.removePayment', function() {
        // Remove the row when the 'Remove' button is clicked
        $(this).closest('.payment-row').remove();
    });
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

        $('.printType').select2({
            width: '100%'
        });

        $(document).on('click', "#total_label", function() {
            $("#total").attr("readonly", false);
        });

        $(document).on('click', "#discount_label", function() {
            $("#discount").attr("readonly", false);
            // $("#discount_amount").attr("readonly", false);
        });

        $(document).on('click', "#redeem_coin_label", function() {
            $("#redeem_coin").attr("readonly", false);
            $("#cash_back_coin").attr("readonly", false);
        });

        $(document).on('click', "#net_amount_label", function() {
            $("#net_amount").attr("readonly", false);
        });

        $(document).on('click', '.addButton', function() {
            // Get the last selected item_id in the table
            var lastGroupSelected = $('.append-here tr').last().find('.narration').val();
            var lastBlockSelected = $('.append-here tr').last().find('.block').val();
            var lastTransportSelected = $('.append-here tr').last().find('.transport_id').val();
            var lastQuantity = $('.append-here tr').last().find('.qty').val();
            var itemID = $('.append-here tr').last().find('.item_id').val();

            if (lastQuantity > 0 && itemID != null) {
                // Clone the stored main row
                var clonedRow = mainRow.clone();

                clonedRow.find('.narration').val(lastGroupSelected);
                // Clear the input fields in the cloned row
                clonedRow.find('.qty').val('');
                clonedRow.find('.rate').val('');
                clonedRow.find('.remark').val('');
                clonedRow.find('.amount').val('');
                clonedRow.find('.discount').val('');
                clonedRow.find('.order_detail_id').val('');
                clonedRow.find('.is_special').val(0);
                clonedRow.find('.is_special_checkbox').prop('checked', false);
                clonedRow.find('.transport_id').select2();
                clonedRow.find('.transport_id').val(lastTransportSelected).trigger('change');
                clonedRow.find('.item_id').val('').trigger('change');
                clonedRow.find('.block').select2();
                clonedRow.find('.block').val(lastBlockSelected).trigger('change');
                // Append the cloned row to the table
                $(".append-here").append(clonedRow);
                $(".append-here tr").last().find('.dungdt-select2-field').trigger('re-select2');
                clonedRow.find('.item_id').select2('open');

                scrollToBottom();
                var printTypeExtra = $('#printTypeExtra').val();
                clonedRow.find('.print_type_other_id').val(printTypeExtra);
            } else {
                sweetAlert("error", 'Item And Qutity Field Required.');
            }
        });

        function scrollToBottom() {
            var $tableContainer = $('.table-responsive');
            $tableContainer.animate({
                scrollTop: $tableContainer.prop("scrollHeight")
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

        $(document).on('select2:open', '.block', function() {
            var refff = $(this);
            $('.select2-search__field').on('keydown', function(e) {

                // Check if Enter key is pressed
                if (e.key === 'Enter') {
                    e.preventDefault(); // Prevent the default behavior of changing selection
                    var row = refff.closest('tr');
                    row.find('.narration').focus();
                }
            });
        });

        $(document).on('select2:open', '.transport_id', function() {
            var refff = $(this);
            $('.select2-search__field').on('keydown', function(e) {

                // Check if Enter key is pressed
                if (e.key === 'Enter') {
                    e.preventDefault(); // Prevent the default behavior of changing selection
                    var row = refff.closest('tr');
                    row.find('.amount').focus();
                }
            });
        });

        $(document).on('select2:select', '.item_id', function(e) {
            var row = $(this).parents('tr');
            var customer = $('#customer').val();
            var item_id = $(this).val();
            scrollToBottom();
            var printTypeExtra = $('#printTypeExtra').val();
            if (customer && item_id) {
                const http = App.http.jqClient;
                http.post(
                    "{{ route('order.getRate') }}", {
                        customer_id: customer,
                        item_id: item_id,
                        printTypeExtra: printTypeExtra
                    },
                ).then(function(result) {
                    if (result) {
                        row.find('.rate').val(result.rate);
                        row.find('.discount').val(Math.round(result.discount));
                    }
                });
            } else {
                $(this).val(null).trigger('change');
                sweetAlert("error", "Please select customer and item.");
            }
        });

        $(document).on('keyup', '.qty, .rate, .discount, #discount', function(e) {
            total($(this));
        });

        $(document).on('keyup', '#redeem_coin', function(e) {
            // var redeem = $(this).val();
            // var total = $('#total').val();
            // var totalDiscount = $('#discount_amount').val();
            // var net = Number(total) - Number(totalDiscount);

            // if (!isNaN(redeem) && redeem) {
            //     $('#net_amount').val(Math.round(Number(net) - Number(redeem)));
            // }

            comamanfinalamount();
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
            $('#total').val(Math.round(sum));

            comamanfinalamount();

        }

        function comamanfinalamount() {
            var sum = $('#total').val();
            var discount = $('#discount').val();
            if (!isNaN(discount) && discount) {
                var netAmount = sum - (sum * (discount / 100));
            } else {
                var netAmount = sum;
            }

            $('#discount_amount').val(Math.round(sum * (discount / 100)));
            if (netAmount > 0) {
                var data = ($('#customer').select2('data')[0]);
                if (data.balance == undefined) {
                    data.balance = $('#customer').find(":selected").data('balance');
                }
                if (data.party_type_id == undefined) {
                    data.party_type_id = $('#customer').find(":selected").data('party_type_name');
                }

                var redeemCoin = (netAmount * 20) / 100;
                if (data.balance >= redeemCoin) {
                    $('#redeem_coin').val(Math.round(redeemCoin));
                    netAmount = netAmount - redeemCoin;
                } else {
                    $('#redeem_coin').val(Math.round(data.balance));
                    netAmount = netAmount - data.balance;
                }
                var cashBackCoin = (netAmount * 10) / 100;
                $('#net_amount').val(Math.round(netAmount));
                if (data.party_type_name == 'Retailer') {
                    $('#cash_back_coin').val(Math.round(cashBackCoin));
                } else {
                    $('#cash_back_coin').val('0');
                }
            }

        }

        $('#customer').on('change', function() {
            var customer = $(this);
            if (!window.edit || customer?.select2('data')?.length) {
                var data = (customer.select2('data')[0]);
                var discount = Math.round(data.discount);
                $('#discount').val(discount);
            }
            const http = App.http.jqClient;
            http.post(
                "{{ route('order.getAddress') }}", {
                    customer_id: $(this).val()
                },
            ).then(function(result) {
                $('#address').val(result)
            })
            $('.party-color').css('background', data.color)
            $('#partyDetail').text('(' + data.group_name + ' - ' + data.category_name + ')');
        });

        if (window.edit) {
            var data = ($('#customer').select2('data')[0]);
            if (data.color == undefined) {
                data.color = $('#customer').find(":selected").data('color');
                data.group_name = $('#customer').find(":selected").data('group_name');
                data.category_name = $('#customer').find(":selected").data('category_name');
            }
            var customer_id = $('#customer').val();
            $('.party-color').css('background', data.color)
            $('#partyDetail').text('(' + data.group_name + ' - ' + data.category_name + ')');
        }

        $('#printTypeExtra').on('select2:select', function() {
            var printTypeExtra = $(this).val();
            $('.print_type_other_id').each(function() {
                var rate = $(this).parents('tr').find('.rate').val();
                if (!rate) {
                    $(this).val(printTypeExtra);
                }
            })
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
