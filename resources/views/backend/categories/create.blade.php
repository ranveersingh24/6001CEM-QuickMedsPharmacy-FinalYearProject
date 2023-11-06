@extends('layouts.admin_app')

@section('content')
<div class="page-header">
    <h1>
        Create New Condition
    </h1>
</div>
@if($errors->any())
  <div class="alert alert-danger">{!! implode('<br/>', $errors->all(':message')) !!}</div>
@endif
<form method="POST" action="{{ route('category.categories.store') }}" id="categories-form" enctype="multipart/form-data">
@csrf
@include('backend.categories.form')
</form>


<div class="submit-form-btn">
    <div class="form-group wizard-actions" align="right">
        <a href="{{ route('category.categories.index') }}" class="btn btn-default">
            <i class="fa fa-ban"> 
                Cancel
            </i>
        </a>

        <button class="btn btn-primary">
            <i class="fa fa-check"> 
               Create
            </i>
        </button>

    </div>
</div>

@endsection

@section('js')
<script type="text/javascript">
	$('.submit-form-btn .btn-primary').click( function(e){
    	e.preventDefault();
    	
    	$('#categories-form').submit();
    });
</script>
@endsection