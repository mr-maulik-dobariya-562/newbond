@extends('Layouts.app')

@section('title', 'Dashboard')

@section('header')
	<div class="page-header d-print-none">
		<div class="row g-2 align-items-center">
			<div class="col-md-10">
				<div class="page-pretitle">
					Overview
				</div>
				<h2 class="page-title">
					Dashboard
				</h2>
			</div>
			<div class="col-md-2">
				<input type="month" name="date" id="date" class="form-control" value="{{ date('Y-m') }}">
			</div>
		</div>
	</div>
@endsection
