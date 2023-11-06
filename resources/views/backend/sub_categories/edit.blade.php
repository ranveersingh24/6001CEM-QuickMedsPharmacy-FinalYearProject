@extends('layouts.admin_app')

@section('content')
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Sub Category Details
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            {{ $sub_category->sub_category_name }}
        </small>
    </h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard.dashboards.index') }}">Home</a></li>
              <li class="breadcrumb-item active">        Sub Category Details </li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

@if($errors->any())
  <div class="alert alert-danger">{!! implode('<br/>', $errors->all(':message')) !!}</div>
@endif
<form method="POST" action="{{ route('sub_category.sub_categories.update', $sub_category->id) }}" id="sub-categories-form">
@csrf
@method('PUT')
@include('backend.sub_categories.form')
</form>

<div class="submit-form-btn">
	<div class="form-group wizard-actions" align="right">
		<a href="{{ route('sub_category.sub_categories.index') }}" class="btn btn-default">
			<i class="fa fa-ban"> CANCEL</i>
		</a>

		<button class="btn btn-primary">
			<i class="fa fa-check"> SAVE CHANGES</i>
		</button>

	</div>
</div>

@endsection

@section('js')
<script type="text/javascript">
	$('.submit-form-btn .btn-primary').click( function(e){
    	e.preventDefault();
    	
    	$('#sub-categories-form').submit();
    });
</script>
@endsection