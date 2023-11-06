@extends('layouts.app')

@section('content')
<div class="profile-own-bg">
	<div class="personal-header-info">
			<div class="container">
				<div class="row">
					<div class="col-xs-4" align="left">
						<a href="{{ route('AddressBook.AddressBook.index') }}">
							<p style="color: white;"><i class="fa fa-chevron-left"></i> Back</p>
						</a>
					</div>
					<div class="col-xs-4" align="left">
						<p align="center" class="header-title">Address details</p>
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
								<!-- <img src="{{ url(Auth::user()->profile_logo) }}" width="50" class="profile-logo"> -->
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

<div class="container">
	<div class="profile-content">
		<div class="container-box mb-80">
			<div class="form-group">
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<h4>Address Book Details</h4>
						</div>
						<form method="POST" action="{{ route('AddressBook.AddressBook.update', $address->id) }}" id="new-address-form">
						@method('PUT')
						@csrf
							@include('frontend.address_book_form')
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('js')
<script type="text/javascript">
	$('#new-address-form .required-feild').change( function(){
    	if($(this).val()){
    		$(this).removeClass('required-feild-error');
    	}
    });

	$('.submit-address').click( function(e){
		e.preventDefault();
		var empty_fill;
	    $('#new-address-form .required-feild').each( function(){
	    	if(!$(this).val()){
	    		$(this).addClass('required-feild-error');
	    		empty_fill = 1;
	    	}
	    });
	    if(empty_fill == 1){
	    	$('#error-message').html('Please Fill In All Required Field.');
	    	return false;
	    }

	    $('#new-address-form').submit();
	});
</script>
@endsection