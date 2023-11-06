@extends('layouts.app')

@section('content')
<div class="profile-own-bg">
	<div class="personal-header-info">
			<div class="container">
				<div class="row">
					<div class="col-xs-4" align="left">
						<a href="{{ route('home') }}"  class="header-title">
							<i class="fa fa-chevron-left"></i> Back
						</a>
					</div>
					<div class="col-xs-4" align="left">
						<p align="center" class="header-title">My Wish List</p>
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
<div class="profile-content">
	<div class="container">
		<div class="form-group">
			<div class="row">
				<div class="col-sm-12 myOrder-list">
					<div class="form-group container-box wishlist-box">
						@if(!$favourites->isEmpty())
						@foreach($favourites as $favourite)
						@php
						$image = (!empty($favourite->image)) ? $favourite->image : 'images/no-image-available-icon-6.jpg';
						@endphp
						<div class="form-group wish-row">
							<div class="row">
								<div class="col-sm-6" align="center">
									<div class="from-group">
										<div class="row">
											<div class="col-xs-3">
												<a href="{{ route('details', [$favourite->product_name, md5($favourite->id)]) }}">
													<img src="{{ url($image) }}" style="width: 70px;">
												</a>
											</div>
											<div class="col-xs-9" align="left">
												<div class="form-group product-details">
													<a href="{{ route('details', [$favourite->product_name, md5($favourite->id)]) }}">
														{{ $favourite->product_name }}
													</a>
													<br>
													@if(!empty($favourite->special_price))
													RM {{ $favourite->special_price }}<br>
													<small><strike>RM {{ $favourite->price }}</strike></small>
													@else
													RM {{ number_format($favourite->price, 2) }}<br>
													@endif
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-6">
									@if($stockBalance[$favourite->id] == 0)
									<span class="important-text">Not Available</span>
									<a href="#" class="btn btn-outline-danger btn-sm remove-wish-list pull-right" data-id="{{ $favourite->id }}">
										<i class="fa fa-trash"></i>
									</a>
									@else
									<div class="from-group" align="right">
										<a href="#" class="btn btn-outline-primary add-to-cart-button btn-sm" data-id="{{ $favourite->id }}">
											<i class="fa fa-shopping-cart"></i> 
										</a>
										&nbsp;&nbsp;
										<a href="#" class="btn btn-outline-danger btn-sm remove-wish-list pull-right" data-id="{{ $favourite->id }}" >
											<i class="fa fa-trash"></i>
										</a>
									</div>
									@endif
								</div>
							</div>
						</div>
						<hr>
						@endforeach
						@else
						<div class="form-group" align="center">
							There are no favorites yet 
						</div>
						<div class="form-group" align="center">
							Add your favorites to wishlist and they will show here
						</div>
						<div class="form-group" align="center">
							<a href="{{ route('home') }}" class="btn btn-primary">
								Continue shopping
							</a>
						</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection


@section('js')
<script type="text/javascript">
$('.add-to-cart-button').click( function(e){
  	e.preventDefault();
  	$('.loading-gif').show();
  	var auth_check = '{{ Auth::check() }}';
  	
  	if(auth_check){
	  	var fd = new FormData();
	  	fd.append('product_id', $(this).data('id'));
	  	fd.append('quantity', '1');

	  	$.ajax({
	        url: '{{ route("AddToCart") }}',
	        type: 'post',
	        data: fd,
	        contentType: false,
	        processData: false,
	        success: function(response){
	        	// alert(response);
	        	// return false;
	        	$('.loading-gif').hide();
	        	if(response == 'quantity error'){
	        		toastr.error('Please Add Quantity At least 1');
	        		return false;
	        	}

	        	if(response == 'quantity exceed error'){
	        		toastr.error('Product Balance Quantity Not Enough');
	        		return false;
	        	}


	        	if(response == 'ok'){
	        		$.ajax({
				        url: '{{ route("CountCart") }}',
				        type: 'get',
				        success: function(response){
				        	$('.badge-cart').html(response);
				        	
				        }
				    });
	            	toastr.success('Items Add To Cart. <a href="{{ route("checkout") }}" class="view-cart-button pull-right"><i class="fa fa-shopping-cart"></i> View Cart</a>');
	            }else{
	            	toastr.error('Error Please Contact Admin');
	            }
	        },
	    });
  	}else{
  		window.location.href = "{{ route('login') }}";
  	}
});

$('.remove-wish-list').click( function(e){
	e.preventDefault();
  	
  	var ele = $(this);
	var auth_check = '{{ Auth::check() }}';
	  	
	if(auth_check){
  		var fd = new FormData();
	  	fd.append('product_id', ele.data('id'));
	  	if(confirm('Product will be removed from wish list'))
	  	$('.loading-gif').show();
	  	$.ajax({
	        url: '{{ route("remove_wish") }}',
	        type: 'post',
	        data: fd,
	        contentType: false,
	        processData: false,
	        success: function(response){
	        	$('.loading-gif').hide();
		        ele.closest('.wish-row').remove();
		        toastr.info('Product has been removed from wish list');
		        if($('.wish-row').length == 0){
		        	$('.wishlist-box').html('<div class="form-group" align="center">\
												There are no favorites yet.\
											</div>\
											<div class="form-group" align="center">\
												Add your favorites to wishlist and they will show here.\
											</div>\
											<div class="form-group" align="center">\
												<a href="{{ route("home") }}" class="btn btn-primary">\
													Continue Shopping\
												</a>\
											</div>');
		        }
	        }
	    });
  	}else{
  		window.location.href = "{{ route('login') }}";
  	}
});
</script>
@endsection