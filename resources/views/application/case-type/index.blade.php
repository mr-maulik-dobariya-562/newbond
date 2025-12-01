@extends("Layouts.app")

@section("title", "Case Type")

@section("header")
<style>
</style>
<div class="page-header d-print-none">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                Master
            </div>
            <h2 class="page-title">
                Case Type
            </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a class="btn btn-primary d-none d-sm-inline-block add-new-btn" data-bs-toggle="modal"
                    data-bs-target="#modal" href="#">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Create new Case Type
                </a>
                <a class="btn btn-primary d-sm-none btn-icon add-new-btn" data-bs-toggle="modal" data-bs-target="#modal"
                    href="#" aria-label="Create new report">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
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
                <h3 class="card-title">Case Type</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-loader table-vcenter table-hover card-table" id="datatable">
                            <thead>
                                <tr>
                                    <th data-name="id">Serial No </th>
                                    <th data-name="title">Title</th>
                                    <th data-name="sequence_number">Sequence Number</th>
                                    <th data-name="is_active">Is Active</th>
                                    <th data-name="created_by">Created By</th>
                                    <th data-name="created_at">Created At</th>
                                    <th data-name="updated_at">Last Update At</th>
                                    <th data-name="action" data-orderable="false">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<form id="modal-form" action="{{ route('application.case-type.store') }}" enctype="multipart/form-data" method="POST">
    @csrf
    <div class="modal modal-blur fade" id="modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> <span class="title">Add</span> Case Type</h5>
                    <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2 align-items-end">
                        <div class="col-md-12">
                            <label class="form-label">Title</label>
                            <input class="form-control" type="text" name="title" placeholder="Enter Title" />
                        </div>
                    </div>
                    <div class="row mb-2 align-items-end">
                        <div class="col-md-9">
                            <label class="form-label">Sequence Number</label>
                            <input type="number" class="form-control" name="sequence_number" id="sequence_number" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-check form-switch">Is Active
                                <input type="checkbox" class="form-check-input check-item" id="is_active" name="is_active">
                            </label>
                        </div>
                    </div>
                    <div class="row mb-2 align-items-end">
                        <div class="col-md-12">
                            <label class="form-label">Image</label>
                            <input class="form-control" type="file" name="image" id="image" onchange="previewImage(event)" />
                        </div>
                    </div>
                    <div class="col-md-6"></div>
                    <div class="col-md-12 mt-3">
                        <img id="imagePreview" src="" alt="Image Preview" style="display: none; max-width: 30%;" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn me-auto" data-bs-dismiss="modal" type="button">Close</button>
                    <button class="btn btn-primary" type="submit">
                        Save <i class="fa-solid fa-spinner fa-spin ms-1 save-loader" style="display:none"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push("javascript")
<script>
    $(document).ready(function() {
        const http = App.http.jqClient;
        const modal = $("#modal");
        var table = window.table(
            "#datatable",
            "{{ route('application.case-type.getList') }}",
        );
        window.edit = false;

        $(".add-new-btn").click(function() {
            $('#imagePreview').hide();
            modal.find(".title").text("Add");
            modal.find("select").select2();
            modal.find("select").val(null).trigger("change");
            modal.parents("form").attr("action", '{{ route("application.case-type.store") }}');
            window.edit = false;
        });

        $("#modal-form").submit(function(e) {
            e.preventDefault();
            const F = $(this);
            removeErrors();
            F.find(".save-loader").show();

            var formData = new FormData(this);

            var actionUrl = F.attr("action");
            var requestType = window.edit ? 'POST' : 'POST';

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
                        table.ajax.reload();
                        modal.modal("hide");
                        sweetAlert("success", res.message);
                    } else {
                        sweetAlert("error", res.message);
                    }
                },
                complete: function() {
                    F.find(".save-loader").hide();
                }
            });
        });

        $(document).on('click', '.edit-btn', function() {
            const id = $(this).data("id");
            const title = $(this).data("title");
            const sequence_number = $(this).data("sequence_number");
            const is_active = $(this).data("is_active");
            const image = $(this).data("image");
            const edit_url = "{{ route('application.case-type.update', ':id') }}";
            modal.parents("form").attr("action", edit_url.replace(":id", id));
            modal.find(".title").text("Edit");
            modal.find("input[name='title']").val(title);
            modal.find("input[name='sequence_number']").val(sequence_number);
            modal.find("input[name='is_active']").prop("checked", is_active);

            // Set the image preview
            if (image) {
                $('#imagePreview').attr('src', image).show();
            } else {
                $('#imagePreview').hide();
            }
            modal.modal("show");
            window.edit = true;
        })

        $(document).on('change', '.check-item', function() {
            $(this).val($(this).prop("checked") ? 1 : 0);
        });
    });
</script>
<script>
    function previewImage(event) {
        var input = event.target;
        var reader = new FileReader();
        reader.onload = function() {
            var dataURL = reader.result;
            var imagePreview = document.getElementById('imagePreview');
            imagePreview.src = dataURL;
            imagePreview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
</script>
@endpush
