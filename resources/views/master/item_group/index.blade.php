@extends("Layouts.app")

@section("title", "Item Group")

@section("header")
<style>
    #nprogress .bar {
        z-index: 2000;
        /* Adjust higher than the modal's z-index */
    }

    #nprogress .peg {
        box-shadow: 0 0 10px #29d, 0 0 5px #29d;
    }

    .btn-rounded {
        border-radius: 10em;
        padding: 6px 8px;
        font-size: small;
        text-transform: none;
        text-shadow: none !important;
        background: #eaeaea;
        border-color: transparent;
        border: none;
    }
</style>
<div class="page-header d-print-none">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                Master
            </div>
            <h2 class="page-title">
                Item Group
            </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a class="btn btn-primary d-none d-sm-inline-block add-new-btn" data-bs-toggle="modal" data-bs-target="#country-modal" href="#">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Create new Item Group
                </a>
                <a class="btn btn-primary d-sm-none btn-icon add-new-btn" data-bs-toggle="modal" data-bs-target="#country-modal" href="#" aria-label="Create new report">
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
                <h3 class="card-title">Item Group</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-loader table-vcenter table-hover card-table" data-page_length="100" id="country-table">
                            <thead>
                                <tr>
                                    <th data-name="id">Serial No </th>
                                    <th data-name="name">Group Name</th>
                                    <th data-name="sequence">Sequence Number</th>
                                    <th data-name="gst">GST</th>
                                    <th data-name="retail_wp_available">Retail WP Available</th>
                                    <th data-name="created_by">Created By</th>
                                    <th data-name="created_at">Created At</th>
                                    <th data-name="updated_at">Last Update At</th>
                                    <th data-name="image" data-orderable="false">Image</th>
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
<form id="country-form" action="{{ route('master.print-type.store') }}" enctype="multipart/form-data" method="POST">
    @csrf
    <div class="modal modal-blur fade pt-5" id="country-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-full-width modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="progress progress-sm" style="display:none">
                    <div class="progress-bar progress-bar-indeterminate bg-primary"></div>
                </div>
                <div class="modal-header">
                    <h5 class="modal-title"> <span class="title">Add</span> Item Group</h5>
                    <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="results"></div>
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
<form id="image-form" action="{{ route('master.item-group.printGroupImage') }}" enctype="multipart/form-data" method="POST">
    @csrf
    <div class="modal modal-blur fade" id="printGroupImage" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="progress progress-sm" style="display:none">
                    <div class="progress-bar progress-bar-indeterminate bg-primary"></div>
                </div>
                <div class="modal-header">
                    <h5 class="modal-title"> <span class="title">Add</span> Print & Group Image</h5>
                    <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="printImage"></div>
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
        const countryModal = $("#country-modal");
        const printGroupImageModal = $("#printGroupImage");
        var mainRow = '';
        var table = window.table(
            "#country-table",
            "{{ route('master.item-group.getList') }}",
        );
        window.edit = false;

        $(".add-new-btn").click(function() {
            $("#results").html('');
            $.ajax({
                url: "{{ route('master.item-group.model') }}",
                cache: false,
                type: "POST",
                success: function(html) {
                    $("#results").html(html);
                    mainRow = $('.additional .removeRow').first().clone();
                    $('.printType').select2({
                        dropdownParent: countryModal,
                    });
                }
            });
            countryModal.find(".title").text("Add");
            countryModal.parents("form").attr("action", '{{ route("master.item-group.store") }}');
            window.edit = false;
        });

        $("#country-form").submit(function(e) {
            e.preventDefault();
            const F = $(this)
            removeErrors();
            F.find(".save-loader,.progress").show();
            const http = App.http.jqClient;
            var U;
            if (window.edit) {
                U = http.put(
                    F.attr("action"),
                    F.serialize(),
                )
            } else {
                U = http.post(
                    F.attr("action"),
                    F.serialize(),
                )
            }
            U.then(res => {
                if (res.success) {
                    table.ajax.reload();
                    countryModal.modal("hide");
                    sweetAlert("success", res.message);
                } else {
                    sweetAlert("error", res.message);

                }
            }).catch((error) => {
                sweetAlert("error", error?.responseJSON?.message || "Something went wrong.. please try again");
            }).always(() => {
                F.find(".save-loader,.progress").hide()
            })

        });

        $(document).on('click', '.edit-btn', function() {
            const id = $(this).data("id");
            const permission = $(this).data("permission");

            $.ajax({
                url: "{{ route('master.item-group.model') }}",
                cache: false,
                type: "POST",
                data: {
                    id: id
                },
                success: function(html) {
                    $("#results").html(html);
                    $("#results .select2").select2();
                    mainRow = $('.additional .removeRow').first().clone();
                    $('.printType').select2({
                        dropdownParent: countryModal,
                    });
                    if (!permission) {
                        $(".modal-footer").css('display', 'none');
                    }
                }
            });

            const edit_url = "{{ route('master.item-group.update', ':id') }}";
            countryModal.parents("form").attr("action", edit_url.replace(":id", id));
            countryModal.find(".title").text("Edit");
            countryModal.modal("show");
            window.edit = true;
        })

        $(document).on('click', '.addButton', function() {
            if (checkForDuplicates()) {
                sweetAlert("error", "Print type extra already exist");
                $(this).val('').trigger('change');
            } else {
                var clonedRow = mainRow.clone(); // Clone the mainRow each time
                $(".additional").append(clonedRow); // Append the cloned row to the .additional container
                // Reinitialize select2 for the newly added .printType element
                clonedRow.find('.printType').select2({
                    dropdownParent: countryModal,
                }).val(null).trigger('change');
                clonedRow.find('.printType').select2('open');
                clonedRow.find('.amount').val('');
            }
        });

        $(document).on('click', '.remove-btn', function() {
            var $row = $(this).closest('.removeRow');
            var $tbody = $row.closest('.additional');
            if ($tbody.find('.removeRow').length > 1) {
                $row.remove();
            } else {
                sweetAlert("error", 'Last row cannot be deleted.');
            }
        });

        function checkForDuplicates() {
            let selectedValues = [];
            let hasDuplicates = false;

            // Iterate through each select element with the class 'printType'
            $('.printType').each(function() {
                let value = $(this).val();
                if (value) {
                    if (selectedValues.includes(value)) {
                        hasDuplicates = true;
                        return false; // Exit loop if duplicate is found
                    }
                    selectedValues.push(value);
                }
            });

            return hasDuplicates;
        }

        $(document).on('select2:select', '.printType', function() {
            if (checkForDuplicates()) {
                sweetAlert("error", "Print type extra already exist");
                $(this).val('').trigger('change');
            }
        });

        $(document).on("click", ".image-btn", function() {
            printGroupImageModal.modal("show");
            $("#printImage").html('');
            $.ajax({
                url: "{{ route('master.item-group.printTypeGet') }}",
                cache: false,
                type: "POST",
                data: {
                    id: $(this).data("id")
                },
                success: function(html) {
                    $("#printImage").html(html);
                }
            });
            printGroupImageModal.find(".title").text("Add");
            printGroupImageModal.parents("form").attr("action", '{{ route("master.item-group.printGroupImage") }}');
        });

        $("#image-form").submit(function(e) {

            e.preventDefault();
            const F = $(this);
            removeErrors();
            F.find(".save-loader").show();

            // Create a new FormData object
            var formData = new FormData(this);

            // Determine the request type and URL
            var actionUrl = F.attr("action");

            // If it's a PUT request, append _method=PUT to the form data
            if (window.edit) {
                formData.append('_method', 'POST');
            }

            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    if (res.success) {
                        table.ajax.reload();
                        printGroupImageModal.modal("hide");
                        sweetAlert("success", res.message);
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

        $('#printImage').on('change', '.image', function(event) {
            var input = event.target; // Get the current input element
            var index = $('.image').index(input); // Get the index of the current input

            var reader = new FileReader();

            reader.onload = function() {
                var dataURL = reader.result;
                // Use the index to select the correct imagePreview element
                $('.imagePreview').eq(index).attr('src', dataURL).show(); // Set and show the image preview
            };

            reader.readAsDataURL(input.files[0]); // Read the file input
        });
    });
</script>
@endpush
