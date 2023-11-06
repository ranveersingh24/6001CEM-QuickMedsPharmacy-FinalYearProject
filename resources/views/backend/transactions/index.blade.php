@extends('layouts.admin_app')

@section('content')
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          	  <h1 class="m-0">Transaction Management
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
           Transaction List
        </small>
             </h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="{{ route('dashboard.dashboards.index') }}">Home</a></li>
              <li class="breadcrumb-item active">        Transaction List </li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

<form action="{{ route('transaction.transactions.index') }}" method="GET">
	<div class="container-fluid">
<div class="row">
	<div class="col-sm-2">
		<div class="form-group">
			<input type="text" class="form-control" name="dates" value="{{ !empty(request('dates')) ? request('dates') : $startDate.' - '.$endDate }}">
		</div>
	</div>
	<div class="col-sm-2">
		<div class="form-group">
			<input type="text" class="form-control" name="transaction_no" value="{{ !empty(request('transaction_no')) ? request('transaction_no') : '' }}" placeholder="Search Transaction No..">
		</div>
	</div>

	<div class="col-sm-2">
			<div class="form-group">
				<select class="form-control" name="status">
					<option value="">
						Select Status
					</option>
					<option {{ (!empty(request('status')) && request('status') == '1') ? 'selected' : '' }} value="1">
						Paid
					</option>
					<option {{ (!empty(request('status')) && request('status') == '98') ? 'selected' : '' }} value="98"> 
						Waiting Verification
					</option>
					<option {{ (!empty(request('status')) && request('status') == '96') ? 'selected' : '' }} value="96">
						Rejected
					</option>
					<option {{ (!empty(request('status')) && request('status') == '99') ? 'selected' : '' }} value="99">
						Unpaid
					</option>
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
					<option {{ (!empty(request('per_page')) && request('per_page') == '10') ? 'selected' : '' }} value="10">10</option>
					<option {{ (!empty(request('per_page')) && request('per_page') == '20') ? 'selected' : '' }} value="20">20</option>
					<option {{ (!empty(request('per_page')) && request('per_page') == '50') ? 'selected' : '' }} value="50">50</option>
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
					<i class="fa fa-search"></i>
					Search
				</button>
				<a href="{{ route('transaction.transactions.index') }}" class="btn btn-warning btn-sm">
					<i class="fa fa-refresh"></i>
					Clear Search
				</a>
			</div>
		</div>
	</div>
</div>
</form>
<hr>

<div class="col-12">	
<div class="row">
	<div class="col-xs-12">
		<table class="table table-bordered">
			<thead>
				<tr class="info">
					<th>#</th>
					<th>
						Transaction No
					</th>
					<th>
						Customer
					</th>
					<th>
					Role
				   </th>
					<th>
					Payment Method
				    </th>
					<th>
					Quantity
				    </th>
					<th>
					Net Amount
				    </th>
					<th>
					Shipping Fee
				    </th>
					<th>
					Processing Fee
				   </th>
					<th>
					Total Amount (RM)
					</th>
					<th>
						Status
					</th>
					<th>
					Created
					</th>
					<th></th>
					<!-- <th>Action</th> -->
				</tr>
			</thead>
			<tbody>
				@php
				$totalTransaction = 0;
				@endphp
				@if(!$transactions->isEmpty())
				@foreach($transactions as $key => $transaction)
				<tr>
					<td>{{ $key+1 }}
						<input type="hidden" name="tid" class="tid" value="{{ $transaction->Tid }}">
					</td>
					<td>{{ $transaction->transaction_no }}</td>
					<td>{{ !empty($transaction->customer_name) ? $transaction->customer_name : $transaction->address_name }}</td>
					<td>
						
						@if(!empty($transaction->Acode))
							Agent
						@elseif(!empty($transaction->Ccode))
							Customer
						@elseif(!empty($transaction->ADcode))
							Admin
						@else
							Guest
						@endif
					</td>
					<td>

						@if($transaction->mall == 1)
			                <b>Point</b>
			            @else
			            	<b>
			            		@if($transaction->online_payment_method == 'CC')
			            			Credit Card / Debit Card
			            		@elseif($transaction->online_payment_method == 'DD')
			            			Online Transfer
			            		@elseif($transaction->online_payment_method == 'WA')
			            			E-Wallet
			            		@else
			            			@if($transaction->status == 99)
			            				<i class="fa fa-minus"></i>
			            			@else
			            				Bank Transfer
			            			@endif
			            		@endif
		            		</b>
			            @endif
					</td>
					<!-- <td>{{ $transaction->product_name }}</td>
					<td>{{ !empty($transaction->sub_category) ? $transaction->sub_category."°" : '-' }}</td>
					<td>{{ $transaction->unit_price }}</td> -->
					<td>{{ $transaction->quantity }}</td>
					<td>
						@if($transaction->mall == 1)
							{{ number_format($transaction->grand_total - $transaction->shipping_fee - $transaction->processing_fee, 2) }} Point
						@else
							RM {{ number_format($transaction->grand_total - $transaction->shipping_fee - $transaction->processing_fee, 2) }}
						@endif
					</td>
					<td>
						@if($transaction->mall == 1)
							<i class="fa fa-minus"></i>
						@else
							RM {{ number_format($transaction->shipping_fee, 2) }}
						@endif
					</td>
					<td>
						@if($transaction->mall == 1)
							<i class="fa fa-minus"></i>
						@else
							RM {{ number_format($transaction->processing_fee, 2) }}
						@endif
					</td>
					<td>
						@if($transaction->mall == 1)
							{{ number_format($transaction->grand_total, 2) }} Point
						@else
							RM {{ number_format($transaction->grand_total, 2) }}
						@endif
					</td>
					<td>
						@if($transaction->status == 99)
							<span class="badge badge-pill badge-warning">Unpaid</span>
						@elseif($transaction->status == 98)
							<span class="badge badge-pill badge-info">Waiting Verification</span>
						@elseif($transaction->status == 97)
							<span class="badge badge-pill badge-info">In-progress</span>
						@elseif($transaction->status == '96')
							<span class="badge badge-danger">Rejected</span>
						@elseif($transaction->status == 1)
							@if($transaction->completed == 1)
								<span class="badge badge-success">Delivered</span>
							@else
								<span class="badge badge-success">Paid</span>
							@endif
						@else
							<span class="badge badge-pill badge-danger">Cancelled</span>
						@endif
					</td>
					<td>{{ ($transaction->created_at) }}</td>
					<td>
						<a href="{{ route('transaction.transactions.edit', $transaction->Tid) }}">
							<i class="fa fa-eye"></i> View
						</a>
						&nbsp;&nbsp;
						@if($transaction->status == '1' && $transaction->completed != '1')
						<div class="btn-group">
						  <button type="button" class="btn btn-default btn-sm  dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						    Action <span class="caret"></span>
						  </button>

							  <ul class="dropdown-menu" role="menu">
							  		<li><a href="#" class="change_action" data-id="11">Completed</a></li>
							  </ul>

						  
						</div>
						@endif
						@if($transaction->status == '98')
						<div class="btn-group">
						  <button type="button" class="btn btn-default btn-sm  dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						    Action<span class="caret"></span>
						  </button>

						  <ul class="dropdown-menu" role="menu">
								    <li><a href="#" class="change_action" data-id="1">Approve</a></li>
								    <li><a href="#" class="change_action" data-id="96">Reject</a></li>
							</ul>
						</div>
						  	
						@endif
						&nbsp;&nbsp;
						<a href="#" class="change_action important-text" data-id="95">
							<i class='fa fa-trash-o'></i>Cancel
						</a>

						
					</td>
				</tr>
				@php
				if($transaction->status == '1'){
					$totalTransaction += $transaction->grand_total;
				}
				@endphp
				@endforeach
				@else
				<tr>
					<td colspan="10">
						No Result Found
					</td>
				</tr>
				@endif
			</tbody>
		</table>
		{{ $transactions->links() }}
	</div>
</div>
</div>
</div>
@endsection

@section('js')
<script type="text/javascript">
	$('.change_action').click( function(){

		$('.loading-gif').show();

		var ele = $(this);
		var action_id = $(this).data('id');
		var tid = $(this).closest('tr').find('input[name="tid"]').val();
		var fd = new FormData();
		fd.append('action_id', action_id);
		fd.append('tid', tid);
		fd.append('_token', '{{ csrf_token() }}');

		if(action_id == '1'){
			var confirmMessage = confirm('Complete This Transaction?');
		}else if(action_id == '95'){
			var confirmMessage = confirm('Cancel This Transaction? ');
		}else if(action_id == '96'){
			var confirmMessage = confirm('Reject This Transaction?');
		}else if(action_id == '11'){
			var confirmMessage = confirm('Delivered?');
		}


		if(confirmMessage == true){
			$.ajax({
		       url: '{{ route("change_transaction_action") }}',
		       type: 'post',
		       data: fd,
		       contentType: false,
		       processData: false,
		       success: function(response){
		       		$('.loading-gif').hide();
		       		// alert(response);
		       		// alert(response);
		       		// return false;
		       		toastr.success('Update Successful');
		       		window.location.href = "{{ route('transaction.transactions.index') }}";
		       		if(action_id == '1'){
		       			ele.closest('tr').find('.status_id').html('<span class="badge badge-success">Approved</span>');
		       		}else if(action_id == '98'){
		       			ele.closest('tr').find('.status_id').html('<span class="badge badge-danger">Rejected</span>');
		       		}else{
		       			ele.closest('tr').find('.status_id').html('<span class="badge badge-danger">Cancelled</span>');
		       		}
		       },
		    });			
		}else{
			$('.loading-gif').hide();
		}
	});
</script>


@endsection