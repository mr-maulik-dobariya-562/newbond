<div class="row mb-3 align-items-end">
    <div class="col-md-3 form-group">
        <label class="form-label">Group Name</label>
        <input class="form-control" type="text" name="group_name" value="{{ old('group_name', $itemGroup?->group_name ?? '') }}" placeholder="Enter Group Name" />
    </div>
    <div class="col-md-2 form-group">
        <label class="form-label">Bill Title</label>
        <input class="form-control" type="text" name="bill_title" value="{{ old('bill_title', $itemGroup?->bill_title ?? '') }}" placeholder="Enter Bill Title" />
    </div>
    <div class="col-md-1 form-group">
        <label class="form-label">Sequence Number</label>
        <input class="form-control" type="number" name="sequence_number" value="{{ old('sequence_number', $itemGroup?->sequence_number ?? '') }}" placeholder="Enter Sequence Number" />
    </div>
    <div class="col-md-2 form-group">
        <label class="form-label">GST</label>
        <input class="form-control" type="text" name="gst" value="{{ old('gst', $itemGroup?->gst ?? '') }}" placeholder="Enter GST" />
    </div>
    <div class="col-md-2 form-group">
        <label class="form-label">Retail WP Available</label>
        <select class="form-select select2" data-tags="true" name="retail_wp_available">
            <option value="YES" <?= old("retail_wp_available", $itemGroup?->retail_wp_available ?? '') == 'YES' ? 'selected' : ''; ?>>YES</option>
            <option value="NO" <?= old("retail_wp_available", $itemGroup?->retail_wp_available ?? '') == 'NO' ? 'selected' : ''; ?>>NO</option>
        </select>
    </div>
    <div class="col-md-2 form-group">
        <label class="form-label">Case Type</label>
        <select class="form-select select2" name="case_type_id">
            <option value="">Select Case Type</option>
            @foreach ($caseTypes as $caseType)
                <option value="{{ $caseType->id }}" {{ old('case_type_id', $itemGroup?->case_type_id ?? '') == $caseType->id ? 'selected' : '' }}>{{ $caseType->title }}</option>
            @endforeach
        </select>
    </div>

    <h2 class="pt-3">Extra Price On Printing</h2>
    <div class="col-md-12 row">
        @foreach ($printTypes as $printType)
        <div class="col-md-2 pt-2 form-group">
            <label class="form-label">{{ $printType->name }}</label>
            <input class="form-control" type="number" name="extra_price[]" step="any" value="{{ old('extra_price', $printType?->extra_price ?? '') }}" placeholder="Enter Extra Price" />
        </div>
        @endforeach
    </div>
    <h2 class="pt-3">USD Price On Printing</h2>
    <div class="col-md-12 row">
        @foreach ($printTypes as $printType)
        <div class="col-md-2 pt-2 form-group">
            <label class="form-label">{{ $printType->name }}</label>
            <input class="form-control" type="number" name="usd_extra_price[]" step="any" value="{{ old('usd_extra_price', $printType?->usd_extra_price ?? '') }}" placeholder="Enter USD Price" />
        </div>
        @endforeach
    </div>

    <div class="col-md-6 row">
        <h2 class="pt-3 col-md-12 text-center">Min Quantity Dealer</h2>
        @foreach ($printTypes as $printType)
        <input type="hidden" name="print_type_id[]" value="{{ $printType->id }}">
        <div class="col-md-6 pt-2 form-group">
            <label class="form-label">Min QTY {{ $printType->name }} Dealer</label>
            <input class="form-control" type="number" name="min_dealer[]" step="any" value="{{ old('min_dealer', $printType?->min_dealer ?? '') }}" placeholder="Enter Min QTY" />
        </div>
        <div class="col-md-6 pt-2 form-group">
            <label class="form-label">Total QTY {{ $printType->name }} Dealer</label>
            <input class="form-control" type="number" name="total_dealer[]" step="any" value="{{ old('total_dealer', $printType?->total_dealer ?? '') }}" placeholder="Enter Total QTY" />
        </div>
        @endforeach
    </div>

    <div class="col-md-6 row">
        <h2 class="pt-3 col-md-12 text-center">Min Quantity Retail</h2>
        @foreach ($printTypes as $printType)
        <div class="col-md-6 pt-2 form-group">
            <label class="form-label">Min QTY {{ $printType->name }} Retail</label>
            <input class="form-control" type="number" name="min_retail[]" step="any" value="{{ old('min_retail', $printType?->min_retail ?? '') }}" placeholder="Enter Min QTY" />
        </div>
        <div class="col-md-6 pt-2 form-group">
            <label class="form-label">Total QTY {{ $printType->name }} Retail</label>
            <input class="form-control" type="number" name="total_retail[]" step="any" value="{{ old('total_retail', $printType?->total_retail ?? '') }}" placeholder="Enter Total QTY" />
        </div>
        @endforeach
    </div>

    <div class="row pt-2">
        <div class="additional">
            @if(isset($itemGroupPrintExtras) && count($itemGroupPrintExtras) > 0)
            @foreach ($itemGroupPrintExtras as $printExtra)
            <div class="removeRow row pt-2">
                <div class="col-md-3 form-group">
                    <label class="form-label">Print Type Extra</label>
                    <select class="form-select printType" name="print_extra[]">
                        @foreach ($printTypeExtras as $printTypeExtra)
                            <option value="{{ $printTypeExtra->id }}" {{ $printExtra->print_extra_id == $printTypeExtra->id ? 'selected' : '' }}>{{ $printTypeExtra->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label class="form-label">Amount</label>
                    <input class="form-control amount" type="number" name="amount[]" step="any" value="{{ old('amount', $printExtra?->amount ?? '') }}" placeholder="Enter Amount" />
                </div>
                <div class="col-md-1 form-group pt-5">
                    <button type="button" class="btn btn-danger form-control remove-btn"><i class="fas fa-trash"></i></button>
                </div>
            </div>
            @endforeach
            @else
            <div class="removeRow row pt-2">
                <div class="col-md-3 form-group">
                    <label class="form-label">Print Type Extra</label>
                    <select class="form-select printType" name="print_extra[]">
                        <option value="">Select Print Type Extra</option>
                        @foreach ($printTypeExtras as $printTypeExtra)
                            <option value="{{ $printTypeExtra->id }}">{{ $printTypeExtra->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label class="form-label">Amount</label>
                    <input class="form-control amount" type="number" name="amount[]" step="any" value="{{ old('amount') }}" placeholder="Enter Amount" />
                </div>
                <div class="col-md-1 form-group pt-5">
                    <button type="button" class="btn btn-danger form-control remove-btn"><i class="fas fa-trash"></i></button>
                </div>
            </div>
            @endif
        </div>
    </div>
    <div class="text-left pt-1">
        <button type="button" class="btn btn-success addButton"><i class="fas fa-plus"></i> &nbsp;Add</button>
    </div>
</div>
