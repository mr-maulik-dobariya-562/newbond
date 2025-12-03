@extends("Layouts.app")

@section("title", "Item")

@section("header")
	<style>
	</style>
	<div class="page-header d-print-none">
		<div class="row g-2 align-items-center">
			<div class="col">
				<div class="page-pretitle">
					Item Master
				</div>
				<h2 class="page-title">
					Item
				</h2>
			</div>
			<div class="col-auto ms-auto d-print-none">
				<div class="btn-list">
					<a class="btn btn-primary d-none d-sm-inline-block add-new-btn" data-bs-toggle="modal"
						data-bs-target="#modal" href="#">
						<svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
							stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
							stroke-linejoin="round">
							<path stroke="none" d="M0 0h24v24H0z" fill="none" />
							<path d="M12 5l0 14" />
							<path d="M5 12l14 0" />
						</svg>
						Create new item
					</a>
					<a class="btn btn-primary d-sm-none btn-icon add-new-btn" data-bs-toggle="modal" data-bs-target="#modal"
						href="#" aria-label="Create new report">
						<svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
							stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
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
			<!-- <div class="card">
				<div class="card-status-top bg-primary"></div>
				<div class="mt-3 p-2">
					<div class="card-body">
						<div class="container-fluid">
							<div class="row row-cards">
								<div class="form-group col-md-2">
									<label for="itemCategories">Item Categories</label>
									<select class="form-control select2" id="categories">
										<option value="" selected>Selected</option>
										@foreach ($categories as $categorie)
											<option value="{{ $categorie?->id }}">{{ $categorie->name }}</option>
										@endforeach
									</select>
								</div>
								<div class="form-group col-md-2">
									<label for="activeType">Active Type</label>
									<select id="activeType" class="form-control select2">
										<option value="" selected>Selected</option>
										<option value="Active">Active</option>
										<option value="Non Active">Non Active</option>
										<option value="Offline">Offline</option>
									</select>
								</div>
								<div class="form-group col-md-2">
									<label for="group_by">Type</label>
									<select id="itemType" class="form-control select2">
										<option value="" selected>Selected</option>
										<option value="Finish">Finish</option>
										<option value="Raw">Raw</option>
										<option value="Semi-Finished">Semi Finished</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="card-footer">
						<button type="button" class="btn btn-outline-primary float-end" id="search">Search</button>
					</div>
				</div>
			</div> -->
			<div class="card mt-2">
				<div class="card-status-top bg-primary"></div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<div class="table-responsive">
								<table class="table table-loader table-vcenter table-hover card-table"
									data-page_length="500" id="datatable">
									<thead>
										<tr>
											<th data-name="id">Serial No </th>
											<th data-name="action" data-orderable="false">Action</th>
											<th data-name="name" style="min-width: 200px;">Name</th>
											<th data-name="image">Image</th>
											<th data-name="categories">Categories</th>
											<th data-name="price">Price</th>
											<th data-name="created_by">Created By</th>
											<th data-name="created_at">Created At</th>
											<th data-name="updated_at">Last Update At</th>
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
	</div>
	<form id="modal-form" action="{{ route('master.item.store') }}" enctype="multipart/form-data" method="POST">
		@csrf
		<div class="modal modal-blur fade pt-5" id="modal" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-full-width modal-dialog-scrollable" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title"> <span class="title">Add</span> Item</h5>
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
@endsection

@push("javascript")
	<script>
		$(document).ready(function () {
			const modal = $("#modal");
			var table = window.table(
				"#datatable",
				"{{ route('master.item.getList') }}", {
				override: {
					responsive: false
				},

				additionalData: () => {
					return {
						_token: "{{ csrf_token() }}",
						categories: $('#categories').val() || [],
					}
				}
			},
				{
					lengthMenu: [10, 100, 1000, 5000],
					pageLength: 100,
				}
			);
			window.edit = false;

			$("#search").click(function () {
				table.draw();
			});

			$(".add-new-btn").click(function () {
				$("#results").html('');
				$.ajax({
					url: "{{ route('master.item.model') }}",
					cache: false,
					type: "POST",
					success: function (html) {
						$("#results").html(html);
						$('#categories').select2({
							dropdownParent: $('#modal'),
						});
						window.mainRow = $('.mainRow')[0].outerHTML;
					}
				});
				modal.find(".title").text("Add");
				modal.parents("form").attr("action", '{{ route("master.item.store") }}');
				window.edit = false;
			});

			$("#modal-form").submit(function (e) {
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
					success: function (res) {
						if (res.success) {
							table.ajax.reload();
							modal.modal("hide");
							sweetAlert("success", res.message);
						} else {
							sweetAlert("error", res.message);
						}
					},
					// error: function (xhr, status, error) {
					// 	sweetAlert("error", "An error occurred while processing the request.");
					// },
					complete: function () {
						F.find(".save-loader").hide();
					}
				});
			});

			$(document).on('click', '.edit-btn', function () {
				const id = $(this).data("id");
				const permission = $(this).data("permission");
				$.ajax({
					url: "{{ route('master.item.model') }}",
					cache: false,
					type: "POST",
					data: {
						id: id
					},
					success: function (html) {
						$("#results").html(html);
						$('#categories').select2({
							dropdownParent: $('#modal'),
						});
						if (!permission) {
							$(".modal-footer").css('display', 'none');
						}
					}
				});
				const edit_url = "{{ route('master.item.update', ':id') }}";
				modal.parents("form").attr("action", edit_url.replace(":id", id));
				modal.find(".title").text("Edit");
				modal.modal("show");
				window.edit = true;
			})
		});
	</script>
	<script>
		function previewImage(event) {
			var input = event.target;
			var reader = new FileReader();
			reader.onload = function () {
				var dataURL = reader.result;
				var imagePreview = document.getElementById('imagePreview');
				imagePreview.src = dataURL;
				imagePreview.style.display = 'block';
			};
			reader.readAsDataURL(input.files[0]);
		}
	</script>
@endpush
