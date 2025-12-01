@extends("Layouts.app")

@section("title", "Inward")

@section("header")
<div class="page-header d-print-none">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                Production
            </div>
            <h2 class="page-title">
                Inward
            </h2>
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
                <form action="{{ route('production.inward.getList') }}" method="post" id="inwardForm">
                    @csrf
                    <div class="card-body">
                        <div class="container-fluid">
                            <div class="row row-cards">
                                <div class="col-md-2 ">
                                    <div class="form-group">
                                        <label for="from" class="form-label">Date</label>
                                        <input type="date" value="<?= date('Y-m-d'); ?>" class="form-control from" name="date">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-outline-primary float-end" id="search">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push("javascript")
<script>
</script>
@endpush
