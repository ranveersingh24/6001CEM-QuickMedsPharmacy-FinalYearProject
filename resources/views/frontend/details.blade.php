@extends('layouts.app')
<style type="text/css">
	body{margin-top:20px;
background:#eee;
}

/*panel*/
.panel {
    border: none;
    box-shadow: none;
}

.panel-heading {
    border-color:#eff2f7 ;
    font-size: 16px;
    font-weight: 300;
}

.panel-title {
    color: #2A3542;
    font-size: 14px;
    font-weight: 400;
    margin-bottom: 0;
    margin-top: 0;
    font-family: 'Open Sans', sans-serif;
}

/*product list*/

.prod-cat li a{
    border-bottom: 1px dashed #d9d9d9;
}

.prod-cat li a {
    color: #3b3b3b;
}

.prod-cat li ul {
    margin-left: 30px;
}

.prod-cat li ul li a{
    border-bottom:none;
}
.prod-cat li ul li a:hover,.prod-cat li ul li a:focus, .prod-cat li ul li.active a , .prod-cat li a:hover,.prod-cat li a:focus, .prod-cat li a.active{
    background: none;
    color: #ff7261;
}

.pro-lab{
    margin-right: 20px;
    font-weight: normal;
}

.pro-sort {
    padding-right: 20px;
    float: left;
}

.pro-page-list {
    margin: 5px 0 0 0;
}

.product-list img{
    width: 100%;
    border-radius: 4px 4px 0 0;
    -webkit-border-radius: 4px 4px 0 0;
}

.product-list .pro-img-box {
    position: relative;
}
.adtocart {
    background: #fc5959;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    -webkit-border-radius: 50%;
    color: #fff;
    display: inline-block;
    text-align: center;
    border: 3px solid #fff;
    left: 45%;
    bottom: -25px;
    position: absolute;
}

.adtocart i{
    color: #fff;
    font-size: 25px;
    line-height: 42px;
}

.pro-title {
    color: #5A5A5A;
    display: inline-block;
    margin-top: 20px;
    font-size: 16px;
}

.product-list .price {
    color:#fc5959 ;
    font-size: 15px;
}

.pro-img-details {
    margin-left: -15px;
}

.pro-img-details img {
    width: 100%;
}

.pro-d-title {
    font-size: 16px;
    margin-top: 0;
}

.product_meta {
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
    padding: 10px 0;
    margin: 15px 0;
}

.product_meta span {
    display: block;
    margin-bottom: 10px;
}
.product_meta a, .pro-price{
    color:#fc5959 ;
}

.pro-price, .amount-old {
    font-size: 18px;
    padding: 0 10px;
}

.amount-old {
    text-decoration: line-through;
}

.quantity {
    width: 120px;
}

.pro-img-list {
    margin: 10px 0 0 -15px;
    width: 100%;
    display: inline-block;
}

.pro-img-list a {
    float: left;
    margin-right: 10px;
    margin-bottom: 10px;
}

.pro-d-head {
    font-size: 18px;
    font-weight: 300;
}
</style>
@section('content')
@php
if(!empty($Pimage->image))
  $Fimage = file_exists($Pimage->image) ? url($Pimage->image) : url('images/no-image-available-icon-6.jpg');
else
  $Fimage = url('images/no-image-available-icon-6.jpg');
@endphp
<div class="container bootdey">
<div class="col-md-12">
<section class="panel">
      <div class="panel-body">
          <div class="col-md-6">
              <div class="pro-img-details">
                  @if(!$images->isEmpty())
                  @foreach($images as $key => $image)
                    <div class="item"><img src="{{ url($image->image) }}" alt=""></div>
                  @endforeach
                @endif
              </div>
          </div>
          <div class="col-md-6">
              <h4 class="pro-d-title">
                  <a href="#" class="">
                   <b> {{ $product->product_name }} {{ !empty($product->uom_name) ? '/ '.$product->uom_name : '' }}</b>
                  </a>
              </h4>
              <p>
                  {{ $product->short_description }}
              </p>
              <div class="m-bot15"> <strong>Price : </strong> 
              	@if(Auth::guard('merchant')->check() || Auth::guard('admin')->check())
                    @if(!empty($product->agent_special_price))
                        RM {{ number_format($product->agent_special_price, 2) }}
                        <del>RM {{ number_format($product->agent_price, 2) }}</del> 
                    @else
                        RM {{ number_format($product->agent_price, 2) }}
                    @endif
                @else

                    @if(!empty($product->special_price))
                        RM {{ number_format($product->special_price, 2) }}
                        <del>RM {{ number_format($product->price, 2) }}</del> 
                    @else
                        RM {{ number_format($product->price, 2) }}
                    @endif
                @endif</div>
              <div class="form-group">
                  <label>Quantity</label>
                  <input class="form-control quantity" type="number" value="1" name="quantity">
                  @if($product->packages == 1)
                @else
                  @if($stockBalance <= 0)
                  <span class="quantity-balance important-text">Out of stock</span>
                  @else
                  <span class="quantity-balance">Only {{ $stockBalance }} Items Left</span>
                  @endif
                @endif
              </div>
              @if($product->variation_enable == '1')
      				@if(!$variations->isEmpty())
  		            <div class="ps-product__block ps-product__style">
  		              <h4>CHOOSE YOUR VARIATION</h4>
  		              <ul>
  		              	@foreach($variations as $variationsKey => $variation)
  		                <li>
  		                	<a href="#" class="variation_option {{ ($variationsKey == 0) ? 'active' : '' }} {{ ($vStock[$variation->id] <= 0) ? 'out-of-stock' : '' }}" data-id="{{ $variation->id }}">
  		                		{{ $variation->variation_name }}
  		                	</a>
  		                </li>
  		                @endforeach
  		              </ul>
  		            </div>
      		    @endif
      		  @endif
              <p>
                  <button class="btn btn-round btn-danger add-to-cart-button" type="button"><i class="fa fa-shopping-cart"></i> Add to Cart</button>
                  &nbsp;&nbsp;
                  @if(!empty($favourite->id)) 
                  <button class="btn btn-white btn-default mr-10 add-favourite-btn"><i class="fa fa-star"></i> Add to Wishlist</button>
		              @else
                  <button class="btn btn-white btn-default mr-10 add-favourite-btn"><i class="fa fa-star"></i> Add to Wishlist</button>
                  @endif
                  <button onclick="document.location='{{ route('Contact') }}'"class="btn btn-white btn-default"><i class="fa fa-envelope"></i> Contact Seller</button>  
              </p>
          </div>
      </div>
  </section>
  </div>
  </div>
@endsection

@section('js')
<script type="text/javascript">

  
  var variation_enable = '{{ $product->variation_enable }}';

  $('.add-to-cart-button').click( function(e){
  	// alert('123');
  	e.preventDefault();
	

  	$('.loading-gif').show();
  	var mall = '{{ $product->mall }}';
  	var isAdmin = '{{ Auth::guard("admin")->check() }}';
  	var isMerchant = '{{ Auth::guard("merchant")->check() }}';
  	var isUser = '{{ Auth::guard("web")->check() }}';
  	var isGuest = "{{ !empty($_COOKIE['new_guest']) ? $_COOKIE['new_guest'] : $data['new_guest'] }}";
  	var option = $('.variation_option.active').data('id');

  	if(variation_enable == 1 && !option){
  		alert('Please select product variation first');
  		$('.loading-gif').hide();
  		return false;
  	}

  	if(isAdmin){
  		auth_check = '{{ !empty(Auth::guard("admin")->user()->code) ? Auth::guard("admin")->user()->code : '' }}';
  	}else if(isMerchant){
  		auth_check = '{{ !empty(Auth::guard("merchant")->user()->code) ? Auth::guard("merchant")->user()->code : '' }}';
  	}else if(isUser){
  		auth_check = '{{ !empty(Auth::guard("web")->user()->code) ? Auth::guard("web")->user()->code : '' }}';
  	}else{
  		auth_check = "";
  	}
  	
  	if(auth_check){
	  	var fd = new FormData();
	  	fd.append('product_id', '{{ $product->id }}');
	  	fd.append('quantity', $('input[name="quantity"]').val());
	  	fd.append('sub_category_id', option);
	  	fd.append('second_sub_category_id', $('.second_sub_category_id').val());
	  	


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

	        	if(response == 'wallet not enough balance'){
	        		toastr.error('Wallet Balance Not Enough');
	        		return false;
	        	}

	        	if(response == 'quantity error'){
	        		toastr.error('Please Add Quantity At least 1');
	        		return false;
	        	}

	        	if(response == 'quantity exceed error'){
	        		toastr.error('Product Balance Quantity Not Enough');
	        		return false;
	        	}

	        	// if(response == 'quantity personal exceed'){
	        	// 	toastr.error('The maximum quantity available for this item is '+'{{ $stockBalance }}');
	        	// 	return false;
	        	// }

	        	if(response == 'ok'){
              location.reload();
	        		$.ajax({
				        url: '{{ route("CountCart") }}',
				        type: 'get',
				        success: function(response){
				        	$('.ps-cart .ps-cart__toggle span i').html(response[0]);
				        }
				      });

              $.ajax({
                url: '{{ route("SelectHeaderCart") }}',
                type: 'get',
                success: function(response){
                  $('.ps-cart__listing').html(response);
                }
              });

	            	toastr.success('Items Add To Cart. <a href="{{ route("checkout") }}" class="view-cart-button pull-right"><i class="fa fa-shopping-cart"></i> View Cart</a>');
	            }else{
                // toastr.error('Error Please Contact Admin');
	            	toastr.error(response);
	            }
	        },
	    });
  	}else{
  		window.location.href = "{{ route('login') }}";
  	}
  });

  $('.add-favourite-btn').click( function(e){
  		e.preventDefault();
  		$('.loading-gif').show();
  		var ele = $(this);
	  	var isAdmin = '{{ Auth::guard("admin")->check() }}';
	  	var isMerchant = '{{ Auth::guard("merchant")->check() }}';
	  	var isUser = '{{ Auth::check() }}';

	  	if(isAdmin){
	  		auth_check = isAdmin;
	  	}else if(isMerchant){
	  		auth_check = isMerchant;
	  	}else if(isUser){
	  		auth_check = isUser;
	  	}else{
	  		auth_check = "";
	  	}
	  	
	  	if(auth_check){
	  		var fd = new FormData();
		  	fd.append('product_id', '{{ $product->id }}');

		  	$.ajax({
		        url: '{{ route("Favourite") }}',
		        type: 'post',
		        data: fd,
		        contentType: false,
		        processData: false,
		        success: function(response){
		        	
		        	$('.loading-gif').hide();
              
		        	if(response[1] == 1){
		        		ele.css('background-color', '#2AC37D');
						
		        	}else{
		        		ele.css('background-color', '#999999');
						
						toastr.success('Items is unselected from WishList. <a href="{{ route("wish_list") }}" class="view-cart-button pull-right"><i class="fa fa-heart" aria-hidden="true"></i>View WishList</a>');
		        	}
					
		        }
		    });
	  	}else{
	  		window.location.href = "{{ route('login') }}";
	  	}
  });

  $('.add-to-wish-btn').click( function(e){
  		e.preventDefault();
  		$('.loading-gif').show();
  		var ele = $(this);
	  	var isAdmin = '{{ Auth::guard("admin")->check() }}';
	  	var isMerchant = '{{ Auth::guard("merchant")->check() }}';
	  	var isUser = '{{ Auth::check() }}';

	  	if(isAdmin){
	  		auth_check = isAdmin;
	  	}else if(isMerchant){
	  		auth_check = isMerchant;
	  	}else if(isUser){
	  		auth_check = isUser;
	  	}else{
	  		auth_check = "";
	  	}
	  	
	  	if(auth_check){
	  		var fd = new FormData();
		  	fd.append('product_id', '{{ $product->id }}');

		  	$.ajax({
		        url: '{{ route("add_to_wish") }}',
		        type: 'post',
		        data: fd,
		        contentType: false,
		        processData: false,
		        success: function(response){
		        	$('.loading-gif').hide();
		        	if(response == 1){
		        		$('.wishlist_count').html(response);
		        		$('.add-favourite-btn').html('<i class="fa fa-heart fa-2x" aria-hidden="true"></i>')
		        	}else{
		        		toastr.info('Already In Wishlist');
		        	}
		        }
		    });
	  	}else{
	  		window.location.href = "{{ route('login') }}";
	  	}
  });

  $('.sub-category-list').click( function(){
  	  var ele = $(this);
  	  $('.sub-category-list').removeClass('active');
  	  $(this).addClass('active');
  	  ele.parent().find('input[name="sub_category_id"]').prop("checked", true);
  });

  $('.add-qty-button').click( function(e){

		e.preventDefault();
		
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
		
	});

	$('.deduct-qty-button').click( function(e){
		e.preventDefault();
		
		var ele = $(this);
		var quantity = ele.parent().find('input[name="quantity"]').val();
		if(quantity != 1){
			quantity = Number(quantity) - 1;
			ele.parent().find('input[name="quantity"]').val(quantity);
		}		
	});

	
	if(variation_enable == 1){
		$('.variation_option').click( function(e){
			e.preventDefault();

			$('.loading-gif').show();
			var ele = $(this);
			var vid = ele.data('id');

			$('.variation_option').removeClass('active');
			ele.addClass('active');

			var fd = new FormData();
			  	fd.append('vid', vid);

			$.ajax({
		        url: '{{ route("getVariation") }}',
		        type: 'post',
		        data: fd,
		        contentType: false,
		        processData: false,
		        success: function(response){
		        	$('.loading-gif').hide();
		        	if(response[0] != 0){
			        	$('.ps-product__price').html('RM '+response[0]);
						$('.has-special-price').html('RM '+response[1]);
		        	}else{
		        		$('.ps-product__price').html('RM '+response[1]);
						$('.has-special-price').hide();
		        	}

		        	if(response[2] <= 0){
		        		$('.quantity-balance').html('Out of stock');
		        	}else{
		        		$('.quantity-balance').html('Only '+ response[2] +' Items Left');
		        	}
		        }
		    });
		});

		$.ajaxSetup({
	          headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
	    });
		$('.variation_option.active').trigger('click');		
	}

  $('.ps-product__preview').on('click', '.owl-stage-outer .owl-item.active .small-image', function(e){
      e.preventDefault();

      var ele = $(this);

      $('.ps-product__thumbnail--mobile .ps-product__main-img').find('img').attr('src', ele.attr('src'));
  });
	
</script>
@endsection