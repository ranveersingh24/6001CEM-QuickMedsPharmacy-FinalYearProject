@extends('layouts.app')
@section('content')
<style>
.container {
  position: relative;
  font-family: Arial;
}
.navigation--sticky .navigation {
   background-color: transparent; 
}
.navigation--sticky {
    position: fixed;
   border-bottom: 1px solid transparent; */
    top: 0;
    left: 0;
    width: 100%;
    z-index: 100000;
    transition: all 0.4s ease;
}
.text-block {
  position: absolute;
  bottom: 20px;
  right: 20px;
  background-color: black;
  color: white;
  padding-left: 20px;
  padding-right: 20px;
}
body {
  font-family: Arial, Helvetica, sans-serif;
  margin: 0;
}

html {
  box-sizing: border-box;
}

*, *:before, *:after {
  box-sizing: inherit;
}

.column {
  float: left;
  width: 33.3%;
  margin-bottom: 16px;
  padding: 0 8px;
}

.card {
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
  margin: 8px;
}

.about-section {
  padding: 50px;
  text-align: center;
  background-color: #474e5d;
  color: white;
}

.container {
  padding: 0 16px;
}

.container::after, .row::after {
  content: "";
  clear: both;
  display: table;
}

.title {
  color: grey;
}

.button {
  border: none;
  outline: 0;
  display: inline-block;
  padding: 8px;
  color: white;
  background-color: #000;
  text-align: center;
  cursor: pointer;
  width: 100%;
}

.button:hover {
  background-color: #555;
}

@media screen and (max-width: 650px) {
  .column {
    width: 100%;
    display: block;
  }
}
</style>
<!-- Categories Section Begin -->
<div class="ps-banner">
    <div class="rev_slider fullscreenbanner" id="home-banner">
      <ul>
        @foreach($banners as $key => $banner)
        <li class="ps-banner" data-index="rs-{{ $key }}" data-transition="random" data-slotamount="default" data-hideafterloop="0" data-hideslideonmobile="off" data-rotate="0">
            <img class="rev-slidebg" src="{{ url($banner->image) }}" alt="" data-bgposition="center center" data-bgfit="cover" data-bgrepeat="no-repeat" data-bgparallax="5" data-no-retina>
        </li>
        @endforeach
      </ul>
    </div>
  </div>
  <br>
    <!-- Our menu -->
    <section class="section-ourmenu bg2-pattern ">
       <div class="container">
    <div class="row">
       <div class="col-xs-6">
            
        </div>
    </div>
</div>
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-sm-6">
                            <!-- Item our menu -->
                            <div class=" bo-rad-10 hov-img-zoom pos-relative m-t-30">
                                <img src="images/hand_sani1.png" alt="IMG-MENU">

                                <!-- Button2 -->
                              
                                    <button onclick="document.location='http://127.0.0.1:8000/Listing?category=PERSONAL CARE&brand=&result='" type="button" class="btn btn-secondary flex-c-m txt5 ab-c-m size4">PERSONAL CARE</button>
                                   
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <!-- Item our menu -->
                            <div class="bo-rad-10 hov-img-zoom pos-relative m-t-30">
                                <img src="images/first_aid1.png" alt=" IMG-MENU ">

                                <!-- Button2 -->
                                <button onclick="document.location='http://127.0.0.1:8000/Listing?category=MEDICAL SUPPLIES&brand=&result='"type="button" class="btn btn-secondary flex-c-m txt5 ab-c-m size4">FIRST AID KIT</button>
                            </div>
                        </div>

                        
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="row">
                        <div class="col-12">
                            <!-- Item our menu -->
                            <div class="bo-rad-10 hov-img-zoom pos-relative m-t-30">
                                <img src="images/test_kit1.jpg" alt="IMG-MENU">

                                <!-- Button2 -->
                                <button onclick="document.location='http://127.0.0.1:8000/Listing?category=MEDICAL SUPPLIES&brand=&result='"type="button" class="btn btn-secondary flex-c-m txt5 ab-c-m size4">COVID-19 TEST KIT</button>
                            </div>
                        </div>

                        <div class="col-12">
                            <!-- Item our menu -->
                            <div class="bo-rad-10 hov-img-zoom pos-relative m-t-30">
                                <img src="images/skin_care1.jpg" alt="IMG-MENU">

                                <!-- Button2 -->
                                <button onclick="document.location='http://127.0.0.1:8000/Listing?category=SKIN CARE&brand=&result='"type="button" class="btn btn-secondary flex-c-m txt5 ab-c-m size4">SKIN CARE</button>
                            </div>
                        </div>





                        
                    </div>
                </div>
            </div>

        </div>
    </section>
    




<br>
@endsection


@section('js')
<script type="text/javascript">
$('.add-to-cart-btn').click( function(e){
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
        fd.append('product_id', ele.data('id'));
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

                if(response == 'ok'){
                    $.ajax({
                        url: '{{ route("CountCart") }}',
                        type: 'get',
                        success: function(response){
                            $('.cart_count span').html(response[0]);
                            $('.cart_price').html('RM '+parseFloat(response[1]).toFixed(2));
                            
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

    var id = ele.data('id');
    var nameProduct = ele.parent().parent().find('.js-name-b2').html();
    if(auth_check){
        var fd = new FormData();
        fd.append('product_id', id);

        $.ajax({
            url: '{{ route("Favourite") }}',
            type: 'post',
            data: fd,
            contentType: false,
            processData: false,
            success: function(response){
                $('.loading-gif').hide();
                if(response[0] == 1){
                    toastr.success('Added to wish list');
                }else{
                    toastr.success('Removed from wish list');
                }

                $('.wishlist_count').html(response[1]);
            }
        });
    }else{
        window.location.href = "{{ route('login') }}";
    }
});


</script>
@endsection