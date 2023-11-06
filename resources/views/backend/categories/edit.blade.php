@extends('layouts.admin_app')

@section('content')
<div class="page-header">
    <h1>
       Condition Details
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            {{ $category->category_name }}
        </small>
    </h1>
</div>
@if($errors->any())
  <div class="alert alert-danger">{!! implode('<br/>', $errors->all(':message')) !!}</div>
@endif
<form method="POST" action="{{ route('category.categories.update', $category->id) }}" id="categories-form" enctype="multipart/form-data">
@csrf
@method('PUT')
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
                Save Changes
            </i>
        </button>

    </div>
</div>

@endsection

@section('js')
<script type="text/javascript">
    var url = '{{ route("LoadCategoryImage", ":id") }}';
    url = url.replace(':id', '{{ $category->id }}');
    
    $.ajax({
        url: url,
        type: 'get',
        success: function(response){
            $('.category-image-list .row').html(response);
            
        },
    });

	$('.submit-form-btn .btn-primary').click( function(e){
    	e.preventDefault();
    	
    	$('#categories-form').submit();
    });
</script>
@endsection