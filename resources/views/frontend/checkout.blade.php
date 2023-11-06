@extends('layouts.app')
<style type="text/css">
	/*.nav-link.active{
	    color: #fff;
	    background-color: #007bff;
	}*/
	.footer__widget__social a .fa{
		margin-top: 10px;
	}
</style>
@section('content')



<br>
<div class="cart_section">
	<div class="container p-t-65 p-b-60">
		<form method="POST" action="{{ route('placeOrder') }}" id="placeorder-form" enctype="multipart/form-data">
		@csrf
			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						<div class="container-box f-15">
							<div class="form-group">
								<div class="row">
									<div class="col-xs-4">
										<b>Item Details</b>
									</div>
									<div class="col-xs-2" align="center">
										<b>Unit Price</b>
									</div>
									<div class="col-xs-2" align="center">
										<b>Qty</b>
									</div>
                                    <div class="col-xs-2" align="center">
										<b>Shipping Fee</b>
									</div>

									<div class="col-xs-2" align="right">
										<b>Total Price</b>
									</div>
								</div>
							</div>
							<hr>
							@php
							$totalPrice = 0;
							
							@endphp
							@foreach($carts as $key => $cart)
							@php
		                        if(isset($cart->image) && !empty($cart->image)){
		                            $image = file_exists($cart->image) ? $cart->image : url('images/no-image-available-icon-6.jpg');
		                        }else{
		                            $image = url('images/no-image-available-icon-6.jpg');
		                        }
		                    @endphp
							<div class="form-group cart-detail">
								<div class="row">
									<div class="col-xs-2 order-images">
										<input type="hidden" name="selected_cart[]" class="form-control required-feild" value="{{ md5($cart->cid) }}">
										<a href="{{ route('details', [$cart->product_name, md5($cart->id)]) }}">
											<img src="{{ url($image) }}" style="width: 100%;">
										</a>
									</div>
									<div class="col-xs-10 order-details">
										<div class="row">
											<div class="col-sm-3">
												<div class="form-group product-name">
													<a href="{{ route('details', [$cart->product_name, md5($cart->id)]) }}">
														{{ $cart->product_name }}
														@if($cart->variation_enable == '1')
															<br>
															Variation: {{ $cart->variation_name }}
														@endif
														
													</a>
													
													<br>
													<a href="#" class="important-text non-load delete-cart-btn" data-id="{{ md5($cart->cid) }}">
														<i class="fa fa-trash"></i>
													</a>
												</div>
											</div>


											<div class="col-sm-1" align="right">
												<div class="form-group">
													@if(!empty(request('m')) && request('m') == '1')
														@if($cart->variation_enable == '1')
															@if(Auth::guard('merchant')->check() || Auth::guard('admin')->check())
																{{ number_format($cart->variation_point_agent_price, 2) }}	point
															@else
																{{ number_format($cart->variation_point_price, 2) }}	point
															@endif
														@else
															@if(Auth::guard('merchant')->check() || Auth::guard('admin')->check())
																{{ number_format($cart->point_agent_price, 2) }}	point
															@else
																{{ number_format($cart->point_price, 2) }}	point
															@endif
														@endif
													@else
														@if(Auth::guard('merchant')->check() || Auth::guard('admin')->check())
															@if($cart->variation_enable == '1')
																@if(!empty($cart->variation_agent_special_price))
																	RM {{ number_format($cart->variation_agent_special_price, 2) }}	
																@else
																	RM {{ number_format($cart->variation_agent_price, 2) }}	
																@endif
															@else
																@if(!empty($cart->agent_special_price))
																	RM {{ number_format($cart->agent_special_price, 2) }}	
																@else
																	RM {{ number_format($cart->agent_price, 2) }}	
																@endif
															@endif
														@else
															@if($cart->variation_enable == '1')
																@if(!empty($cart->variation_special_price))
																	RM {{ number_format($cart->variation_special_price, 2) }}	
																@else
																	RM {{ number_format($cart->variation_price, 2) }}	
																@endif
															@else
																@if(!empty($cart->special_price))
																	RM {{ number_format($cart->special_price, 2) }}	
																@else
																	RM {{ number_format($cart->price, 2) }}	
																@endif
															@endif
														@endif
													@endif
													
												</div>
											</div>
											<div class="col-sm-3" align="right">
												<div class="form-group">
													Qty: {{ $cart->qty }}
												</div>
											</div>
											<div class="col-sm-2" align="right">
												<div class="form-group shipping_amount">
													@if(!empty(request('m')) && request('m') == '1')
												@else
												@if($totalshipping_fees != 0)			
										RM {{ number_format($totalshipping_fees, 2) }}
											@endif
											@endif
												</div>
											</div>
											<div class="col-sm-3" align="right">
												<div class="form-group">
													@if(!empty(request('m')) && request('m') == '1')
														@if($cart->variation_enable == '1')
															@if(Auth::guard('merchant')->check() || Auth::guard('admin')->check())
																{{ number_format($cart->variation_point_agent_price * $cart->qty, 2) }}	point
															@else
																{{ number_format($cart->variation_point_price * $cart->qty, 2) }}	point
															@endif
														
														@endif
													
														@else
															@if($cart->variation_enable == '1')
																@if(!empty($cart->variation_special_price))
																	RM {{ number_format($cart->variation_special_price * $cart->qty, 2) }}	
																@else
																	RM {{ number_format($cart->variation_price * $cart->qty, 2) }}	
																@endif
															@else
																@if(!empty($cart->special_price))
																	RM {{ number_format($cart->special_price * $cart->qty, 2) }}	
																@else
																	RM {{ number_format($cart->price * $cart->qty, 2) }}	
																@endif
															@endif
														@endif
													
												</div>
											</div>
										</div>
									</div>
								</div>
								<hr>
							</div>
							@php
							if(!empty(request('m')) && request('m') == '1'){
								if($cart->variation_enable == '1'){
									if(Auth::guard('merchant')->check() || Auth::guard('admin')->check()){
										$totalPrice += $cart->variation_point_agent_price * $cart->qty;
									}else{
										$totalPrice += $cart->variation_point_price * $cart->qty;
									}

								}else{
									
								}
							
								}else{
									if($cart->variation_enable == '1'){
										if(!empty($cart->variation_special_price)){
											$totalPrice += $cart->variation_special_price * $cart->qty;
										}else{
											$totalPrice += $cart->variation_price * $cart->qty;
										}
									}else{
										if(!empty($cart->special_price)){
											$totalPrice += $cart->special_price * $cart->qty;
										}else{
											$totalPrice += $cart->price * $cart->qty;
										}
									}
								}
						
							
							


							@endphp
							@endforeach
				
							@php
								$applied_discount_amount = 0;
								$applied_discount_type = "";
								if(!empty($checkAppliedPromo->id)){
									if($checkAppliedPromo->amount_type == 'Percentage'){
										$applied_discount_amount = (float) $totalPrice * $checkAppliedPromo->amount / 100;
										$applied_discount_type = $checkAppliedPromo->amount."%";
									}else{
										$applied_discount_amount = $checkAppliedPromo->amount;
										$applied_discount_type = "RM ".$checkAppliedPromo->amount;
									}
								}
							@endphp
							

							<div class="form-group">
								<div class="row">
									<div class="col-xs-6">
										<b>Subtotal: </b>
									</div>
									<div class="col-xs-6" align="right">

										
									
											<b>RM {{ number_format($totalPrice, 2) }} </b>
									

										<input type="hidden" name="sub_total" id="subtotal" value="{{ $totalPrice }}">
									</div>
								</div>
							</div>
							<hr>
							
							@php
								$totalRebate = 0;
							@endphp
							@if(Auth::guard('web')->check())
							@php
								$totalRebate = number_format(($totalPrice - $applied_discount_amount) * 5 / 100, 2, '.', '');
							@endphp
							<div class="form-group">
								<div class="row">
									<div class="col-xs-6">
										<b>Rebate(5%): </b>
									</div>
									<div class="col-xs-6" align="right">
										@if(!empty(request('m')) && request('m') == '1')
											<b>0.00 point</b>
										@else
											@if(!empty($totalRebate))
												<b>RM {{ number_format($totalRebate, '2') }}</b>
											@else
												<b>RM 0.00</b>
											@endif
										@endif
										
									</div>
								</div>
							</div>
							<hr>
							@endif
							<input type="hidden" name="hidden_rebate" class="hidden_rebate" value="{{ !empty($totalRebate) ? $totalRebate : '0' }}">
							

							
							
							
							<div class="form-group">
								<div class="row">
									<div class="col-xs-6">
								<b style="font-size: 20px;">Grand total: </b>
									</div>
									<div class="col-xs-6" align="right" style="font-size: 20px;">
										@if(!empty(request('m')) && request('m') == '1')
											<b class="grand-total">{{ number_format(($totalPrice - $applied_discount_amount), 2) }} point</b>
											@php
												$totalGrand = $totalPrice + $totalshipping_fees;
											@endphp
										@else
											<b class="grand-total">RM {{ number_format(($totalPrice - $applied_discount_amount - $totalRebate + $totalshipping_fees), 2) }}</b>
											@php
												$totalGrand = ($totalPrice - $applied_discount_amount - $totalRebate + $totalshipping_fees);
											@endphp
										@endif

										<input type="hidden" id="hidden_grand_total" value="{{ $totalGrand }}">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-12">
						<div class="container-box form-group">
							<div class="form-group">
								<h4>Shipping Address </h4>
							</div>
							@if(!empty($shipping_address->id))
								<input type="hidden" name="billing_details_im" value="{{ md5($shipping_address->id) }}">
								<div class="form-group">
									<div class="form-group">
										<i class="fa fa-user" aria-hidden="true"></i> &nbsp;&nbsp;
										<b>{{ $shipping_address->f_name }} {{ $shipping_address->l_name }}</b>
									</div>
									<div class="form-group">
										<i class="fa fa-map-marker" aria-hidden="true"></i> &nbsp;&nbsp;
										<span>{{ $shipping_address->address }},</span> <br> 
										<span class="ml-22">{{ $shipping_address->postcode }} {{ $shipping_address->city }},</span> <br>
										<span class="ml-22">{{ $shipping_address->name }}</span>
									</div>
									<div class="form-group">
										<i class="fa fa-phone" aria-hidden="true"></i> &nbsp;&nbsp;
										{{ $shipping_address->phone }}
									</div>
									<div class="form-group">
										<i class="fa fa-envelope" aria-hidden="true"></i> &nbsp;&nbsp;
										{{ $shipping_address->email }}
									</div>
								</div>
							@else
								<input type="hidden" name="billing_details_im" value="">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-6">
											<input type="text" class="form-control required-feild" placeholder="First Name *" name="f_name" value="">
										</div>
										<div class="col-sm-6">
											<input type="text" class="form-control required-feild" placeholder="Last Name *" name="l_name" value="">
										</div>
									</div>
								</div>
								<div class="form-group">
									<input type="text" class="form-control required-feild" placeholder="Email Address *" name="email" value="">
								</div>
								<div class="form-group">
									<input type="text" class="form-control required-feild" placeholder="Phone *" name="phone" value="" onkeypress="return isNumberKey(event)">
								</div>
								<div class="form-group">
									<textarea class="form-control required-feild" placeholder="Address *" name="address"></textarea>
								</div>
								<div class="form-group">
									<input type="text" name="state" class="form-control" placeholder="State *">
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-sm-6">
											<input type='text' class="form-control required-feild" placeholder="City *" name="city" value="">
										</div>
										<div class="col-sm-6">
											<input type='text' class="form-control required-feild" placeholder="Post Code*" name="postcode" value="" onkeypress="return isNumberKey(event)">
										</div>
									</div>
								</div>
								<div class="form-group">
									<textarea class="form-control" name="" placeholder="Remark"></textarea>
								</div>
								<div class="form-group">
									<b id="error-message" class="important-text error-message"></b>
								</div>

							@endif
							
								<div class="widget-box transparent" id="recent-box">
									<div class="widget-header">
										

										<div class="widget-toolbar no-border">
											<ul class="nav nav-tabs" id="recent-tab">
												<a data-toggle="tab" class="payment_method f-15" data-id="2" href="#cdm-tab">Bank Transfer/Duitnow QR</a>
											</ul>
										</div>
									</div>

									<div class="widget-body">
										<div class="widget-main padding-4">
											<div class="tab-content padding-8">

												<input type="radio" name="bank_id" value="1" checked>
												
												<div id="cdm-tab" class="tab-pane " align="center">
													<div class="form-group">
														<input type="hidden" name="cdm_bank_id" value="10000743">
														<div class="card border-danger mb-3" style="max-width: 18rem;" align="center">
															<div class="card-body text-danger">
															    <h5 class="card-title">Ranveer Singh Balleh</h5>
															    <h5 class="card-title">Maybank</h5>
															    <h5 class="card-title">1583 0559 0298</h5>
															</div><br>
															<h5 class="card-title">OR</h5><br><br>
															<h5 class="card-title">Scan the QR for Duitnow</h5>
															<img src="https://donate.sols247.org/wp-content/uploads/2022/01/duitnow-qr-code-sols247.png" alt="Image Description">
														</div>
													</div>
													<div class="form-group bank_details">

													</div>

													<input type="hidden" name="cdm">
													<div class="form-group">
														<input type="file" name="bank_slip" class="form-control" accept="image/*" style="height: 34px;">
													</div>

													<div class="form-group">
														<b id="error-message-cdm-banks" class="important-text"></b>
													</div>
													
													<div class="form-group">
														<button class="btn btn-primary btn-block placeorder-btn bg-color">Place order now
														</button>
													</div>

													<div class="form-group">
														<a href="{{ route('listing') }}" class="btn btn-primary btn-block bg-color"> Continue Shopping
														</a>
													</div>
												</div><!-- /.#member-tab -->
											</div>
										</div><!-- /.widget-main -->
									</div><!-- /.widget-body -->
								</div><!-- /.widget-box -->
						</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

@endsection
@section('js')
<script type="text/javascript">
	$(document).ready( function(){
            
        if($(window).width() < 480) {
            $('.order-images').removeClass('col-2');
            $('.order-images').addClass('col-3');

            $('.order-details').removeClass('col-10');
            $('.order-details').addClass('col-9');
        }else{

        }
    });
    $('#placeorder-form .required-feild').change( function(){
    	if($(this).val()){
    		$(this).removeClass('required-feild-error');
    	}
    });
	$('.placeorder-btn').click( function(e){
		e.preventDefault();
		$('.loading-gif').show();
		var empty_fill;
		var grand_total = $('#hidden_grand_total').val();
		var m = '{{ request("m") }}';
		
		if(m == 1){
			if(parseFloat(wallet_balance) < parseFloat(grand_total)){
				$('#error-balance').html('Your wallet balance not enough.');
				$('.loading-gif').hide();
	    		return false;
			}

			if(confirm('Confirm redeem?') == false){
				$('.loading-gif').hide();
				return false;
			}
		}
		
	    $('#placeorder-form .required-feild').each( function(){
	    	if(!$(this).val()){
	    		$(this).addClass('required-feild-error');
	    		empty_fill = 1;
	    	}
	    });
	    if(empty_fill == 1){
	    	$('.error-message').html('Please Fill In All Required Field.');
	    	$('.loading-gif').hide();
	    	return false;
	    }

	    if(!m && m != 1){
		    if(!$("input[name='bank_id']:checked").val()){
		    	$('#error-message-banks').html('Please Select Bank To Continue Payment.');
		    	$('.loading-gif').hide();
		    	return false;
		    }	    	
	    }

	    $('#placeorder-form').submit();
	});


	
	


	

	$('.parent_payment_method').click( function(e){
		e.preventDefault();

		var ele = $(this);
		$('.tab-pane').removeClass('active');
		$(ele.data('filter')).addClass('active');
	});

	$('.payment_method').click( function(e){
		e.preventDefault();

		var ele = $(this);
		var total = $('#hidden_grand_total').val();
		var sub = $('#subtotal').val();
		var shipping_fee = $('.hidden_shipping_amount').val();
		var discount = $('.hidden_discount').val();
		var rebate = $('.hidden_rebate').val();
		var m = '{{ request("m") }}';

		$('.parent_payment_method').removeClass('active');
		ele.parent().addClass('active');

		discount = (discount) ? discount : 0;
		total = parseFloat(sub) + parseFloat(shipping_fee) - parseFloat(discount) - parseFloat(rebate);

		if(ele.data('id') == 1){
			$("input[name='online']").val(1);
			$("input[name='cdm']").val(0);
			if(m != 1){
				if(total <= 0){
					// $('.processing_amount').html('RM '+parseFloat(shipping_fee * 1.6 / 100).toFixed(2));
					// $('.hidden_processing_amount').val(parseFloat(shipping_fee * 1.6 / 100).toFixed(2));

					$('#hidden_grand_total').val((parseFloat(shipping_fee) - parseFloat(discount)).toFixed(2));
					$('.grand-total').html('RM '+(parseFloat(shipping_fee)).toFixed(2));
				}else{
					// $('.processing_amount').html('RM '+parseFloat(total * 1.6 / 100).toFixed(2));
					// $('.hidden_processing_amount').val(parseFloat(total * 1.6 / 100).toFixed(2));

					$('#hidden_grand_total').val((parseFloat(total)).toFixed(2));
					$('.grand-total').html('RM '+(parseFloat(total)).toFixed(2));
					
				}
			}

		}else{
			if(m != 1){
				$('.processing_amount').html('RM 0.00');
				$('.hidden_processing_amount').val('0.00');
				if(total <= 0){
					$('#hidden_grand_total').val(shipping_fee);
					$('.grand-total').html('RM '+parseFloat(shipping_fee).toFixed(2));
				}else{
					$('#hidden_grand_total').val((parseFloat(sub) - parseFloat(discount) - parseFloat(rebate) + parseFloat(shipping_fee)).toFixed(2));
					$('.grand-total').html('RM '+(parseFloat(sub) - parseFloat(discount) - parseFloat(rebate) + parseFloat(shipping_fee)).toFixed(2));				
				}

			}

			$("input[name='online']").val(0);
			$("input[name='cdm']").val(1);
		}
		
	});

	

	$('.delete-cart-btn').click( function(e){
		e.preventDefault();
		var ele = $(this);
		var cart_id = $(this).data('id');
		var fd = new FormData();
		fd.append('cart_id', cart_id);
		
		if(confirm("Item(s) will be removed from Cart") == true){
			$('.loading-gif').show();
			$.ajax({
		       url: '{{ route("deleteCart") }}',
		       type: 'post',
		       data: fd,
		       contentType: false,
		       processData: false,
		       success: function(response){
		       		$('.loading-gif').hide();
		       		$.ajax({
				        url: '{{ route("CountCart") }}',
				        type: 'get',
				        success: function(response){
				        	
				        	$('.cart_count span').html(response[0]);
				        	$('.cart_price').html('RM '+parseFloat(response[1]).toFixed(2));
				        }
				    });
			        ele.closest('.cart-detail').remove();
			        var check = $('.container-box .cart-detail').length;
		       		
			        if(check == 0){
			        	window.location.href = "{{ route('listing') }}";
			        }else{
			        	location.reload();
			        }
		       },
		    });			
		}
		
	});

	$('.change-login-register-tab').click( function(e){
		e.preventDefault();
		var ele = $(this);
		$('.rl-tab').removeClass('show active');
		$(ele.attr('href')).addClass('show active');
		// alert(ele.parent().parent().('class'));
	});

	$('.continue-as-guess').click( function(e){
		$('.loading-gif').show();
		$.ajax({
	        url: '{{ route("setNewGuest") }}',
	        type: 'get',
	        success: function(response){
	        	$('.loading-gif').hide();
	        	location.reload();
	        }
	    });
	});

	$('#login-form .button-inside').on('click', '.get-verify-code-btn', function(e){
        e.preventDefault();
        var ele = $(this);
        var phone = $('#login-form input[name="phone"]').val();
        if(phone.length < 10){
            alert("Please enter a valid mobile phone number");
            return false;
        }

        var fd = new FormData();
        fd.append('phone', phone);

    });

    $('.login-btn').click( function(e){
       e.preventDefault();
       $('.loading-gif').show();
       // $('input[name="password"]').val(phone);
       $('#login-form').submit();
  
    });


    $('#register-form .button-inside').on('click', '.get-verify-code-btn', function(e){
        e.preventDefault();
        var ele = $(this);
        var phone = $('#register-form input[name="phone"]').val();
        if(phone.length < 10){
            alert("Please enter a valid mobile phone number");
            return false;
        }

        var fd = new FormData();
        fd.append('phone', phone);
        fd.append('register', '1');


    });


    $('.register-btn').click( function(e){
       e.preventDefault();

       // $('input[name="password"]').val(phone);
       // $('#register-form').submit();
       var empty_fill;
       var phone = $('#register-form input[name="phone"]').val();
       var code = $('#register-form input[name="code"]').val();
       var country_code = $('#register-form .country_code').val();
       // var refferal_code = $('input[name="refferal_code"]').val();

       $('#register-form .required-feild').each( function(){
            if(!$(this).val()){
                $(this).addClass('required-feild-error');
                empty_fill = 1;
            }
        });
        if(empty_fill == 1){
            $('.error-message').html('Please Fill In All Required Field.');
            $('.loading-gif').hide();
            return false;
        }

       if(!phone){
            $('#action-return-message').addClass('important-text');
            $('#action-return-message').html("Please enter phone number");
            return false;
       }else{
          if(phone.length < 10){
                $('#action-return-message').addClass('important-text');
                $('#action-return-message').html("Please enter a valid mobile phone number");
                return false;
          }
       }

       if(!code){
            $('#action-return-message').addClass('important-text');
            $('#action-return-message').html("Please enter a valid verification code");

            return false;
       }



       var fd = new FormData();
       fd.append('phone', phone);
       fd.append('code', code);
       fd.append('country_code', country_code);
       // fd.append('refferal_code', refferal_code);

       $.ajax({
            url: '{{ route("CheckLogin") }}',
            type: 'post',
            data: fd,
            contentType: false,
            processData: false,
            success: function(response){
                if(response == 1){
                    $('#register-form #action-return-message').html("Verification code error");
                    $('#register-form #action-return-message').addClass('important-text');
                    return false;
                }else if(response == 2){
                    // $('input[name="password"]').val(phone);
                    $('#register-form').submit();
                }else if(response == 3){
                    $('#register-form #action-return-message').html("Account exists");
                    $('#register-form #action-return-message').addClass('important-text');
                }else if(response == 4){
                    $('#register-form #action-return-message').html("Referrer's mobile phone number does not exist");
                    $('#register-form #action-return-message').addClass('important-text');
                }else{
                    $('#register-form #action-return-message').html("System error");
                    $('#register-form #action-return-message').addClass('important-text');
                }
            },
        }); 
    });

    
</script>

@if(empty($shipping_address->id))
<script type="text/javascript">
	$('.start-modal').click();
</script>
@endif
@endsection