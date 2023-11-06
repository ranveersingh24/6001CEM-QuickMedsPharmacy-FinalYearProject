@extends('layouts.app')

@section('content')
<div class="profile-own-bg">
	<div class="personal-header-info">
			<div class="container">
				<div class="row">
					<div class="col-xs-4" align="left">
						<a href="{{ route('home') }}">
							<p  style="color: white;"><i class="fa fa-chevron-left"></i> Back</p>
						</a>
					</div>
					<div class="col-xs-4" align="left">
						<p align="center" class="header-title">Address book</p>
					</div>
					<div class="col-xs-4" align="right">
						<a style="color:white" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
							 Logout &nbsp;
							<i class="fa fa-sign-out" aria-hidden="true"></i>
						</a>
						<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
		                @csrf
		            </form>
		
					</div>
				</div>
			</div>

		<div class="container">
			<div class="form-group">
				<div class="row">
					<div class="col-xs-2">
						<a href="{{ route('my_setting') }}">
							@if(!empty(Auth::user()->profile_logo))
								
								<div style="background-image: url({{ url(Auth::user()->profile_logo) }}); width: 50px; height: 50px; border-radius: 100%; background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
							@else
								<img src="{{ url('images/images.png') }}" width="50" class="profile-logo">
							@endif							
						</a>
					</div>
					<div class="col-xs-6">
					</div>
					
				</div>
			</div>

		

		</div>
	</div>
</div>
<div class="form-group">
	<div class="container">
		<div class="profile-content">
			<div class="form-group">
				<a href="{{ route('AddressBook.AddressBook.create') }}" class="btn btn-primary btn-sm">
					<i class="fa fa-plus"></i> &nbsp;&nbsp; Add new address
				</a>
			</div>
		</div>
		<div class="container-box">
		@if(!$address_book->isEmpty())
			@foreach($address_book as $key => $address)
				<div class="form-group" style="position: relative;">
					<a href="{{ route('AddressBook.AddressBook.edit', md5($address->id)) }}" class="btn btn-sm btn-primary" style="position: absolute; right: 5px; z-index: 10;">
						Edit address
					</a>
					<div class="row">
						<div class="col-sm-2">
							<div class="form-group">
								<i class="fa fa-user"></i>&nbsp; {{ $address->f_name }} {{ $address->l_name }}
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<i class="fa fa-phone" aria-hidden="true"></i>&nbsp; {{ $address->phone }}
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								
								<p>
									<i class="fa fa-map-marker" aria-hidden="true"></i>&nbsp; 
									{{ $address->address }},
								</p>
								<p>&nbsp; &nbsp; {{ $address->postcode }} {{ $address->city }}</p> 
								<p>&nbsp; &nbsp; {{ $address->state }}</p>
							</div>
						</div>


						<div class="col-sm-2">
							<div class="form-group default-box">
								@if($address->default == 1)
									&nbsp; &nbsp;<span class="badge badge-success">Default</span>
								@else
									&nbsp; &nbsp;<button class="btn btn-warning btn-sm default" data-id="{{ md5($address->id) }}">Set as default address
									</button>
								@endif
							</div>
						</div>
						<!-- <div class="col-sm-2">
							<div class="form-group">
								<input type="radio" name="default" data-id="{{ md5($address->id) }}" {{ ($address->default == 1) ? 'checked' : '' }}>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<a href="{{ route('AddressBook.AddressBook.edit', md5($address->id)) }}" class="action-btn">
									<i class="fa fa-pencil"></i>
								</a>
								@if($count > 1)
								&nbsp;&nbsp;
								<a href="#" class="action-btn delete-address-btn non-load red" data-id="{{ md5($address->id) }}">
									<i class="fa fa-trash"></i>
								</a>
								@endif
							</div>
						</div> -->

					</div>
				</div>
				<hr>
			@endforeach
		@else
			<p align="center">No Address</p>
		@endif
		</div>
	</div>
</div>
@endsection

@section('js')
<script type="text/javascript">
	$('input[name="default"]').click( function(e){
		
		$('.loading-gif').show();
		var ele = $(this);
		var value = ele.data('id');


		var fd = new FormData();
		fd.append('address_id', value);

		$.ajax({
	       url: '{{ route("changeDefaultAddress") }}',
	       type: 'post',
	       data: fd,
	       contentType: false,
	       processData: false,
	       success: function(response){
	       		toastr.success('Update Successfully');
	       		$('.loading-gif').hide();
	       },
	    });
	});

	$('.delete-address-btn').click( function(e){
		e.preventDefault();
		var ele = $(this);
		var value = ele.data('id');
		var $confirm;
		if(ele.closest('tr').find('input[name="default"]').prop("checked")){
			$confirm = confirm("This Address Information is your Default Address, Remove? \n *If Yes, Please reselect your Default Address*");
		}else{
			$confirm = confirm("Address will be removed from Address Book");
		}
		
		var fd = new FormData();
		fd.append('address_id', value);
		if($confirm == true){
			$('.loading-gif').show();
			$.ajax({
		       url: '{{ route("deleteAddress") }}',
		       type: 'post',
		       data: fd,
		       contentType: false,
		       processData: false,
		       success: function(response){
		       		toastr.info('Address Row Deleted');
		       		ele.closest('tr').remove();
		       		$('.loading-gif').hide();
		       		if($('#shipping-row tr').length == 1){
		       			$('.delete-address-btn').remove();
		       		}
		       },
		    });
		}else{

		}
	});

	$('.row .default-box').on('click', '.default', function(e){
		
		$('.loading-gif').show();
		var ele = $(this);
		var value = ele.data('id');


		var fd = new FormData();
		fd.append('address_id', value);
		
		$.ajax({
	       url: '{{ route("changeDefaultAddress") }}',
	       type: 'post',
	       data: fd,
	       contentType: false,
	       processData: false,
	       success: function(response){
	       		toastr.success('Setting successfully');

	       		$('.loading-gif').hide();
	    		location.reload();   		
	       },
	    });
	});
</script>
@endsection
