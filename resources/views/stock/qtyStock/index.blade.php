@extends("Layouts.app")

@section("title", "Stock Qty")

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
				Manage Stock
			</div>
			<h2 class="page-title">
				Stock
			</h2>
		</div>
	</div>
</div>
@endsection
@section("content")
<div class="row pt-2">
	<div class="col-md-12">
		<div class="card">
			<div class="card-status-top bg-primary"></div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-2">
						<label class="form-label">From Date</label>
						<input type="date" class="form-control" name="fromDate" id="fromDate" value="{{ date('Y-m-01') }}">
					</div>
					<div class="col-md-2">
						<label class="form-label">To Date</label>
						<input type="date" class="form-control" name="toDate" id="toDate" value="{{ date('Y-m-d') }}">
					</div>
					<div class="col-md-2">
						<label class="form-label">Item</label>
						<select class="form-select select2" name="item" id="item">
							<option value="ALL">All</option>
							@foreach ($items as $item)
							<option value="{{ $item->id }}">{{ $item->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-2">
						<label class="form-label">Item Status</label>
						<select class="form-select select2" name="item_status" id="item_status">
							<option value="ALL">All</option>
							<option value="Active">Active</option>
							<option value="Non Active">Non Active</option>
							<option value="Offline">Offline</option>
						</select>
					</div>
					<div class="col-md-2">
						<label class="form-label">Group</label>
						<select class="form-select select2" name="group" id="group">
							<option value="ALL">All</option>
							@foreach ($groups as $group)
							<option value="{{ $group->id }}">{{ $group->group_name }}</option>
							@endforeach
						</select>
					</div>
                    <div class="col-md-2">
						<label class="form-label">Category</label>
						<select class="form-select select2" name="category" id="category">
							<option value="ALL">All</option>
							@foreach ($categories as $categorie)
							<option value="{{ $categorie->id }}">{{ $categorie->name }}</option>
							@endforeach
						</select>
					</div>
                    <div class="col-md-2">
                        <label class="form-label">Is Special</label>
						<select class="form-select select2" name="is_special" id="is_special">
							<option value="">All</option>
							<option value="YES">YES</option>
							<option value="NO">No</option>
						</select>
                    </div>
					<div class="col-md-2">
						<label class="form-label"></label></br>
						<button class="btn btn-primary" id="searchBtn">Search</button>
					</div>
				</div>
			</div>
		</div>
		<div class="card mt-2">
			<div class="card-status-top bg-primary"></div>
			<div class="card-body">
				<div class="row">
					<div id="table"></div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push("javascript")
<script>
	$(document).ready(function() {
		$("#searchBtn").on("click", function() {
			var fromDate = $("#fromDate").val();
			var toDate = $("#toDate").val();
			var item = $("#item").val();
			var group = $("#group").val();
			var status = $("#item_status").val();
            var is_special = $("#is_special").val();
            var category = $("#category").val();

			$.ajax({
				url: "{{ route('stock.getList') }}",
				type: "POST",
				data: {
					fromDate: fromDate,
					toDate: toDate,
					item: item,
					group: group,
					status: status,
                    is_special: is_special,
                    category: category
				},
                showLoader :true,
				success: function(data) {
					$("#table").html(data);
					var stockTable = $(".datatable").DataTable({
						"ordering":  true,
						"searching": true,
						"paging":  true,
						"info":  true,
						responsive:  false,
						lengthMenu: [10, 100, 500,1000, 2000 ,5000],
						pageLength: 100,
						buttons: ["copy", "csv", "excel", "pdf", "print"],
                        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                            "<'row'<'col-sm-12'tr>>" +
                            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>" +
                            "B",
						});

                    var res =  stockTable
                    .buttons()
                    .container()
                    .appendTo('.dataTables_wrapper .col-md-6:eq(0)');
                }
			});
		});
	});
</script>
@endpush
