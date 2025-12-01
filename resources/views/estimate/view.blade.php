@extends("Layouts.app")

@section("title", isset($estimate) ? __("Edit Estimate", ["estimate" => $estimate->id]) : "Create New estimate")
@php
$actionRoute = isset($estimate) ? route("estimate.update", ["estimate" => $estimate->id]) : route("estimate.store");
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

    label {
        white-space: nowrap;
    }
</style>
<div class="page-header d-print-none">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                Estimate
            </div>
            <h2 class="page-title">
                {{ isset($estimate) ? "Edit Estimate" : "Create New Estimate" }}
            </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                @if (isset($estimate))


                <form id="estimateForm" action="{{ route('print.getEstimatePdf') }}" method="post" target="_blank">
                    @csrf
                    <input type="hidden" name="type" id="type" value="">
                    <input type="hidden" name="id[]" id="id" value="{{ $estimate?->id }}">
                    <button type="button" data-type="estimate-excel" class="print-button btn btn-outline-primary ml-2">Bill
                        Excel</button>
                    <button type="button" data-type="estimate" data-id="{{ $estimate?->id }}"
                        class="print-button btn btn-outline-primary">Estimate</button>
                    <button type="button" data-type="cover-print" data-id="{{ $estimate?->id }}"
                        class="print-button btn btn-outline-primary">Cover Print</button>
                </form>
                @endif
                <a class="btn btn-primary d-none d-sm-inline-block add-new-btn" href="{{ route('estimate.index') }}">
                    <i class="fa-solid fa-arrow-left me-2"></i>
                    Back
                </a>
                <a class="btn btn-primary d-sm-none btn-icon add-new-btn" href="{{ route('estimate.index') }}"
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
            @if (isset($estimate))
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
                        <div class="col-md-12 mb-3">
                            <div class="row">
                                <div class="col-md-4 pb-1">
                                    <label class="col-form-label">Party Name : <span class="text-danger">*</span>&nbsp;
                                        <span style="color: red;" id="partyDetail"></span></label>
                                    <select class="form-select ajax-customer @error('customer_id') is-invalid @enderror"
                                        id="customer" name="customer_id" disabled>
                                        <option value="{{ optional($estimate ?? null)->customer?->id }}"
                                            data-color="{{ optional($estimate ?? null)->customer?->partyType?->color }}"
                                            data-balance="{{optional($estimate ?? null)->customer?->balance}}"
                                            data-party_type_id="{{optional($estimate ?? null)->customer?->partyType?->id}}"
                                            data-group_name="{{optional($estimate ?? null)->customer?->partyGroup?->name}}"
                                            data-category_name="{{optional($estimate ?? null)->customer?->partyCategory?->name}}"
                                            selected>
                                            {{ optional($estimate ?? null)->customer?->name }}-({{optional($estimate ?? null)->customer?->city?->name}}
                                            - {{optional($estimate ?? null)->customer?->partyType?->name}})
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-1 pb-1">
                                    <label class='col-form-label'>Party Cat:</label>
                                    <select class="form-control @error('bill_type') is-invalid @enderror select2"
                                        name="bill_type" id="bill_type">
                                        <option value="">Select Category</option>
                                        @foreach ($partyCategorys as $partyCategory)
                                        <option value="{{ $partyCategory->id }}" <?= isset($customer) && $customer?->bill_type == $partyCategory->id ? 'selected' : '' ?>>
                                            {{ $partyCategory->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-5 pb-1">
                                    <label class='col-form-label required'>Address : </label>
                                    <textarea style="height: 50px; width: 100%;"
                                        class='form-control @error("address") is-invalid @enderror' id="address"
                                        name="address" placeholder="Address" type="text"
                                        required>{{ old("address", $estimate?->address ?? "") }}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                                </div>



                                <div class="col-md-1 pb-2 d-none">
                                    <label class="col-form-label">PO No : </label>
                                    <input class='form-control po_no @error("po_no") is-invalid @enderror'
                                        value='{{ old("po_no", $estimate?->po_no ?? "") }}' name="po_no"
                                        placeholder="PO No" type="text">
                                    <x-input-error class="mt-2" :messages="$errors->get('po_no')" />
                                </div>



                                <div class="col-md-2">
                                    <label class='col-form-label required'>Date : </label>
                                    <input class='form-control date @error("date") is-invalid @enderror'
                                        value='{{ old("date", $estimate?->date ?? date("Y-m-d")) }}' name="detail_date"
                                        placeholder="Date" type="date" required>
                                    <x-input-error class="mt-2" :messages="$errors->get('date')" />
                                </div>

                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="accordion pt-2" id="accordion-example">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="heading-1" style="font-size: 13px;">
                                                    <div class="row m-0 p-0">
                                                        <div class="col-md-1">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#collapse-1"
                                                                aria-expanded="false">
                                                            </button>
                                                        </div>
                                                        <div class="col-md-3 pr-1 pt-1">
                                                            <select class="form-select select2" id="orderCode">
                                                                <option value="">Select Order Code</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </h2>



                                                <div id="collapse-1" class="accordion-collapse collapse"
                                                    data-bs-parent="#accordion-example">
                                                    <div class="accordion-body pt-0"
                                                        style="height: 250px; overflow-y: auto;">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="table-responsive">
                                                                    <table
                                                                        class="table card-table table-vcenter text-nowrap order-summary">
                                                                        <thead>
                                                                            <tr>
                                                                                <td>O No.</td>
                                                                                <td><input type='checkbox'
                                                                                        class='checkBoxAll'></td>
                                                                                <td>Order Date</td>
                                                                                <td>Item</td>
                                                                                <td style="padding: 0px !important;">OPT
                                                                                </td>


                                                                                <td>PQ</td>
                                                                                <td>Rate</td>
                                                                                <td>Prin Detail</td>

                                                                                <td>Remarks</td>
                                                                                <td>Other Remarks</td>
                                                                                <td>Transport</td>
                                                                                <td>Amount</td>
                                                                                <td>Design</td>
                                                                                <td>Discount</td>
                                                                                <td>Block</td>
                                                                                <td>QTY</td>
                                                                                <td>DQ</td>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="append-data"></tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="row col-md-12">
                                            <div class="col-md-9">
                                                <label class="form-label required">Transport : </label>
                                                <select
                                                    class="form-control select2 @error('transport_id') is-invalid @enderror"
                                                    id="transport_id" name="transport_id">
                                                    <option value="">Select </option>
                                                    @for ($i = 0; $i < count($transport); $i++)
                                                        <option value="{{ $transport[$i]->id }}"
                                                        <?= isset($estimate->transport_id) && $estimate?->transport_id == $transport[$i]->id ? 'selected' : '' ?>>
                                                        {{ $transport[$i]->name }} -
                                                        {{ $transport[$i]->is_waybill ? 'YES' : 'NO' }}
                                                        </option>
                                                        @endfor
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Parcel : </label>
                                                <input
                                                    class='form-control no-spinners parcel @error("parcel") is-invalid @enderror'
                                                    value='{{ old("parcel", $estimate?->parcel ?? "") }}' name="parcel"
                                                    placeholder="Parcel" type="number">
                                                <x-input-error class="mt-2" :messages="$errors->get('parcel')" />
                                            </div>

                                            <div class="col-md-6">
                                                <label class='form-label'>LR No : </label>
                                                <input
                                                    class='form-control no-spinners @error("lr_no") is-invalid @enderror'
                                                    value='{{ old("lr_no", $estimate?->lr_no ?? "") }}' name="lr_no"
                                                    placeholder="LR No" type="number">
                                                <x-input-error class="mt-2" :messages="$errors->get('lr_no')" />

                                            </div>



                                            <div class="col-md-6">
                                                <label class='form-label'>LR Date : </label>
                                                <div class="col">
                                                    <input class='form-control @error("lr_date") is-invalid @enderror'
                                                        value='{{ old("lr_date", $estimate?->lr_date ?? "") }}'
                                                        name="lr_date" placeholder="Date" type="date">
                                                    <x-input-error class="mt-3" :messages="$errors->get('lr_date')" />

                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">LR Image : </label>

                                                <input class="form-control" type="file" name="image" id="image" />
                                                <x-input-error class="mt-2" :messages="$errors->get('image')" />
                                            </div>

                                            <div class="col-md-3 pt-1 d-none">
                                                <label class='form-label'>Company Name: </label>
                                                <input
                                                    class='form-control company_name @error("company_name") is-invalid @enderror'
                                                    value='{{ old("company_name", $estimate?->company_name ?? "") }}'
                                                    name="company_name" placeholder="Company Name" type="text">
                                                <x-input-error class="mt-2" :messages="$errors->get(' company_name')" />
                                            </div>

                                            <div class="col-md-6 pt-1">
                                                <label class='form-label'>Invoice : </label>
                                                <select class="form-select select2" required id="invoice"
                                                    name="invoice_id">
                                                    @foreach ($invoices ?? [] as $invoice)
                                                    <option value="{{ $invoice->id }}" {{ isset($estimate->invoice_id) && $invoice->id == $estimate?->invoice_id ? 'selected' : '' }}>
                                                        {{ $invoice->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-6 pt-1">
                                                <label class='form-label'>Courier : </label>
                                                <select class="form-select select2" id="courier_id" name="courier_id">
                                                    <option value="">Select Courier</option>
                                                    @foreach ($couriers ?? [] as $couriers)
                                                    <option value="{{ $couriers->id }}" {{ isset($estimate->courier_id) && $couriers->id == $estimate?->courier_id ? 'selected' : '' }}>
                                                        {{ $couriers->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-6 pt-1">
                                                <label class='form-label'>Docket : </label>
                                                <input
                                                    class='form-control no-spinners @error("docket") is-invalid @enderror'
                                                    value='{{ old("docket", $estimate?->docket ?? "") }}' name="docket"
                                                    placeholder="Docket" type="number">
                                                <x-input-error class="mt-2" :messages="$errors->get('docket')" />
                                            </div>

                                            <div class="col-md-12 pt-1">
                                                <label class='form-label'>Note : </label>
                                                <!-- <div class="col"> -->
                                                <input class='form-control note @error("note") is-invalid @enderror'
                                                    value='{{ old("note", $estimate?->note ?? "") }}' name="note"
                                                    placeholder="Note" type="text">
                                                <x-input-error class="mt-2" :messages="$errors->get('note')" />
                                                <!-- </div> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="rowToCloneContainer">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                            <table class="table card-table table-vcenter text-nowrap targetTable">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <td style="width: 20% !important;">Item</td>
                                                        <td style="padding: 0% !important;margin: 0% !important;">OPT
                                                        </td>
                                                        <td>Quantity</td>
                                                        <td>Rate</td>
                                                        <td>Print Detail</td>
                                                        <td>Item Remarks</td>
                                                        <td>Other Remarks</td>
                                                        <td>Amount</td>
                                                        <td>Order Date</td>
                                                        <td>Disco</td>
                                                        <td style="padding: 0% !important;">Is Spec</td>
                                                        <td>Action</td>
                                                        <td style="padding: 0% !important;margin: 0% !important;">Parcel
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody class="append-here">
                                                    @if (isset($estimateDetail) && count($estimateDetail) > 0)
                                                    @foreach ($estimateDetail as $detail)
                                                    <tr class="mainRow">
                                                        <input type="hidden" class="order-id" name="order_id[]"
                                                            value="{{ old('order_id', $detail?->order_id ?? '') }}" />
                                                        <input type="hidden" name="estimate_id[]"
                                                            value="{{ $detail?->id ?? '' }}" />
                                                        <input type="hidden" class="printOtherId"
                                                            name="print_type_other_id[]"
                                                            value="{{ $detail?->print_type_other_id ?? '' }}" />
                                                        <td style="padding: 2px !important;width: 20% !important;">
                                                            <select class="form-select item_id" required disabled name="item_id[]">
                                                                <option value="">Select</option>
                                                                @foreach ($items ?? [] as $item)
                                                                <option value="{{ $item[1] }}" <?= $detail?->item_name == $item[0] ? 'selected' : '' ?>>
                                                                    {{ $item[0] }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td style="padding: 0px !important;width: 0% !important;text-align: center" class="printTypeOther">
                                                            </th>
                                                        <td style="padding: 2px !important;width: 6% !important;">
                                                            <input
                                                                class='form-control no-spinners qty @error("qty") is-invalid @enderror'
                                                                value='{{ old("qty", $detail?->qty ?? "") }}'
                                                                name="qty[]" placeholder="Quantity" min="0" step="any"
                                                                type="number" required>
                                                        </td>
                                                        <td style="padding: 2px !important;width: 6% !important;">
                                                            @can('estimate-other')
                                                            <input
                                                                class='form-control rate no-spinners @error("rate") is-invalid @enderror'
                                                                value='{{ old("rate", $detail?->rate ?? "") }}'
                                                                name="rate[]" placeholder="Rate" min="0" step="any"
                                                                type="number" readonly required>
                                                            @else
                                                            <input
                                                                class='form-control rate no-spinners @error("rate") is-invalid @enderror'
                                                                value='{{ old("rate", $detail?->rate ?? "") }}'
                                                                name="rate[]" placeholder="Rate" min="0" step="any"
                                                                type="number" required>
                                                            @endcan
                                                        </td>
                                                        <td style="padding: 2px !important">
                                                            <input
                                                                class='form-control narration @error("narration") is-invalid @enderror'
                                                                value='{{ old("narration", $detail?->narration ?? "") }}'
                                                                name="narration[]" placeholder="Printing Detail"
                                                                type="text">
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
                                                                name="other_remark[]" placeholder="Other Remark"
                                                                type="text">
                                                        </td>
                                                        <td style="padding: 2px !important;width: 7% !important;">
                                                            <input
                                                                class='form-control amount no-spinners @error("amount") is-invalid @enderror'
                                                                readonly
                                                                value='{{ old("amount", $detail?->amount ?? "") }}'
                                                                name="amount[]" placeholder="Amount" min="0" step="any"
                                                                type="number" required>
                                                        </td>
                                                        <td style="padding: 2px !important">
                                                            <input
                                                                class='form-control date @error("date") is-invalid @enderror'
                                                                value='{{ old("date", $detail?->date ?? date("Y-m-d")) }}'
                                                                name="date[]" placeholder="Date" type="date">
                                                        </td>
                                                        <td style="padding: 2px !important;width: 5% !important;">
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
                                                            <input class="form-check-input is_special"
                                                                name="is_special[]" value="{{$detail?->is_special}}"
                                                                type="hidden">
                                                        </td>
                                                        <td style="padding: 2px !important;width: 5% !important;">

                                                        </td>
                                                        <td style="padding: 2px !important;width: 5% !important;">
                                                            <input
                                                                class='form-control item_parcel @error("item_parcel") is-invalid @enderror'
                                                                value='{{ old("item_parcel", $detail?->parcel ?? "") }}'
                                                                name="item_parcel[]" placeholder="" min="0" step="any"
                                                                type="number">
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    @else

                                                    @endif
                                                </tbody>
                                                <tfoot>
                                                    <tr style="font-weight: bolder;">
                                                        <td colspan="2" class="text-right">Total</td>
                                                        <td class="text-center" id="total_qty"></td>
                                                        <td colspan="10"></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <!-- <div class="text-left pt-1">
										<button type="button" class="btn btn-success addButton"><i class="fas fa-plus"></i> &nbsp;Add</button>
									</div> -->
                                    </div>
                                </div>
                                <div class="row pt-5">
                                    <div class="row">
                                        <div class="col-md-1 pb-1">
                                            <label class="form-label required" id="total_label">Total Amount
                                                :</label>
                                            <input class="form-control no-spinners" id="total" type="number" min="0"
                                                step="any" name="total_amount"
                                                value='{{ old("total_amount", $estimate?->total_amount ?? "") }}'
                                                placeholder="Enter Amount" readonly />
                                        </div>
                                        <div class="row col-md-2 pb-1">
                                            <div class="col-4">
                                                <label class="form-label required" id="discount_label">Dis(%):
                                                </label>
                                                <input class="form-control no-spinners" id="discount" type="number"
                                                    min="0" step="any" name="discount"
                                                    value='{{ old("discount", $estimate?->discount ?? "") }}'
                                                    placeholder="Enter Discount" readonly />
                                            </div>
                                            <div class="col-8">
                                                <label class="form-label required" id="discount_label">Dis Amount:
                                                </label>
                                                <input class="form-control no-spinners" min="0" step="any"
                                                    id="discount_amount" type="number" name="discount_amount"
                                                    value='{{ old("discount_amount", $estimate?->discount_amount ?? "0") }}'
                                                    placeholder="Enter Discount" readonly />
                                            </div>
                                        </div>
                                        <div class="col-md-1 pb-1">
                                            <label class="form-label">Other Charge :</label>
                                            <input class="form-control no-spinners" id="other_charge" type="number"
                                                min="0" step="any" name="other_charge"
                                                value='{{ old("other_charge", $estimate?->other_charge ?? "") }}'
                                                placeholder="Enter Other Charge" />
                                        </div>
                                        <div class="col-md-1">
                                            <label class="form-label" id="redeem_coin_label">Redeem Coin
                                                :</label>
                                            <input class="form-control no-spinners" id="redeem_coin" type="number"
                                                min="0" step="any" name="redeem_coin"
                                                value='{{ old("redeem_coin", $estimate?->redeem_coin ?? "") }}'
                                                placeholder="Enter Coin" readonly />
                                        </div>
                                        <div class="col-md-1">
                                            <label class="form-label required" id="net_amount_label">Net
                                                Amount :</label>
                                            <input class="form-control no-spinners" id="net_amount" type="number"
                                                min="0" step="any" name="net_amount"
                                                value='{{ old("net_amount", $estimate?->net_amount ?? "") }}'
                                                placeholder="Enter Amount" readonly />
                                        </div>

                                        <div class="row col-md-2">
                                            <div class="col-md-6 pb-1">
                                                <div class="form-group">
                                                    <label class="form-label">Back Coin :</label>
                                                    <input class="form-control no-spinners" id="cash_back_coin"
                                                        type="number" min="0" step="any" name="cash_back_coin"
                                                        value='{{ old("cash_back_coin", $estimate?->cash_back_coin ?? "") }}'
                                                        placeholder="Enter Coin" readonly />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Offer Dis : </label>
                                                    <input class="form-control no-spinners" type="number" min="0" step="any"
                                                        name="offer_discount"
                                                        value='{{ old("offer_discount", $estimate?->offer_discount ?? "") }}'
                                                        placeholder="Enter Offer Dis" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 pt-5">

                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Comments :</label>
                                            <textarea class="form-control" type="text" name="comments"
                                                placeholder="Enter Comments"> {{ old("comments", $estimate?->comments ?? "") }} </textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </form>
</div>
</div>

<div id="rowToCloneContainer" style="display:none;">
    <table>
        <tbody id="rowToClone">
            <tr>
                <input type="hidden" class="printOtherId" name="print_type_other_id[]" value="" />
                <input type="hidden" class="order-id" name="order_id[]" value="" />
                <input type="hidden" name="estimate_id[]" value="" />
                <td style="padding: 2px !important;width: 20% !important;">
                    <select class="form-select item_id" disabled name="item_id[]">
                        <option value="">Select </option>
                        @foreach ($items as $item)
                        <option value="{{ $item[1] }}"> {{ $item[0] }} </option>
                        @endforeach
                    </select>
                </td>
                <td style="padding: 0px !important;width: 0% !important;text-align: center" class="printTypeOther">
                    </th>
                <td style="padding: 2px !important;width: 5% !important;">
                    <input class='form-control qty no-spinners @error("qty") is-invalid @enderror' value='' name="qty[]"
                        placeholder="Quantity" type="number" min="0" step="any" required>
                </td>
                <td style="padding: 2px !important;width: 5% !important;">
                    @can('estimate-other')
                    <input class='form-control rate no-spinners @error("rate") is-invalid @enderror' value=''
                        name="rate[]" placeholder="Rate" type="number" min="0" step="any" required>
                    @else
                    <input class='form-control rate no-spinners @error("rate") is-invalid @enderror' value=''
                        name="rate[]" placeholder="Rate" type="number" min="0" step="any" readonly required>
                    @endcan
                </td>
                <td style="padding: 2px !important">
                    <input class='form-control narration @error("narration") is-invalid @enderror' value=''
                        name="narration[]" placeholder="Printing Detail" type="text">
                </td>
                <td style="padding: 2px !important">
                    <input class='form-control remark @error("remark") is-invalid @enderror' value='' name="remark[]"
                        placeholder="Remark" type="text">
                </td>
                <td style="padding: 2px !important">
                    <input class='form-control other_remark @error("other_remark") is-invalid @enderror' value=''
                        name="other_remark[]" placeholder="Other Remark" type="text">
                </td>
                <td style="padding: 2px !important;width: 7% !important;">
                    <input class='form-control amount no-spinners @error("amount") is-invalid @enderror' readonly
                        value='' name="amount[]" placeholder="Amount" type="text" equired>
                </td>
                <td style="padding: 2px !important">
                    <input class='form-control date @error("date") is-invalid @enderror' value='' name="date[]"
                        placeholder="Date" type="date">
                </td>
                <td style="padding: 2px !important;width: 5% !important;">
                    <input class='form-control discount no-spinners @error("discount") is-invalid @enderror' value=''
                        name="order_discount[]" placeholder="Discount" min="0" step="any" type="number" required>
                </td>
                <td style="padding: 0px !important;text-align: center;">
                    <input class="form-check-input is_special_checkbox @error('is_special') is-invalid @enderror"
                        type="checkbox">
                    <input class="form-check-input is_special" name="is_special[]" value="0" type="hidden">
                </td>
                <td style="padding: 2px !important;width: 5% !important;">
                </td>
                <td style="padding: 2px !important;width: 5% !important;">
                    <input class='form-control item_parcel @error("item_parcel") is-invalid @enderror'
                        value='{{ old("item_parcel", $detail?->parcel ?? "") }}' name="item_parcel[]"
                        placeholder="item parcel" min="0" step="any" type="number">
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection

@push("javascript")

<script>
    $(document).on('click', '.print-button', function() {
        var form = $(this).closest('form');
        form.find('#type').val($(this).data('type'));
        form.submit();
    });
</script>

<script>
    $(document).ready(function() {
        // $("#customerForm").submit(function(e) {
        // e.preventDefault();
        // const F = $(this)
        // removeErrors();
        // F.find(".save-loader").show();
        // const http = App.http.jqClient;
        // http[window.edit ? 'put' : 'post'](
        // F.attr("action"),
        // F.serialize()
        // ).then(res => {
        // if (res.success) {
        // sweetAlert("success", res.message);
        // setTimeout(() => {
        // window.location = "{{ route('estimate.index') }}";
        // }, 1000);
        // } else {
        // sweetAlert("error", res.message);
        // }
        // }).always(() => {
        // F.find(".save-loader").hide()
        // }).catch(function() {
        // $('html, body').animate({
        // scrollTop: 0
        // }, 'slow');
        // });
        // });
        $("#customerForm").submit(function(e) {
            $('#submitData').prop('disabled', true);
            e.preventDefault();
            const F = $(this);
            removeErrors();
            F.find(".save-loader").show();

            // Create a new FormData object
            var formData = new FormData(this);

            // Determine the request type and URL
            var actionUrl = F.attr("action");
            var requestType = window.edit ? 'POST' : 'POST'; // Use POST for both, handle PUT in server-side

            // If it's a PUT request, append _method=PUT to the form data
            if (window.edit) {
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: actionUrl,
                type: requestType,
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    if (res.success) {
                        sweetAlert("success", res.message);
                        setTimeout(() => {
                            window.location = "{{ route('estimate.index') }}";
                        }, 1000);
                    } else {
                        sweetAlert("error", res.message);
                    }
                },
                error: function(xhr, status, error) {
                    sweetAlert("error", "An error occurred while processing the request.");
                },
                complete: function() {
                    F.find(".save-loader").hide();
                }
            });
        });
    });
</script>

<script>
    window.edit = <?php echo isset($estimate) ? "true" : "false"; ?>;
</script>

<script>
    var clonedRow = $('#rowToClone').html();
    $(document).ready(function() {

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

        $('#transport_id').select2({
            width: '100%'
        });

        var table = null;
        var tableOrder = null;
        $(document).on('click', '.addButton', function() {
            addRow();
        });

        $(document).on('click', '.remove-btn', function() {
            var $row = $(this).closest('tr');
            var $tbody = $row.closest('tbody');

            $row.remove();
            $('.qty, .rate, .discount, #discount').trigger('keyup');
        });

        $(document).on('select2:select', '.item_id', function(e) {
            var row = $(this).parents('tr');
            var customer = $('#customer').val();
            var item_id = $(this).val();
            if (customer && item_id) {
                const http = App.http.jqClient;
                http.post(
                    "{{ route('estimate.getRate') }}", {
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

        $(document).on('keyup', '.qty, .rate, .discount, #discount, #other_charge', function(e) {
            total($(this));
        });

        $(document).on('keyup', '#redeem_coin', function(e) {
            // var redeem = $(this).val();
            // var total = $('#total').val();
            // var totalDiscount = $('#discount_amount').val();
            // var net = Number(total) - Number(totalDiscount);

            // if (!isNaN(redeem) && redeem) {
            // $('#net_amount').val(Math.round(Number(net) - Number(redeem)));
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
                ref.closest('tr').find('.amount').val(Math.round(total - totalDiscount));
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

            // console.log(netAmount);

            $('#discount_amount').val(Math.round(sum * (discount / 100)));
            if (netAmount > 0) {
                var data = ($('#customer').select2('data')[0]);
                if (data.balance == undefined) {
                    data.balance = $('#customer').find(":selected").data('balance');
                }
                if (data.party_type_id == undefined) {
                    data.party_type_id = $('#customer').find(":selected").data('party_type_id');
                }
                if (data.party_type_id == 2) {
                    var redeemCoin = (netAmount * 20) / 100;
                    if (data.balance >= redeemCoin) {
                        $('#redeem_coin').val(Math.round(redeemCoin));
                    } else {
                        $('#redeem_coin').val(data.balance);
                    }
                    netAmount = netAmount - $('#redeem_coin').val();
                    var cashBackCoin = (netAmount * 10) / 100;
                    $('#cash_back_coin').val(Math.round(cashBackCoin));
                } else {
                    $('#redeem_coin').val('0');
                    $('#cash_back_coin').val('0');
                }
                var otherCharge = $('#other_charge').val();

                // console.log(netAmount);

                $('#net_amount').val(Math.round(netAmount + Number(otherCharge)));
            }

        }

        function addRow() {
            $(".append-here").append(clonedRow);
            scrollToBottom();
        };

        function scrollToBottom() {
            var $tableContainer = $('.targetTable').closest('.table-responsive');
            $tableContainer.animate({
                scrollTop: $tableContainer[0].scrollHeight
            }, 500);
        }

        $(document).on('change', '.ajax-customer', function() {
            customerChange($(this));
            $('.append-here').find('tr').remove();
        });

        if (window.edit) {
            customerChange($('#customer'), false);
            $('.item_id').select2({
                width: '100%'
            });
            $('.transport_id').select2({
                width: '100%'
            });
            $('.block').select2({
                width: '100%'
            });
        }

        function customerChange(customer = null, isEdit = true) {
            var data = (customer.select2('data')[0]);
            if (data.color == undefined) {
                data.color = customer.find(":selected").data('color');
                data.group_name = customer.find(":selected").data('group_name');
                data.category_name = customer.find(":selected").data('category_name');
            }
            var discount = data.discount;
            $('#discount').val() == '' ? $('#discount').val(discount) : '';
            var customer_id = $('#customer').val();
            $('.party-color').css('background', data.color)
            $('#partyDetail').text('(' + data.group_name + ' - ' + data.category_name + ')');
            $.ajax({
                showLoader: true,
                url: "{{ route('estimate.getOrder') }}",
                timeout: 8000,
                type: "POST",
                processData: true,
                data: {
                    customer_id: customer_id,
                    type: window.edit
                },
                success: function(response) {
                    if (response.orderCodes) {
                        $('#orderCode').val(response.orderCodes);
                        $('#orderCode').select2({
                            data: response.orderCodes
                        });
                    }
                    $('.order-summary').DataTable().destroy();
                    $('#append-data').html(response?.orderHtml);
                    setTimeout(function() {
                        table = $('.order-summary').DataTable({
                            dom: 'tr',
                            "pageLength": -1,
                            "columnDefs": [{
                                "orderable": false,
                                "targets": 1
                            }]
                        });
                    }, 100);
                    $('#collapse-1').addClass('accordion-collapse collapse show');
                    if (response.customer) {
                        if (isEdit) {
                            response.customer?.other_transport_id && $('#transport_id').val(response.customer?.other_transport_id).trigger('change');
                            response.customer?.other_courier_id && $('#courier_id').val(response.customer?.other_courier_id).trigger('change');
                            response.customer?.bill_group_id && $('#bill_group').val(response.customer?.bill_group_id).trigger('change');
                        }
                        $('#address').val([
                            response.customer?.address,
                            response.customer?.area,
                            response.customer?.city?.name,
                            response.customer?.state?.name,
                            response.customer?.country?.name,
                            response.customer?.pincode,
                            response.customer?.contact_person == null ? '' : `Contact: ${response.customer?.contact_person}`,
                            response.customer?.mobile,
                            response.customer?.gst == null ? '' : `GST: ${response.customer?.gst}`,
                            response.customer?.pan_no == null ? '' : `PAN No: ${response.customer?.pan_no}`,
                        ].filter(Boolean).join(', '));
                    }
                }
            });
        };

        $('#orderCode').on('change', function() {
            var selectedValue = $(this).val();
            table.search('').columns().search('').draw();

            if (selectedValue) {
                table.column(0).search('^' + selectedValue + '$', true, false).draw();
            }

            // $('.accordion-item:not(:first)').addClass('collapsed');
            // $('.collapsed').removeClass('collapsed');
            $('#collapse-1').addClass('accordion-collapse collapse show');
        });

        $(document).on('change', '.orderId', function() {
            addOrder($(this));
        });

        $(document).on('click', '.checkBoxAll', function() {
            var checkbox = $('.orderId').prop('checked', $(this).prop('checked'));
            $('.orderId').each(function() {
                addOrder($(this));
            });
        });

        function addOrder(position) {
            var orderId = position.is(':checked') ? position.val() : '';
            if (orderId) {
                var itemOrder = position.closest('tr').find('.itemOrder').data('itemid');
                var qtyOrder = position.closest('tr').find('.pendingqty').text();
                var rateOrder = position.closest('tr').find('.rateOrder').text();
                var blockOrder = position.closest('tr').find('.blockOrder').text();
                var narrationOrder = position.closest('tr').find('.narrationOrder').text();
                var remarkOrder = position.closest('tr').find('.remarkOrder').text();
                var remarkOtherOrder = position.closest('tr').find('.remarkOtherOrder').text();
                var transportOrder = position.closest('tr').find('.transportOrder').data('transportid');
                var amountOrder = position.closest('tr').find('.amountOrder').text();
                var dateOrder = position.closest('tr').find('.dateOrder').text();
                var designOrder = position.closest('tr').find('.designOrder').text();
                var discountOrder = position.closest('tr').find('.discountOrder').text();
                var orderDate = position.closest('tr').find('.orderDate').text();
                var printTypeOther = position.closest('tr').find('.printTypeOther').text();
                var printOtherId = position.closest('tr').find('.printTypeOther').data('printotherid');
                var is_special = position.closest('tr').find('.orderCode').data('is_special');

                addRow();
                $('.append-here tr').last().find('.item_id').select2({
                    width: '100%'
                });
                $('.append-here tr').last().find('.block').select2({
                    width: '100%'
                });
                $('.append-here tr').last().find('.transport_id').select2({
                    width: '100%'
                });
                $('.append-here tr').last().find('.item_id').find('option:contains("' + itemOrder + '")').prop('selected', true).change();
                $('.append-here tr').last().find('.qty').val(qtyOrder);
                $('.append-here tr').last().find('.order-id').val(orderId);
                $('.append-here tr').last().find('.rate').val(rateOrder);
                $('.append-here tr').last().find('.block').val(blockOrder).change();
                $('.append-here tr').last().find('.narration').val(narrationOrder);
                $('.append-here tr').last().find('.remark').val(remarkOrder);
                $('.append-here tr').last().find('.other_remark').val(remarkOtherOrder);
                $('.append-here tr').last().find('.transport_id').val(transportOrder).change();
                $('.append-here tr').last().find('.amount').val(amountOrder);
                $('.append-here tr').last().find('.date').val(orderDate);
                $('.append-here tr').last().find('.design').val(designOrder);
                $('.append-here tr').last().find('.printTypeOther').text(printTypeOther);
                $('.append-here tr').last().find('.printOtherId').val(printOtherId);
                $('.append-here tr').last().find('.discount').val(Math.round(discountOrder));
                if (is_special == 1) {
                    $('.append-here tr').last().find('.is_special_checkbox').prop('checked', true);
                    $('.append-here tr').last().find('.is_special').val(1);
                }

                $('.qty, .rate, .discount, #discount').trigger('keyup');
            } else {
                var id = position.val();
                $('.order-id').each(function() {
                    if ($(this).val() == id) {
                        $(this).closest('tr').remove();
                    }
                });
                $('.qty, .rate, .discount, #discount').trigger('keyup');
            }
        }

        <?php if (isset($estimate) && $estimate->bill_generated == 'Yes'): ?> $('#rowToCloneContainer input').each(function() {
                $(this).prop('disabled', true);
                $(this).prop('readonly', true);
                $(this).removeAttr('id');
            });
            $('#rowToCloneContainer select').each(function() {
                $(this).prop('disabled', true);
                $(this).prop('readonly', true);
                $(this).removeAttr('id');
            });
        <?php endif; ?>

        $(document).on('change', '.is_special_checkbox', function() {
            if ($(this).is(':checked')) {
                $(this).parents('tr').find('.is_special').val(1);
            } else {
                $(this).parents('tr').find('.is_special').val(0);
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
</script>
@endpush
