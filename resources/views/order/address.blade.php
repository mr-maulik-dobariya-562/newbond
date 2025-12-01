<div class="modal-body">
    <div class="row col-md-12">
        <label class='col-2 col-form-label required'>Address : </label>
        <div class="col">
            @foreach ($billAddress as $address)
            <label class="form-check">
                <input class="form-check-input addressMulti" type="radio" name="radios" value="{{ $address->address }}">
                <span class="form-check-label">{{ $address->address }}</span>
            </label>
            @endforeach
            <!-- <button type="button" class="btn btn-success" id="addAddress">Add New Address</button> -->
        </div>
    </div>
</div>