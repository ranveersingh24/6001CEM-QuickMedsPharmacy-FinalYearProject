@extends('layouts.admin_app')

@section('content')
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          	  <h1 class="m-0">Varieties Management
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
           Varieties List
        </small>
             </h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="{{ route('dashboard.dashboards.index') }}">Home</a></li>
              <li class="breadcrumb-item active">        Varieties List </li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

<form action="{{ route('category.categories.index') }}" method="GET">
 <div class="container-fluid">
   <div class="row mb-2">
	<div class="col-sm-2">
		<div class="form-group">
			<input type="text" class="form-control" name="brand_name" value="{{ !empty('brand_name') && request('brand_name') ? request('brand_name') : '' }}" placeholder="Search Brand Name..">
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
				<a href="{{ route('brand.brands.index') }}" class="btn btn-warning btn-sm">
					<i class="fa fa-refresh"></i> Clear Search
				</a>
			</div>
		</div>
	</div>
</div>
<div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Brand List</h3>

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
					<th>Name</th>
					<th>Status</th>
					<th>Action</th>
                    </tr>
                  </thead>
<tbody>
				@if (!$brands->isEmpty())
				@foreach($brands as $key => $brand)
				<tr>
					<td>{{ $key+1 }}
						<input type="hidden" class="row_id" value="{{ $brand->id }}">
					</td>
					<td>{{ $brand->brand_name }}</td>
					<td>{!! ($brand->status == 1) ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>' !!}</td>
					<td>
						<a href="{{ route('brand.brands.edit', $brand->id) }}">
							<i class="ace-icon fa fa-pencil bigger-130"></i> Edit
						</a>
						&nbsp;&nbsp;
						@if($brand->status == 1)
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
					<td colspan="4">No Result Found</td>
				</tr>
				@endif
			</tbody>
		</table>
		{{ $brands->links() }}
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

        $.ajax({
           url: '{{ route("BrandStatus") }}',
           type: 'post',
           data: fd,
           contentType: false,
           processData: false,
           success: function(response){
                $('.loading-gif').hide();
                toastr.success('Status Changed');
                window.location.href="{{ route('brand.brands.index') }}";
           },
        });
    });
</script>
@endsection