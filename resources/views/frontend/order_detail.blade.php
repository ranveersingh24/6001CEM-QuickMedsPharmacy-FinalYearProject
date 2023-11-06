@extends('layouts.app')

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
						<p align="center" class="header-title">My orders</p>
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
						@if(!empty(Auth::user()->profile_logo))
							<!-- <img src="{{ url(Auth::user()->profile_logo) }}" width="50" class="profile-logo"> -->
							<div style="background-image: url({{ url(Auth::user()->profile_logo) }}); width: 50px; height: 50px; border-radius: 100%; background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
						@else
							<img src="{{ url('images/images.png') }}" width="50" class="profile-logo">
						@endif
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
					<div class="form-group container-box">
						<div class="row">
							<div class="col-xs-6">
								<b>Order: #{{ $transaction->transaction_no }}</b><br>

								Date: {{ $transaction->created_at }}<br>

								
								Payment Method: {{ (!empty($transaction->cdm_bank_id)) ? 'Bank Transfer' : 'Online Banking' }}
								
							</div>
							<div class="col-xs-6" align="right">
							
									<h4>Total: RM {{ number_format($transaction->grand_total, 2) }}</h4>
								
							</div>
						</div>
					</div>
					<div class="form-group container-box">
						<div class="form-group">
							<h4>
								<b>Shipping Address</b>
							</h4>
						</div>
						<div class="form-group">
							{{ $transaction->address_name }} <br><br>
							{{ $transaction->address }}, <br>
							{{ $transaction->postcode }} {{ $transaction->city }}, <br>
							{{ $transaction->state }}<br><br>
							{{ $transaction->phone }}
						</div>
					</div>
					<div class="form-group container-box">
						@foreach($details as $detail)
						@php
						$image = (!empty($detail->product_image)) ? $detail->product_image : 'images/no-image-available-icon-6.jpg';
						@endphp
						<div class="form-group">
							<div class="row">
								<div class="col-sm-1" align="">
									<div class="from-group">
										<img src="{{ url($image) }}" style="width: 70px;">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group product-details">
										<b>{{ $detail->product_name }}</b> <br>
										{!! ($detail->sub_category != '') ? "Variation: ".$detail->sub_category."<br>" : '' !!}
										{!! ($detail->second_sub_category != '') ? "R: ".$detail->second_sub_category."<br>" : '' !!}
										Qty: {{ $detail->quantity }}<br>
										
											Price: RM {{ number_format($detail->unit_price, 2) }}
										
									</div>
								</div>
								<div class="col-sm-4">
									@if($transaction->status == 99)
										<span class="badge badge-pill badge-warning">Unpaid</span>
									@elseif($transaction->status == 98)
										<span class="badge badge-pill badge-info">Waiting for verify</span>
									@elseif($transaction->status == 97)
										<span class="badge badge-pill badge-info">Waiting for verify</span>
									@elseif($transaction->status == '96')
										<span class="badge badge-danger">Rejected</span>
									@elseif($transaction->status == 1)
										<span class="badge badge-success">Paid</span>
									@else
										<span class="badge badge-pill badge-danger">Cancelled</span>
									@endif
								</div>
							</div>
						</div>
						<hr>
						@endforeach
					</div>
					<div class="form-group">
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group container-box" style="height: 260px;">
								<div class="form-group">
									<h4>
										<b>Bank Slip</b>
									</h4>
								</div>

								@if(!empty($transaction->bank_slip))
									<a href="#" data-toggle="modal" data-target="#exampleModal">
										<img src="{{ url($transaction->bank_slip) }}" width="150px" height="150px">
									</a>

									<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
									  	<div class="modal-dialog">
									    	<div class="modal-content">
									      		<div class="modal-header">
									        		<!-- <h5 class="modal-title" id="exampleModalLabel">Modal title</h5> -->
									        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									          			<span aria-hidden="true">&times;</span>
									        		</button>
									      		</div>
										      	<div class="modal-body">
										        	<img src="{{ url($transaction->bank_slip) }}" width="100%">
										      	</div>
										      	<!-- <div class="modal-footer">
										        	<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
										      	</div> -->
									    	</div>
									  	</div>
									</div>
								@else
									<h5>No Bank Slip</h5>
								@endif
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group container-box">
								<div class="form-group">
									<h4>
										<b>Total Summary</b>
									</h4>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-xs-6">
											Subtotal: 
										</div>
										<div class="col-xs-6" align="right">
											
												RM {{ number_format(str_replace(',', '', $transaction->grand_total) - $transaction->shipping_fee - $transaction->processing_fee + $transaction->discount + $transaction->rebate_amount, 2) }}
											
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-xs-6">
											Shipping Fee:
										</div>
										<div class="col-xs-6" align="right">
											
												RM {{ number_format($transaction->shipping_fee, 2) }}
											
										</div>
									</div>
								</div>

								<div class="form-group">
				                    <div class="row">
				                        <div class="col-xs-6">
				                           Discount:
				                           ({{ ($transaction->amount_type == 'Percentage') ? $transaction->discount_amount."%" : 'RM '.$transaction->discount_amount }}): 
				                        </div>
				                        <div class="col-xs-6" align="right">
				                        	
												- RM {{ number_format($transaction->discount, 2) }}
											
				                        </div>
				                    </div>
				                </div>

				                @if(Auth::guard('web')->check())
								<div class="form-group">
				                    <div class="row">
				                        <div class="col-xs-6">
				                            Rebate(5%): 
				                        </div>
				                        <div class="col-xs-6" align="right">
				                        
												- RM {{ number_format($transaction->rebate_amount, 2) }}
											
				                        </div>
				                    </div>
				                </div>				                
				                @endif

								<div class="form-group">
				                    <div class="row">
				                        <div class="col-xs-6">
				                           Processing Fee:
				                        </div>
				                        <div class="col-xs-6" align="right">
				                        	
												RM {{ number_format($transaction->processing_fee, 2) }}
											
				                        </div>
				                    </div>
				                </div>
								<hr>
								<div class="form-group">
									<div class="row">
										<div class="col-xs-6">
											Grand Total: 
										</div>
										<div class="col-xs-6" align="right">
											
												RM {{ number_format($transaction->grand_total, 2) }}
											
										</div>
									</div>
								</div>
								@if(!empty($transaction->additional_shipping_fee))
								<hr>
								<div class="form-group">
									<div class="row">
										<div class="col-xs-6">
		                                 Additional Shipping Fee: 
										</div>
										<div class="col-xs-6" align="right">
											
												RM {{ number_format($transaction->additional_shipping_fee, 2) }}
										</div>
									</div>
								</div>
							    @endif
							</div>					
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection