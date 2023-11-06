@extends('layouts.app')

@section('content')
	<!-- breadcrumb -->
	
	<div class="cart_section">
	<div class="container">
		<div class="web-cart">
			<div class="form-group">
				<div class="cart-header-list">
					<ul>
						<li class="select-cart">
							<label>
                                <input name="form-field-checkbox" type="checkbox" class="ace select-all-checkbox" />
                                <span class="lbl"> </span>
                            </label>
						</li>
						<li class="product-name">
							<b>Product</b>
						</li>
						<li class="unit-price">
							<b>Unit Price</b>
						</li>
						<li class="product-quantity">
							<b>Quantity</b>
						</li>
						<li class="product-total-price">
							<b>Total Price</b>
						</li>
						<li class="list-action">
							<b>Actions</b>
						</li>
					</ul>
				</div>
			</div>
			<form method="POST" action="{{ (!empty(request('m')) && request('m') == '1') ? route('checkout', 'm=1') : route('checkout') }}" id="form-cart">
				@csrf
				<div class="form-group">
					<div class="cart-details-list">
						@php
						$totalWeight = 0;
						@endphp
						@if (!$carts->isEmpty())						
						@foreach($carts as $key => $cart)
						@php
                            if(isset($cart->image) && !empty($cart->image)){
                                $image = file_exists($cart->image) ? $cart->image : url('images/no-image-available-icon-6.jpg');
                            }else{
                                $image = url('images/no-image-available-icon-6.jpg');
                            }
                        @endphp
						<div class="form-group">
							<ul>
								<li class="select-cart">
									<label>
	                                    <input name="selected_cart[]" type="checkbox" class="ace list-check" value="{{ md5($cart->cid) }}" data-id="{{ md5($cart->cid) }}" />
	                                    <span class="lbl"> </span>
	                                </label>
									
								</li>
								<li class="product-name">
									<div class="form-group product-all-details">
										<a href="{{ route('details', [$cart->product_name, md5($cart->id)]) }}">
										<img src="{{ url($image) }}">
										<span class="product-details-name">
											{{ $cart->product_name }}
											<br>
											@php
											$totalWeight += $cart->weight;
											@endphp
											{{ !empty($cart->weight) ? 'Weight: '.$cart->weight.'KG' : '' }}
										</span>
										</a>
									</div>
								</li>
								<li class="unit-price">
									<div class="form-group">
										@if(!empty(request('m')) && request('m') == '1')
											@if(Auth::guard('merchant')->check() || Auth::guard('admin')->check())
												@if(!empty($cart->agent_special_price))
													{{ number_format($cart->agent_special_price, 2) }} point <br>
													<strike>{{ number_format($cart->agent_price, 2) }} point</strike>
												@else
													{{ number_format($cart->agent_price, 2) }} point
												@endif
											@else
												@if(!empty($cart->special_price))
												{{ number_format($cart->special_price, 2) }} point <br>
												<strike>{{ number_format($cart->price, 2) }} point</strike>
												@else
												{{ number_format($cart->price, 2) }} point
												@endif
											@endif
										@else
											<!-- @if(Auth::guard('merchant')->check() || Auth::guard('admin')->check())
												@if(!empty($cart->agent_special_price))
													RM {{ number_format($cart->agent_special_price, 2) }} <br>
													<strike>RM {{ number_format($cart->agent_price, 2) }}</strike>
												@else
													RM {{ number_format($cart->agent_price, 2) }}
												@endif
											@else
												@if(!empty($cart->special_price))
												RM {{ number_format($cart->special_price, 2) }} <br>
												<strike>RM {{ number_format($cart->price, 2) }}</strike>
												@else
												RM {{ number_format($cart->price, 2) }}
												@endif
											@endif -->
											@if(!empty($cart->special_price))
												<b>RM {{ number_format($cart->special_price, 2) }} </b><br>
												<strike>RM {{ number_format($cart->price, 2) }}</strike>
											@else
												<b>RM {{ number_format($cart->price, 2) }}</b>
											@endif
										@endif
										
									</div>
								</li>
								<li class="product-quantity">
									<div class="form-group quantity-setting">
										<button class="btn btn-primary deduct-qty-button">
											<i class="fa fa-minus"></i>
										</button>
										<input type="text" class="form-control" name="quantity" value="{{ $cart->qty }}" onkeypress="return isNumberKey(event)">
										<button class="btn btn-primary add-qty-button">
											<i class="fa fa-plus"></i>
										</button>
										<input type="hidden" name="balance_quantity" value="{{ $stockBalance[$cart->cid] }}">
									</div>
								</li>
								<li class="product-total-price">
									<!-- @if(!empty(request('m')) && request('m') == '1')
										@if(Auth::guard('merchant')->check() || Auth::guard('admin')->check())
											{{ number_format($cart->agent_actual_price * $cart->qty, 2) }} point
										@else
											{{ number_format($cart->actual_price * $cart->qty, 2) }} point
										@endif
									@else
										@if(Auth::guard('merchant')->check() || Auth::guard('admin')->check())
											RM {{ number_format($cart->agent_actual_price * $cart->qty, 2) }}
										@else
											RM {{ number_format($cart->actual_price * $cart->qty, 2) }}
										@endif
									@endif -->
									<!-- <b>RM {{ number_format($cart->actual_price * $cart->qty, 2) }}</b>	 -->
									@if(!empty($cart->special_price))
										<b>RM {{ number_format($cart->special_price, 2) }} </b><br>
										<strike>RM {{ number_format($cart->price * $cart->qty, 2) }}</strike>
									@else
										<b>RM {{ number_format($cart->price * $cart->qty, 2) }}</b>
									@endif
								</li>
								<li class="list-action">
									<a href="#" class="important-text non-load delete-cart-btn"><i class="fa fa-trash"></i></a>
								</li>
							</ul>
						</div>
						@endforeach
						@else
						<div class="form-group" align="center">
							There are no items in this cart <br><br>
							<a href="{{ route('home') }}" class="continue-shopping-btn btn btn-primary"> Continue Shopping</a>
						</div>
						@endif
					</div>
				</div>
			</form>
		</div>

		<div class="mobile-cart">
			<div class="form-group">
				<div class="cart-details-list">
					@if (!$carts->isEmpty())
					@foreach($carts as $key => $cart)
					@php
                        if(isset($cart->image) && !empty($cart->image)){
                            $image = file_exists($cart->image) ? $cart->image : url('images/no-image-available-icon-6.jpg');
                        }else{
                            $image = url('images/no-image-available-icon-6.jpg');
                        }
                    @endphp
					<div class="form-group">
						<ul>
							<li class="select-cart">
								<label>
                                    <input name="selected_cart[]" type="checkbox" class="ace list-check" value="{{ md5($cart->cid) }}" data-id="{{ md5($cart->cid) }}" />
                                    <span class="lbl"> </span>
                                </label>
							</li>
							<li class="product-name">
								<div class="form-group product-all-details">
									<a href="{{ route('details', [$cart->product_name, md5($cart->id)]) }}">
										<img src="{{ url($image) }}">
									</a>
								</div>
							</li>
							<li class="unit-price">
								<div class="form-group">
									<a href="{{ route('details', [$cart->product_name, md5($cart->id)]) }}">
										<span class="product-details-name">
											{{ $cart->product_name }} &nbsp;&nbsp; <a href="#" class="important-text non-load delete-cart-btn"><i class="fa fa-trash"></i></a>
											<br>
											@php
											$totalWeight += $cart->weight;
											@endphp
											{{ !empty($cart->weight) ? 'Weight: '.$cart->weight.'KG' : '' }}
										</span>
									</a>
								</div>
								<div class="form-group">
									<div class="mobile-cart-desc">
										@if(!empty(request('m')) && request('m') == '1')
											@if(Auth::guard('merchant')->check() || Auth::guard('admin')->check())
												@if(!empty($cart->agent_special_price))
												{{ number_format($cart->agent_special_price, 2) }} point &nbsp;&nbsp;	<strike>{{ number_format($cart->agent_price, 2) }} point</strike>
												@else
												{{ number_format($cart->agent_price, 2) }} point
												@endif
											@else
												@if(!empty($cart->special_price))
												{{ number_format($cart->special_price, 2) }} point &nbsp;&nbsp;	<strike>{{ number_format($cart->price, 2) }} point</strike>
												@else
												{{ number_format($cart->price, 2) }} point
												@endif
											@endif
										@else
											@if(Auth::guard('merchant')->check() || Auth::guard('admin')->check())
												@if(!empty($cart->agent_special_price))
												RM {{ number_format($cart->agent_special_price, 2) }} &nbsp;&nbsp;	<strike>RM {{ number_format($cart->agent_price, 2) }}</strike>
												@else
												RM {{ number_format($cart->agent_price, 2) }}
												@endif
											@else
												@if(!empty($cart->special_price))
												RM {{ number_format($cart->special_price, 2) }} &nbsp;&nbsp;	<strike>RM {{ number_format($cart->price, 2) }}</strike>
												@else
												RM {{ number_format($cart->price, 2) }}
												@endif
											@endif
										@endif
										<br><br>
										<div class="form-group quantity-setting">
											<button class="btn btn-primary deduct-qty-button">
												<i class="fa fa-minus"></i>
											</button>
											<input type="text" class="form-control" name="quantity" value="{{ $cart->qty }}">
											<button class="btn btn-primary add-qty-button">
												<i class="fa fa-plus"></i>
											</button>
											<input type="hidden" name="balance_quantity" value="{{ $stockBalance[$cart->cid] }}">
										</div>
									</div>
								</div>
							</li>
						</ul>
					</div>
					@endforeach
					@else
					<div class="form-group" align="center">
						There are no items in this cart <br><br>
						<a href="{{ route('home') }}" class="continue-shopping-btn btn btn-primary"> Continue Shopping</a>
					</div>
					@endif
				</div>
			</div>
		</div>

		<div class="form-group">
			<div class="cart-checkout">
				<ul>
					<li class="select-cart">
						<label>
                            <input name="form-field-checkbox" type="checkbox" class="ace select-all-checkbox" />
                            <span class="lbl"> </span>
                        </label>
					</li>
					<li class="checkout-total" align="right">
						<div class="form-group">
							<span class="subtotal-title">Subtotal (<span id="item-count">0</span> Items selected) (<span id="total-weight">0</span>KG): </span> 
								<b class="total-amount">
									@if(!empty(request('m')) && request('m') == '1')
										0.00 point
									@else
										RM 0.00
									@endif
								</b>
							<a href="#" class="btn btn-primary checkout-button">CHECK OUT</a>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
@endsection

@section('js')
<script type="text/javascript">
	$('.select-all-checkbox').click( function(){

		$('.loading-gif').show();
		$('.list-check').prop('checked', this.checked);
		$('.select-all-checkbox').prop('checked', this.checked);
		calc();
	});

	$('.web-cart .list-check, .mobile-cart .list-check').click( function(){
		$('.loading-gif').show();
		var cart_id = $(this).data('id');
		$('.list-check').each(function () {
			if($(this).data('id') == cart_id){
				$(this).click();
			}
        });
		calc();
	});

	

	$('.add-qty-button').click( function(e){

		e.preventDefault();
		$('.loading-gif').show();
		var ele = $(this);
		var quantity = ele.parent().find('input[name="quantity"]').val();
		var balance = ele.closest('ul').find('input[name="balance_quantity"]').val();
		quantity = Number(quantity) + 1;
		if(quantity > balance){
			alert('The maximum quantity available for this item is '+balance);
			$('.loading-gif').hide();
			return false;
		}else{
			ele.parent().find('input[name="quantity"]').val(quantity);			
		}
		var cart_id = ele.closest('ul').find('.list-check').data('id');

		updateQty(quantity, cart_id, ele);
		
	});

	$('.deduct-qty-button').click( function(e){
		e.preventDefault();
		$('.loading-gif').show();
		var ele = $(this);
		var quantity = ele.parent().find('input[name="quantity"]').val();
		if(quantity != 1){
			quantity = Number(quantity) - 1;
			ele.parent().find('input[name="quantity"]').val(quantity);
		}
		var cart_id = ele.closest('ul').find('.list-check').data('id');
		updateQty(quantity, cart_id, ele);
		
	});

	$('input[name="quantity"]').change( function(){
		var ele = $(this);
		var quantity = $(this).val();
		var cart_id = $(this).closest('ul').find('.list-check').data('id');
		var balance = ele.closest('ul').find('input[name="balance_quantity"]').val();

		if(parseInt(quantity) > parseInt(balance)){
			ele.val(balance);
			alert('The maximum quantity available for this item is '+balance);
			return false;
		}else{
			updateQty(quantity, cart_id, ele);			
		}
		
	});

	$('.delete-cart-btn').click( function(e){
		e.preventDefault();
		var ele = $(this);
		var cart_id = $(this).closest('ul').find('.list-check').data('id');

		deleteCart(cart_id, ele);
		
	});

	$('.checkout-button').click( function (e){
		e.preventDefault();
		var check = $('.list-check:checked').length;
		
		if(check == 0){
			alert('Please Select At Least 1 item(s) To Checkout.');
			return false;
		}else{
			$('#form-cart').submit();
		}
	});

	function updateQty(qty, cart_id, ele){

		var m = '{{ request("m") }}';
		var fd = new FormData();
		fd.append('cart_id', cart_id);
		fd.append('quantity', qty);

		$.ajax({
	       url: '{{ route("updateQuantity") }}',
	       type: 'post',
	       data: fd,
	       contentType: false,
	       processData: false,
	       success: function(response){
	       		if(m && m == 1){
	       			ele.closest('ul').find('.product-total-price').html(response+' point');
	       		}else{
	       			ele.closest('ul').find('.product-total-price').html('RM '+response);
	       		}

	       		calc();
	       },
	    });
	}

	function deleteCart(cart_id, ele){

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
		       		var cart_id = ele.closest('ul').find('.list-check').data('id');
		       		$.ajax({
				        url: '{{ route("CountCart") }}',
				        type: 'get',
				        success: function(response){
				        	
				        	$('.cart_count span').html(response[0]);
				        	$('.cart_price').html('RM '+parseFloat(response[1]).toFixed(2));
				        }
				    });
					$('.list-check').each(function () {

			        	if($(this).data('id') == cart_id){
							$(this).closest('ul').remove();
						}

			        });
			        var check = $('ul .list-check').length;
			        if(check == 0){
			        	$('.cart-details-list').html('<div class="form-group" align="center">There are no items in this cart <br><br><a href="{{ route("home") }}" class="continue-shopping-btn btn"> Continue Shopping</a></div>');
			        	$('.select-all-checkbox').prop('checked', false);
			        }
		       		calc();
		       },
		    });			
		}else{
			return false;
		}
	}

	function calc(){
		var m = '{{ request("m") }}';
		var arrayA = [];
		var checkedBox = $('.cart-details-list').find('.list-check:checked').data('id');
		$('.list-check:checked').each(function () {
            var sThisVal = (this.checked ? $(this).data('id') : "");
            arrayA.push(sThisVal);
        });
        
	    var fd = new FormData();
	    fd.append('cart_id', arrayA); 

	    $.ajax({
	       url: '{{ route("SelectCart") }}',
	       type: 'post',
	       data: fd,
	       contentType: false,
	       processData: false,
	       success: function(response){

	       		$.ajax({
			        url: '{{ route("CountCart") }}',
			        type: 'get',
			        success: function(response){
			        	$('.cart_count span').html(response[0]);
				        $('.cart_price').html('RM '+parseFloat(response[1]).toFixed(2));
			        }
			    });
	       		
	       		if(m && m == 1){
		         	$('.total-amount').html(response[0]+' point');
	       		}else{
	       			$('.total-amount').html('RM '+response[0]);
	       		}
	         	$('#item-count').html(response[1]);
	         	$('#total-weight').html(parseFloat(response[2]).toFixed(2));

	         	$('.loading-gif').hide();
	       },
	    });

	    if($('.list-check:checked').length == 0){
	    	$('.select-all-checkbox').prop('checked', false);
	    }

	    var countCheckbox = $('.list-check').length;

	    if($('.list-check:checked').length == countCheckbox && countCheckbox != 0){
	    	$('.select-all-checkbox').prop('checked', true);
	    }else{
	    	$('.select-all-checkbox').prop('checked', false);
	    }
	}
</script>
@endsection