<div class="row mb-3 align-items-end">
    <div class="col-md-9 row">
        @foreach ($printTypes as $printType)
        <input type="hidden" name="print_type_id[]" value="{{ $printType->print_type_id ?? $printType->id }}">
        <input type="hidden" name="item_group_id[]" value="{{ $printType->item_group_id }}">
        <div class="col-md-3 pt-2 form-group">
            <label class="form-label">{{ $printType->name }}</label>
        </div>
        <div class="col-md-6 pt-2 form-group">
            <label class="form-label">Image</label>
            <input class="form-control image" type="file" name="image[]" value="" />
        </div>
        <div class="col-md-3 pt-2">
            @if (isset($printType->image) && $printType->image != null)
                <img class="imagePreview" src="{{ $printType->image }}" alt="Image Preview" style="max-width: 80%;" />
            @else
                <img class="imagePreview" src="" alt="Image Preview" style="display: none; max-width: 80%;" />
            @endif
        </div>
        @endforeach
    </div>
</div>
