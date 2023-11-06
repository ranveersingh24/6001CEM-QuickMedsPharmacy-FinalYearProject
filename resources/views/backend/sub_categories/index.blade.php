@extends('layouts.admin_app')

@section('content')
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          	<h1 class="m-0">Sub Category
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
             Sub Category List
        </small>
    </h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard.dashboards.index') }}">Home</a></li>
              <li class="breadcrumb-item active">    Sub Category List </li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

<form action="{{ route('sub_category.sub_categories.index') }}" method="GET">
 <div class="container-fluid">
   <div class="row mb-2">
	<div class="col-sm-2">
		<div class="form-group">
			<input type="text" class="form-control" name="category_name" value="{{ !empty('category_name') && request('category_name') ? request('category_name') : '' }}" placeholder="Search Category Name..">
		</div>
	</div>

	<div class="col-sm-2">
		<div class="form-group">
			<input type="text" class="form-control" name="sub_category_name" value="{{ !empty('sub_category_name') && request('sub_category_name') ? request('sub_category_name') : '' }}" placeholder="Search Sub Category Name..">
		</div>
	</div>

	<div class="col-sm-2">
		<div class="form-group">
			<select class="form-control" name="status">
				<option value="">Select Status</option>
				<option {{ (!empty(request('status')) && request('status') == '1') ? 'selected' : '' }} value="1">Active</option>
				<option {{ (!empty(request('status')) && request('status') == '2') ? 'selected' : '' }} value="2">Inactive</option>
			</select>
		</div>
	</div>
</div>

<div class="form-group">
	<div class="row">
		<div class="col-sm-2">
			<div class="form-group">
				Item Per Page: <br>
				<select class="input-small" name="per_page">
					<option value="10">10</option>
					<option value="20">20</option>
					<option value="50">50</option>
				</select>
			</div>
		</div>
	</div>
</div>

<div class="form-group">
	<div class="row">
		<div class="col-sm-4">
			<div class="form-group">
				<button class="btn btn-primary btn-sm">
					<i class="fa fa-search"></i> Search
				</button>
				<a href="{{ route('sub_category.sub_categories.index') }}" class="btn btn-warning btn-sm">
					<i class="fa fa-refresh"></i> Clear Search
				</a>
			</div>
		</div>
	</div>
</div>
<div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Product List</h3>

           <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap table-bordered">
                  <thead>
                  	<tbody>
                    <tr>
                  <th>#</th>
					<th>Code</th>
					<th>Category</th>
					<th>Name</th>
					<th>Status</th>
					<th>Action</th>
                    </tr>
                  </thead>
<tbody>
				@if (!$sub_categories->isEmpty())
				@foreach($sub_categories as $key => $sub_category)
				<tr>
					<td>
						<input type="hidden" class="row_id" value="{{ $sub_category->id }}">
						{{ $key+1 }}
					</td>
					<td>{{ $sub_category->sub_category_code }}</td>
					<td>{{ $sub_category->category_name }}</td>
					<td>{{ $sub_category->sub_category_name }}</td>
					<td>{!! ($sub_category->status ==1) ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>' !!}</td>
					<td>
						<a href="{{ route('sub_category.sub_categories.edit', $sub_category->id) }}">
							<i class="ace-icon fa fa-pencil bigger-130"></i> Edit
						</a>
						&nbsp;&nbsp;
						@if($sub_category->status == 1)
						<a href="#" class="red change-status" data-id="2"style="color:red;">
							<i class="ace-icon fa fa-ban bigger-130"></i> Inactive
						</a>
						@else
						<a href="#" class="green change-status" data-id="1"style="color:#32CD32;">
							<i class="ace-icon fa fa-check bigger-130"></i> Reactive
						</a>
						@endif

						&nbsp;&nbsp;
						<a href="#" class="red change-status" data-id="3"style="color:red;">
							<i class="ace-icon fa fa-trash-o bigger-130"></i> Delete
						</a>
					</td>
				</tr>
				@endforeach
				@else
				<tr>
					<td colspan="6">No Result Found</td>
				</tr>
				@endif
			</tbody>
		</table>
			<div class="card-footer clearfix">
                <ul class="pagination pagination-sm m-0 float-right">
                  {{ $sub_categories->links() }}
                </ul>
              </div>
                     </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>
        <!-- /.row -->
</form>
@endsection

@section('js')
<script type="text/javascript">
	$('.change-status').click(function(){
        $('.loading-gif').show();
        var ele = $(this);
        var row_id = ele.closest('tr').find('.row_id').val();
        

        var fd = new FormData();
        fd.append('row_id', row_id);
        fd.append('status', ele.data('id'));
        fd.append('_token', '{{ csrf_token() }}');

        var message;
        if(ele.data('id') == 1){
        	message = confirm("Reactive this row?");
        }else if(ele.data('id') == 2){
        	message = confirm("Inactive this row?");
        }else{
        	message = confirm("Delete this row?");
        }

        if(message == true){
	        $.ajax({
	           url: '{{ route("SubCategoryStatus") }}',
	           type: 'post',
	           data: fd,
	           contentType: false,
	           processData: false,
	           success: function(response){
	                $('.loading-gif').hide();
	                toastr.success('Status Changed');
	                window.location.href="{{ route('sub_category.sub_categories.index') }}";
	           },
	        });
	    }else{
	    	$('.loading-gif').hide();
	    }
    });
</script>
@endsection