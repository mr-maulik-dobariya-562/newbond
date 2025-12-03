<div class="row mb-3 align-items-end">
    <div class="form-group col-md-3">
        <label for="itemName">Item Name</label>
        <textarea type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="itemName">{{ old('name', $item?->name ?? '') }}</textarea>
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
        <label for="extraDiscount">Price</label>
        <input type="text" class="form-control @error('price') is-invalid @enderror"
            name="price" id="extraDiscount" step="any"
            value="{{ old('price', $item?->price ?? '') }}" placeholder="">
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
