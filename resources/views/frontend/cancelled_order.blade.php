@extends('layouts.app')
@section('css')
<style type="text/css">
	.col-2{
		-ms-flex: 0 0 16.666667%;
	    flex: 0 0 20%;
	    max-width: 20%;
	}
</style>
@endsection
@section('content')
<div class="profile-own-bg">
	<div class="personal-header-info">
			<div class="container">
				<div class="row">
					<div class="col-xs-4" align="left">
						<a href="{{ route('profile') }}">
							<p style="color: white;"><i class="fa fa-chevron-left"></i> Back</p>
						</a>
					</div>
					<div class="col-xs-4" align="left">
						<p align="center" class="header-title">My Order</p>
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
		<div class="form-group container-box ">
			<div class="row justify-content-center">
			

				<div class="col-xs-2" align="center">
					<a href="{{ route('pending_shipping') }}" style="position: relative;">
						@if($countToShip > 0)
						<span class="badge badge-pill badge-danger" style="position: absolute; right: -10px; top: -10px;">
							{{ $countToShip }}
						</span>
						@endif
						<img src="{{ url('images/profile/shipment_pending_1017207.png') }}" width="30">
						<br>
						<span class="profile-word">To Deliver</span>
					</a>
				</div>

				<div class="col-xs-2" align="center">
					<a href="{{ route('pending_receive') }}" style="position: relative;">
						@if($countToReceive > 0)
						<span class="badge badge-pill badge-danger" style="position: absolute; right: -10px; top: -10px;">
							{{ $countToReceive }}
						</span>
						@endif
						<img src="{{ url('images/profile/Pending-Truck-Delivery-Commerce-Logistic-Transportation-512.png') }}" width="30">
						<br>
						<span class="profile-word">To Receive</span>
					</a>
				</div>

				<div class="col-xs-2" align="center">
					<a href="{{ route('completed_order') }}" style="position: relative;">
						@if($countCompleted > 0)
						<span class="badge badge-pill badge-danger" style="position: absolute; right: -10px; top: -10px;">
							{{ $countCompleted }}
						</span>
						@endif
						<img src="{{ url('images/profile/Box_Package_Delivery_Shipping_Complete_Check_Done-512.png') }}" width="30">
						<br>
						<span class="profile-word"> Order Completed</span>
					</a>
				</div>

				<div class="col-xs-2" align="center">
					<a href="{{ route('completed_order') }}" style="position: relative;">
						@if($countCancelled > 0)
						<span class="badge badge-pill badge-danger" style="position: absolute; right: -10px; top: -10px;">
							{{ $countCancelled }}
						</span>
						@endif
						<img src="{{ url('images/profile/online_shop_ecommerce_shopping-46-512.png') }}" width="30">
						<br>
						<span class="profile-word" style="font-weight: bold; text-decoration: underline;">Order Cancelled</span>
					</a>
				</div>
			</div>
		</div>

		<div class="myOrder-list">
				@if (!$transactions->isEmpty())
				@foreach($transactions as $transaction)
				
				<div class="form-group container-box">
					<div class="row">
						<div class="col-xs-6 order-no-details">
							<b>Order no: #{{ $transaction->transaction_no }}</b><br>
							Order date: {{ $transaction->created_at }}
						</div>
						<div class="col-xs-6" align="right">
							@if($transaction->status == 99)
								<a href="#" class="btn btn-primary btn-sm pay-now-button" data-id="{{ md5($transaction->id) }}" data-toggle="modal" data-target="#myModal">
									Pay now
								</a>
							@else
								<a href="{{ route('order_detail', $transaction->transaction_no) }}" class="btn btn-primary btn-sm pay-now-button">
									Manage
								</a>
							@endif
						</div>
					</div>
					<hr>
					@foreach($details[$transaction->id] as $detail)
					@php
					$image = (!empty($detail->product_image)) ? $detail->product_image : 'images/no-image-available-icon-6.jpg';
					@endphp
					<div class="form-group">
						<div class="row">
							<div class="col-sm-1">
								<div class="from-group">
									<img src="{{ url($image) }}" style="width: 70px;">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group product-details">
									<div class="form-group">
										<b>{{ $detail->product_name }}</b>
									</div>
									@if($transaction->status == 99)
										<span class="badge badge-pill badge-warning">Unpaid</span>
									@elseif($transaction->status == 98)
										<span class="badge badge-pill badge-info">Waiting for verify</span>
									@elseif($transaction->status == 97)
										<span class="badge badge-pill badge-info">Waiting for verify</span>
									@elseif($transaction->status == 1)
										@if(!empty($transaction->bank_id))
				                            <span class="badge badge-pill badge-success">Paid</span>
				                        @else
				                            <span class="badge badge-pill badge-success">Paid</span>
				                        @endif
									@else
										<span class="badge badge-pill badge-danger">Cancelled</span>
									@endif
								</div>
							</div>
							<div class="col-sm-5" align="right">
								Qty: x{{ $detail->quantity }}
								<br>
								<br>
								@if($transaction->mall == '1')
									{{ number_format($detail->unit_price, 2) }} Point
								@else
									RM {{ number_format($detail->unit_price, 2) }}
								@endif
							</div>	
						</div>
					</div>
					<hr>
					@endforeach
					<div class="row">
						<div class="col-xs-6" align="left">
							{{ count($details[$transaction->id]) }} Products	
						</div>
						<div class="col-xs-6" align="right">
							@if($transaction->mall == '1')
								Total: {{ number_format($transaction->grand_total, 2) }} Point
							@else
								Total: RM {{ number_format($transaction->grand_total, 2) }}
							@endif
						</div>
					</div>

					@if($transaction->cod_address != '1' && $transaction->status == '1' && !empty($transaction->tracking_no) && !empty($transaction->order_number) && isset($ship_details[$transaction->id]))
					<hr>
					<div class="form-group">
						<i class="fa fa-truck" aria-hidden="true" style="font-size: 17px;"></i> &nbsp;&nbsp;&nbsp; [{{ $transaction->courier }}] {{ $ship_details[$transaction->id] }}
					</div>
					@endif
				</div>
				@endforeach
				@else
				<div class="form-group container-box">
					<div class="form-group" align="center">
						No order yet. <br><br>
						<a href="{{ route('home') }}" class="continue-shopping-btn btn btn-primary"> Continue Shopping</a>
					</div>
				</div>
				@endif
			</div>
	</div>
</div>
@endsection