@extends('layouts.app')

@section('content')
<div class="profile-own-bg">
	<div class="personal-header-info">
			<div class="container">
				<div class="row">
					<div class="col-xs-4" align="left">
						<a href="{{ route('home') }}">
							<p style="color: white;"><i class="fa fa-chevron-left">&nbsp;&nbsp;</i>Home</p>
						</a>
					</div>
					<div class="col-xs-4" align="left">
						<p align="center" class="header-title">My Account</p>
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
						<a href="{{ route('profile') }}">
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

<div class="profile-content">
	<div class="container">
		<div class="form-group container-box">
			<div class="row">
				<div class="col-xs-6" align="left">
					<b>My Orders</b>
				</div>
				<div class="col-xs-6" align="right">
					<a href="{{ route('pending_shipping') }}">
						<small>View all orders<i class="fa fa-angle-right" aria-hidden="true"></i></small>
					</a>
				</div>
			</div>
			<br>
			<div class="row">

				<div class="col-xs-3" align="center">
					<a href="{{ route('pending_shipping') }}" style="position: relative;">
						@if($countToShip > 0)
						<span class="badge badge-pill badge-danger" style="position: absolute; right: -10px; top: -10px;">
							{{ $countToShip }}
						</span>
						@endif
						<img src="{{ url('images/profile/shipment_pending_1017207.png') }}" width="30">
						<br>
						<span class="profile-word">To Deliver 
					</span>
					</a>
				</div>

				<div class="col-xs-3" align="center">
					<a href="{{ route('pending_receive') }}" style="position: relative;">
						@if($countToReceive > 0)
						<span class="badge badge-pill badge-danger" style="position: absolute; right: -10px; top: -10px;">
							{{ $countToReceive }}
						</span>
						@endif
						<img src="{{ url('images/profile/Pending-Truck-Delivery-Commerce-Logistic-Transportation-512.png') }}" width="30">
						<br>
						<span class="profile-word">To Receive
						</span>
					</a>
				</div>

				<div class="col-xs-3" align="center">
					<a href="{{ route('completed_order') }}" style="position: relative;">
						@if($countCompleted > 0)
						<span class="badge badge-pill badge-danger" style="position: absolute; right: -10px; top: -10px;">
							{{ $countCompleted }}
						</span>
						@endif
						<img src="{{ url('images/profile/Box_Package_Delivery_Shipping_Complete_Check_Done-512.png') }}" width="30">
						<br>
						<span class="profile-word"> Order Completed
						</span>
					</a>
				</div>

					<div class="col-xs-2" align="center">
					<a href="{{ route('cancelled_order') }}" style="position: relative;">
						@if($countCancelled > 0)
						<span class="badge badge-pill badge-danger" style="position: absolute; right: -10px; top: -10px;">
							{{ $countCancelled }}
						</span>
						@endif
						<img src="{{ url('images/profile/online_shop_ecommerce_shopping-46-512.png') }}" width="30">
						<br>
						<span class="profile-word"> Order Cancelled</span>
					</a>
				</div>
			</div>
		</div>

		
	</div>
</div>
@endsection