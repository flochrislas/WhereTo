@extends('layouts.app')

@section('content')

	<div class="container">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title" style="padding:12px 0px;font-size:25px;">
				<strong>Restaurants Import/Export</strong>
				</h3>
			</div>
			<div class="panel-body">
				@if ($message = Session::get('success'))
					<div class="alert alert-success" role="alert">
						{{ Session::get('success') }}
					</div>
				@endif
			
				@if ($message = Session::get('error'))
					<div class="alert alert-danger" role="alert">
						{{ Session::get('error') }}
					</div>
				@endif
			
				<h3>Import Restaurants from File</h3>
				<form style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 20px;" 
						action="{{ URL::to('restaurants/import') }}" 
						class="form-horizontal" 
						method="post" 
						enctype="multipart/form-data">
    		
    				<input type="file" name="imported-file" />
    				{{ csrf_field() }}
    				<br/>
    				<button class="btn btn-primary">Import CSV or Excel File</button>
				</form>
				<br/>	 
				<h3>Export your Restaurants</h3>
				<div style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 20px;">
					<a href="{{ url('restaurants/exportType/xls') }}"><button class="btn btn-success btn-lg">Download Excel xls</button></a>
					<a href="{{ url('restaurants/exportType/xlsx') }}"><button class="btn btn-success btn-lg">Download Excel xlsx</button></a>
					<a href="{{ url('restaurants/exportType/csv') }}"><button class="btn btn-success btn-lg">Download CSV</button></a>
				</div>
				
				<div style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 20px;">
					<a href="{{ url('restaurants/export') }}"><button class="btn btn-success btn-lg">Download Excel xls</button></a>
				</div>
				
			</div>
		</div>
	</div>

@endsection	