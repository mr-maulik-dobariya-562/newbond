@extends("Layouts.app")

@section("title", "Feedback")

@section("header")
<style>
    #nprogress .bar {
        z-index: 2000;
    }

    #nprogress .peg {
        box-shadow: 0 0 10px #29d, 0 0 5px #29d;
    }
</style>
<div class="page-header d-print-none">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                Master
            </div>
            <h2 class="page-title">
                Feedback
            </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a class="btn btn-primary d-none d-sm-inline-block add-new-btn" data-bs-toggle="modal" data-bs-target="#city-modal" href="#">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Create new Feedback
                </a>
                <a class="btn btn-primary d-sm-none btn-icon add-new-btn" data-bs-toggle="modal" data-bs-target="#city-modal" href="#" aria-label="Create new report">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
@section("content")
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-status-top bg-primary"></div>
            <div class="card-header">
                <h3 class="card-title">Feedback</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-loader table-vcenter card-table" id="bank-table">
                            <thead>
                                <tr>
                                    <th data-name="id">Serial No </th>
                                    <th data-name="title">Title</th>
                                    <th data-name="message">Message</th>
                                    <th data-name="image">Image</th>
                                    <th data-name="created_by">Created by</th>
                                    <th data-name="created_at">Created At</th>
                                    <th data-name="updated_at">Last Update At</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push("javascript")
<script>
    $(document).ready(function() {
        const modal = $("#city-modal");
        window.edit = false;
        var table = window.table(
            "#bank-table",
            "{{ route('feedback.getList') }}",
        );


        $(".add-new-btn").click(function() {
            modal.find(".title").text("Add");
            modal.find("select").val("");
            modal.parents("form").attr("action", '{{ route("master.bank.store") }}');
            window.edit = false;
        });

        $("#modal-form").submit(function(e) {
            e.preventDefault();
            const F = $(this)
            removeErrors();
            F.find(".save-loader").show();
            const http = App.http.jqClient;
            var U;
            if (window.edit) {
                U = http.put(
                    F.attr("action"),
                    F.serialize(),
                );
            } else {
                U = http.post(
                    F.attr("action"),
                    F.serialize(),
                );
            }
            U.then(res => {
                if (res.success) {
                    table.ajax.reload();
                    modal.modal("hide");
                    sweetAlert("success", res.message);
                } else {
                    sweetAlert("error", res.message);

                }
            }).always(() => {
                F.find(".save-loader").hide()
            })

        });

        $(document).on('click', '.edit-btn', function() {
            const {
                id,
                name,
            } = $(this).data();
            const edit_url = "{{ route('master.bank.update', ':id') }}";
            modal.parents("form").attr("action", edit_url.replace(":id", id));
            modal.find(".title").text("Edit");
            modal.find("input[name='name']").val(name);
            modal.modal("show");
            window.edit = true;
        });
    });
</script>
@endpush
