<div class="row mb-3 align-items-end">
    <div class="form-group col-md-3">
        <label for="itemName">Item Name</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="itemName"
            value="{{ old('name', $item?->name ?? '') }}" placeholder="">
    </div>
    <div class="form-group col-md-3">
        <label for="itemCategories">Item Categories</label>
        <select class="form-select @error('categories_id') is-invalid @enderror" id="categories" required
            data-tags="true" name="categories_id">
            <option value="" selected>Selected</option>
            @foreach ($categories as $categorie)
                <option value="{{ $categorie?->id }}" <?= isset($item) && $categorie?->id == $item->categories_id ? "selected" : ""?>>
                    {{ $categorie->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-3">
        <label for="extraDiscount">Extra Discount Dealer</label>
        <input type="text" class="form-control @error('extra_dealer_discount') is-invalid @enderror"
            name="extra_dealer_discount" id="extraDiscount" step="any"
            value="{{ old('extra_dealer_discount', $item?->extra_dealer_discount ?? '') }}" placeholder="">
    </div>
    <div class="form-group col-md-3">
        <label for="extraDiscount">Extra Discount Retail</label>
        <input type="text" class="form-control @error('extra_retail_discount') is-invalid @enderror"
            name="extra_retail_discount" id="extraDiscount" step="any"
            value="{{ old('extra_retail_discount', $item?->extra_retail_discount ?? '') }}" placeholder="">
    </div>
    <div class="col-md-3 pt-2">
        <label for="itemType">Item Type</label>
        <select id="itemType" name="type" class="form-control">
            <option value="" selected>Selected</option>
            <option value="Finish" <?= old("type", $item?->type ?? '') == 'Finish' ? 'selected' : ''; ?>>Finish</option>
            <option value="Raw" <?= old("type", $item?->type ?? '') == 'Raw' ? 'selected' : ''; ?>>Raw</option>
            <option value="Semi-Finished" <?= old("type", $item?->type ?? '') == 'Semi-Finished' ? 'selected' : ''; ?>>
                Semi-Finished</option>
        </select>
    </div>
    <div class="form-group col-md-3 pt-2">
        <label for="activeType">Active Type</label>
        <select id="activeType" name="active_type" class="form-control">
            <option value="" selected>Selected</option>
            <option value="Active" <?= old("active_type", $item?->active_type ?? '') == 'Active' ? 'selected' : ''; ?>>
                Active</option>
            <option value="Non Active" <?= old("active_type", $item?->active_type ?? '') == 'Non Active' ? 'selected' : ''; ?>>Non Active</option>
            <option value="Offline" <?= old("active_type", $item?->active_type ?? '') == 'Offline' ? 'selected' : ''; ?>>
                Offline</option>
        </select>
    </div>
    <div class="form-group col-md-3 pt-2">
        <label for="packing">Packing</label>
        <input type="text" class="form-control @error('packing') is-invalid @enderror" name="packing" id="packing"
            value="{{ old('packing', $item?->packing ?? '') }}" placeholder="">
    </div>
    <div class="form-group col-md-3 pt-2">
        <label for="minimumQty">Minimum Qty</label>
        <input type="text" class="form-control @error('minimum_qty') is-invalid @enderror" name="minimum_qty"
            id="minimumQty" value="{{ old('minimum_qty', $item?->minimum_qty ?? '') }}" placeholder="">
    </div>
    <div class="form-group col-md-3 pt-1">
        <label class="form-label">Item Image</label>
        <input class="form-control @error('image') is-invalid @enderror" type="file" name="image" id="image"
            onchange="previewImage(event)" />
    </div>
    <div class="form-group col-md-3 pt-1">
        @if (isset($item->viewUrl))
            <img id="imagePreview" src="{{ $item->viewUrl }}" alt="Image Preview" style="max-width: 30%;" />
        @else
            <img id="imagePreview" src="" alt="Image Preview" style="display: none; max-width: 30%;" />
        @endif
    </div>
    <div class="form-group col-md-6"></div>
    <div class="col-md-6 pb-5 mt-5" style="background-color: #f39494;">
        <h2 class="text-center pt-2"><b>CURRENT Price</b></h2>
        <div class="row">
            <div class="col-md-4">
                <label for="dealerCurrentPrice">Dealer</label>
                <input type="text" class="form-control @error('dealer_current_price') is-invalid @enderror"
                    name="dealer_current_price" step="any" id="dealerCurrentPrice"
                    value="{{ old('dealer_current_price', $item?->dealer_current_price ?? '') }}" placeholder="">
            </div>
            <div class="col-md-4">
                <label for="retailCurrentPrice">Retail</label>
                <input type="text" class="form-control @error('retail_current_price') is-invalid @enderror"
                    name="retail_current_price" step="any" id="retailCurrentPrice"
                    value="{{ old('retail_current_price', $item?->retail_current_price ?? '') }}" placeholder="">
            </div>
            <div class="col-md-4">
                <label for="usdCurrentPrice">USD</label>
                <input type="text" class="form-control @error('usd_current_price') is-invalid @enderror"
                    name="usd_current_price" step="any" id="usdCurrentPrice"
                    value="{{ old('usd_current_price', $item?->usd_current_price ?? '') }}" placeholder="">
            </div>
        </div>
    </div>
    <div class="col-md-6 pb-5 mt-5" style="background-color: #9dcf9d;">
        <h2 class="text-center pt-2"><b>OLD Price</b></h2>
        <div class="row">
            <div class="col-md-4">
                <label for="dealerOldPrice">Dealer</label>
                <input type="text" class="form-control @error('dealer_old_price') is-invalid @enderror"
                    name="dealer_old_price" step="any" id="dealerOldPrice"
                    value="{{ old('dealer_old_price', $item?->dealer_old_price ?? '') }}" placeholder="">
            </div>
            <div class="col-md-4">
                <label for="retailOldPrice">Retail</label>
                <input type="text" class="form-control @error('retail_old_price') is-invalid @enderror"
                    name="retail_old_price" step="any" id="retailOldPrice"
                    value="{{ old('retail_old_price', $item?->retail_old_price ?? '') }}" placeholder="">
            </div>
            <div class="col-md-4">
                <label for="usdOldPrice">USD</label>
                <input type="text" class="form-control @error('usd_old_price') is-invalid @enderror"
                    name="usd_old_price" step="any" id="usdOldPrice"
                    value="{{ old('usd_old_price', $item?->usd_old_price ?? '') }}" placeholder="">
            </div>
        </div>
    </div>
    <div class="col-md-5 pb-5 mt-5" style="background-color: #f39494;">
        <h2 class="text-center pt-2"><b>Export Cartoon</b></h2>
        <div class="row">
            <div class="col-md-6">
                <label for="dealerCurrentPrice">Size</label>
                <select name="export_size_id" class="form-control size">
                    <option value="" selected>Selected</option>
                    @foreach ($cartoons as $cartoon)
                        <option value="{{ $cartoon->id }}" <?= isset($item?->export_size_id) && $item?->export_size_id == $cartoon->id ? 'selected' : ''; ?>>{{ $cartoon->size }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="retailCurrentPrice">Weight</label>
                <input type="text" class="form-control @error('export_weight') is-invalid @enderror"
                    name="export_weight" step="any" id="retailCurrentPrice"
                    value="{{ old('export_weight', $item?->export_weight ?? '') }}" placeholder="">
            </div>
        </div>
    </div>
    <div class="form-group col-md-2"></div>
    <div class="col-md-5 pb-5 mt-5" style="background-color: #9dcf9d;">
        <h2 class="text-center pt-2"><b>Local Cartoon</b></h2>
        <div class="row">
            <div class="col-md-6">
                <label for="dealerCurrentPrice">Size</label>
                <select name="local_size_id" class="form-control size">
                    <option value="" selected>Selected</option>
                    @foreach ($cartoons as $cartoon)
                        <option value="{{ $cartoon->id }}" <?= isset($item?->local_size_id) && $item?->local_size_id == $cartoon->id ? 'selected' : ''; ?>>{{ $cartoon->size }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="retailCurrentPrice">Weight</label>
                <input type="text" class="form-control @error('local_weight') is-invalid @enderror" name="local_weight"
                    step="any" id="weight" value="{{ old('local_weight', $item?->local_weight ?? '') }}" placeholder="">
            </div>
        </div>
    </div>
    @foreach ($printTypes as $printType)
        <div class="col-md-2 p-5">
            <input type="hidden" name="print_type_id[]" value="{{ $printType->id }}" />
            <label for="form-check-label">
                <h3>{{ $printType->name }}</h3>
                <input class="form-check-input checkbox" style="border: 1px solid;" type="checkbox"
                    <?= isset($printType->checkbox) && $printType->checkbox == '1' ? 'checked' : ''; ?>>
                <input class="form-check-input check" style="border: 1px solid;" type="hidden" name="checkbox[]"
                    value="<?= isset($printType->checkbox) && $printType->checkbox == '1' ? '1' : '0'; ?>">
            </label>
        </div>
    @endforeach
</div>
<script>
    $(document).ready(function () {
        window.editItem = <?= isset($item) ? 'false' : 'true';  ?>;
        if (window.editItem) {
            $('.check').val(0);
        }
        $('.checkbox').change(function () {
            if ($(this).is(':checked')) {
                $(this).parent().find('.check').val(1);
            } else {
                $(this).parent().find('.check').val(0);
            }
        });
    });
</script>