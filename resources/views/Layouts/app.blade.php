<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge" />
	<title> @yield("title") | {{ config("app.name") }}</title>
	<meta name="csrf-token" content="{{ csrf_token() }}" />
	<link rel="icon" type="image/x-icon" href="{{ asset('assets') }}/static/logo.png">
	<!-- CSS files -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
		integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
		crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link href="{{ asset('assets') }}/dist/css/tabler.min.css?1692870487" rel="stylesheet" />
	<link href="{{ asset('assets') }}/dist/css/tabler-flags.min.css?1692870487" rel="stylesheet" />
	<link href="{{ asset('assets') }}/dist/css/tabler-payments.min.css?1692870487" rel="stylesheet" />
	<link href="{{ asset('assets') }}/dist/css/tabler-vendors.min.css?1692870487" rel="stylesheet" />
	<link href="{{ asset('assets') }}/dist/css/demo.min.css?1692870487" rel="stylesheet" />
	<link href="{{ asset('assets') }}/dist/css/main.css" rel="stylesheet" />
	<link href="{{ asset('assets') }}/dist/css/spinkit.css" rel="stylesheet" />

	<link rel="stylesheet" href="{{ asset('assets') }}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" href="{{ asset('assets') }}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
	<link rel="stylesheet" href="{{ asset('assets') }}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
	<!-- Select2 Plugins -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
	<link rel="stylesheet"
		href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
	<link rel="stylesheet" href="https://unpkg.com/nprogress@0.2.0/nprogress.css" />
	<link rel="stylesheet"
		href="https://cdn.jsdelivr.net/npm/bootstrap-switch@3.4.0/dist/css/bootstrap3/bootstrap-switch.min.css">

	<link rel="stylesheet"
		href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
	<style>
		@import url('https://rsms.me/inter/inter.css');

		:root {
			--tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
		}

		body {
			font-feature-settings: "cv03", "cv04", "cv11";
		}

		.select2-container--bootstrap-5 .select2-selection {
			font-size: unset !important;
		}

		.select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option {
			font-size: unset !important;
		}

		input[readonly] {
			background-color: #eee;
		}

		/*
		html {
			zoom: 90%;
		} */
	</style>
	@stack("styles")
</head>

<body>
	<script src="{{ asset('assets') }}/dist/js/demo-theme.min.js?1692870487"></script>
	<div class="page">
		<!-- <div class="page" style="background: url('{{ asset('assets') }}/dist/img/vivid.jpg') no-repeat center center fixed; background-size: cover;"> -->
		@include("Layouts.parts.header")
		<div class="page-wrapper">
			@include("Parts::flash-message")
			<div class="mx-5">
				@yield("header")
			</div>
			<div class="mx-5">
				<x-loader />
				<div class="page-body">
					@yield("content")
				</div>
			</div>
			@include("Layouts.parts.footer")
		</div>
	</div>
	<!-- Tabler Core -->
	<script src="{{ asset('assets') }}/dist/js/tabler.min.js?1692870487" defer></script>
	<script src="{{ asset('assets') }}/dist/js/demo.min.js?1692870487" defer></script>

	<script src="https://code.jquery.com/jquery-3.7.1.min.js"
		integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

	<script src="{{ asset('assets/dist/libs/block-ui/block-ui.js') }}"></script>
	<script src="{{ asset('js/script.js?v=1') }}"></script>
	<script src="{{ asset('js/_form.js') }}"></script>
	<script src="{{ asset('js/_condition.js') }}"></script>
	<script src="{{ asset('assets') }}/plugins/datatables/jquery.dataTables.min.js"></script>
	<script src="{{ asset('assets') }}/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
	<script src="{{ asset('assets') }}/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
	<script src="{{ asset('assets') }}/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
	<script src="{{ asset('assets') }}/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
	<script src="{{ asset('assets') }}/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
	<script src="{{ asset('assets') }}/plugins/jszip/jszip.min.js"></script>
	<script src="{{ asset('assets') }}/plugins/pdfmake/pdfmake.min.js"></script>
	<script src="{{ asset('assets') }}/plugins/pdfmake/vfs_fonts.js"></script>
	<script src="{{ asset('assets') }}/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
	<script src="{{ asset('assets') }}/plugins/datatables-buttons/js/buttons.print.min.js"></script>
	<script src="{{ asset('assets') }}/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
	<script src="{{ asset('assets') }}/plugins/datatables/dataTables.checkboxes.min.js"></script>
	{{-- Select2 --}}
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
	<!-- {{-- Sweet Alert --}} -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="https://unpkg.com/nprogress@0.2.0/nprogress.js"></script>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap-switch@3.4.0/dist/js/bootstrap-switch.min.js"></script>


	<script>
		$(document).ready(function () {

			$('.dropdown').hover(
				function () {
					$(this).find('.dropdown-menu').eq(0).addClass('show');
				},
				function () {
					$(this).find('.dropdown-menu').removeClass('show');
				}
			);
		})
	</script>
	@stack("javascript")

</body>

</html>
