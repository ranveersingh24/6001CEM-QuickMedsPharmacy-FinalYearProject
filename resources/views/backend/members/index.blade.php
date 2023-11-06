@extends('layouts.admin_app')

@section('content')
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          	<h1 class="m-0"> Member Management 
            <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
             Member List
        </small>
            </h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard.dashboards.index') }}">Home</a></li>
              <li class="breadcrumb-item active">  Member List </li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

<form action="{{ route('member.members.index') }}" method="GET">
 <div class="container-fluid">
        <div class="row mb-2">
	<div class="col-sm-2">
		<div class="form-group">
			<input type="text" class="form-control" name="code" value="{{ !empty('code') && request('code') ? request('code') : '' }}" placeholder="Search Member Code">
		</div>
	</div>
	<div class="col-sm-2">
		<div class="form-group">
			<input type="text" class="form-control" name="member_name" value="{{ !empty('company_name') && request('company_name') ? request('company_name') : '' }}" placeholder="Search Member Name">
		</div>
	</div>

	<div class="col-sm-2">
		<div class="form-group">
			<select class="form-control" name="status">
				<option value="">Search Status</option>
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
				Row Per Page: <br>
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
	<button class="btn btn-primary btn-sm">
		<i class="fa fa-search"></i> Search
	</button>
	<a href="{{ route('member.members.index') }}" class="btn btn-warning btn-sm">
		<i class="fa fa-refresh"></i> Clear Search
	</a>
</div>
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Member List</h3>

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
                    <tr align="center">
                      <th>#</th>
					<th>Code</th>
					<th>Name</th>
					<th>Email</th>
					<th>Phone</th>
					<th>Status</th>
					<th>Action</th>
                    </tr>
                  </thead>
<tbody>
				@if (!$users->isEmpty())
				@foreach($users as $key => $user)
				<tr align="center">
					<td>
						{{ $key+1 }}
						<input type="hidden" class="row_id" value="{{ $user->id }}">
					</td>
					<td>{{ $user->code }}</td>
					<td>{{ $user->f_name }} {{ $user->l_name }}</td>
					<td>{{ $user->email }}</td>
					<td>{{ $user->phone }}</td>
					<td>{!! ($user->status == 1) ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>' !!}</td>
					<td>
						@if($user->status == 1)
						<a href="#" class="red change-status" data-id="2" style="color:red;">
							<i class="ace-icon fa fa-ban bigger-130"></i> Inactive
						</a>
						@else
						<a href="#" class="green change-status" data-id="1" style="color:#32CD32;">
							<i class="ace-icon fa fa-check bigger-130"></i> Reactive
						</a>
						@endif

						&nbsp;&nbsp;
						<a href="#" class="red change-status" data-id="3" style="color:red;">
							<i class="ace-icon fa fa-trash-o bigger-130"></i> Delete
						</a>

					
					</td>
				</tr>
				@endforeach
				@else
				<tr>
					<td colspan="9">No Result Found</td>
				</tr>
				@endif
			</tbody>
		</table>
				<div class="card-footer clearfix">
                <ul class="pagination pagination-sm m-0 float-right">
                {{ $users->links() }}
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
	           url: '{{ route("UserStatus") }}',
	           type: 'post',
	           data: fd,
	           contentType: false,
	           processData: false,
	           success: function(response){
	                $('.loading-gif').hide();
	                toastr.success('Status Changed');
	                window.location.href="{{ route('member.members.index') }}";
	           },
	        });
	    }else{
        	$('.loading-gif').hide();
        }
    });
</script>
@endsection