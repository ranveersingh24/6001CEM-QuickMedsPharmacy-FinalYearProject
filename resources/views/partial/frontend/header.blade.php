@if(Request::segment(1) != 'Profile' && Request::segment(1) != 'MyQRcode' && Request::segment(1) != 'MyAffiliate' && Request::segment(1) != 'MyWallet' && Request::segment(1) != 'MyVoucher' && 
    Request::segment(1) != 'MyWishList' && Request::segment(1) != 'AddressBook' && Request::segment(1) != 'MySetting' && Request::segment(1) != 'PendingOrder' && Request::segment(1) != 'PendingShipping' &&
    Request::segment(1) != 'PendingReceive' && Request::segment(1) != 'OrderDetails' && Request::segment(1) != 'CompletedOrder' && Request::segment(1) != 'CancelledOrder')
<style type="text/css">
  .header .ps-logos {
    display: inline-block;
    max-width: 150px;
    line-height: 55px;
}
</style>
<header class="header">
  <div class="header__top">
    <div class="container-fluid">
    </div>
  </div>
  <meta name="referrer" content="no-referrer-when-downgrade">
  <!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/6535fc7ca84dd54dc4840957/1hdde4iga';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
  <nav class="navigation">
    <div class="container-fluid">
      <div class="navigation__column left">
        <div class="header__logo">
          <a class="ps-logos" href="{{ route('home') }}">
            <img src="{{ url($data['website_logo']) }}" alt=""  height="100">
          </a>
        </div>
      </div>
      <div class="navigation__column center">
            <ul class="main-menu menu">
              <li class="menu-item">
                  <a href="{{ route('home') }}" class="{{ (Request::segment(1) == '') ? 'active' : '' }}">Home</a>
                    
              </li>
              <li class="menu-item menu-item-has-children dropdown">
                <a href="{{ route('listing') }}" class="{{ (Request::segment(1) == 'Listing'|| Request::segment(1) == 'Details') ? 'active' : '' }}">Medicines</a>
               
            
              </li>
              <li class="menu-item">
                <a href="{{ route('about') }}" class="{{ (Request::segment(1) == 'about') ? 'active' : '' }}">About US</a>
                
                </li>
              <li class="menu-item">
                <a href="{{ route('Contact') }}" class="{{ (Request::segment(1) == 'Contact') ? 'active' : '' }}">Contact Us</a>
              </li>
              
              <li class="menu-item menu-item-has-children dropdown">
                 @if(Auth::guard($data['userGuardRole'])->check())
                  <a href="{{ route('profile') }}">
                    <i class="fa fa-user"></i> 
                    &nbsp;&nbsp;
                    {{ Auth::guard($data['userGuardRole'])->user()->f_name }} {{ Auth::guard($data['userGuardRole'])->user()->l_name }}
                  </a>
                  <ul class="sub-menu">
                    <li class="menu-item"><a href="{{ route('AddressBook.AddressBook.index') }}"><img src="{{ url('images/profile/address-book-2190068-1840518.png') }}" width="30">&nbsp;&nbsp;My Address Book</a></li>
                    
                    <li class="menu-item"><a href="{{ route('my_setting') }}"><img src="{{ url('images/profile/585e4d1ccb11b227491c339b.png') }}" width="30">&nbsp;&nbsp;Account Setting</a></li>

                    <li class="menu-item"><a href="{{ route('wish_list') }}"><img src="{{ url('images/profile/2310834.png') }}" width="30">&nbsp;&nbsp;My Wish List</a></li>
                    
                </ul>
                @else
                  <a href="{{ route('login') }}">
                    Login & Register
                  </a>
                @endif
                
              </li>

            </ul>
      </div>
      <div class="navigation__column right">
        <div class="ps-cart">
            <a class="ps-cart__toggle" href="#">
              <span><i>{{ (!empty($data['totalCart'])) ? $data['totalCart'] : '0'  }}</i></span>
              <i class="ps-icon-shopping-cart"></i></a>
              <div class="ps-cart__listing">
               
                  
                  
                
                <div class="tab-content">
                  <div role="tabpanel" class="tab-pane active" id="home">
                    <div class="ps-cart__content">    
                      @php
                        $headerTotalCart = 0;
                        $headerTotalQty = 0;
                      @endphp
                      @foreach($data['top_carts'] as $top_cart)
                      @php
                      if($top_cart->variation_enable == '1'){
                          if(Auth::guard('merchant')->check() || Auth::guard('admin')->check()){
                            $price = !empty($top_cart->variation_agent_special_price) ? $top_cart->variation_agent_special_price : $top_cart->variation_agent_price;
                          }else{
                            $price = !empty($top_cart->variation_special_price) ? $top_cart->variation_special_price : $top_cart->variation_agent_price;
                          }
                      }else{
                          if(Auth::guard('merchant')->check() || Auth::guard('admin')->check()){
                            $price = !empty($top_cart->agent_special_price) ? $top_cart->agent_special_price : $top_cart->agent_price;
                          }else{
                            $price = !empty($top_cart->special_price) ? $top_cart->special_price : $top_cart->price;
                          }
                      }

                      @endphp
                      <div class="ps-cart-item"><a class="ps-cart-item__close delete-cart" data-id="{{ md5($top_cart->cid) }}" href="#"></a>
                        <div class="ps-cart-item__thumbnail">
                          <a href="{{ route('details', [str_replace('/', '-', $top_cart->product_name), md5($top_cart->pid)]) }}"></a>
                            <img src="{{ url(!empty($top_cart->image) ? $top_cart->image : 'images/no-image-available-icon-61.jpg') }}" alt="">
                        </div>
                        <div class="ps-cart-item__content">
                          <a class="ps-cart-item__title" href="{{ route('details', [str_replace('/', '-', $top_cart->product_name), md5($top_cart->pid)]) }}">
                              {{ $top_cart->product_name }}
                              @if($top_cart->variation_enable == '1')
                                <br>
                                Variation: {{ $top_cart->variation_name }}
                              @endif  
                          </a>
                          <p>
                            <span style="margin-right: 0;">Quantity:<i>{{ $top_cart->qty }}</i></span>
                            <br>
                            @if($top_cart->variation_enable == '1')
                              @if(Auth::guard('merchant')->check() || Auth::guard('admin')->check())
                                <span style="margin-right: 0;">Total:<i>RM {{ $price }}</i></span></p>
                              @else
                                <span style="margin-right: 0;">Total:<i>RM {{ $price }}</i></span></p>
                              @endif
                            @else
                              @if(Auth::guard('merchant')->check() || Auth::guard('admin')->check())
                                <span style="margin-right: 0;">Total:<i>RM {{ $price }}</i></span></p>
                              @else
                                <span style="margin-right: 0;">Total:<i>RM {{ $price }}</i></span></p>
                              @endif
                            @endif
                        </div>
                      </div>
                      @php
                        $headerTotalCart += $price * $top_cart->qty;
                        $headerTotalQty += $top_cart->qty;
                      @endphp
                      @endforeach
                    </div>
                    <div class="ps-cart__total">
                      <p>Number of items:<span>{{ $headerTotalQty  }}</span></p>
                      <p>Item Total:<span>RM {{ number_format($headerTotalCart, 2) }}</span></p>
                    </div>
                    <div class="ps-cart__footer"><a class="ps-btn" href="{{ route('checkout') }}"> Checkout<i class="ps-icon-arrow-left"></i></a></div>
                  </div>
                  
        </div>
        <div class="menu-toggle"><span></span></div>
      </div>
    </div>
  </nav>
</header>
@endif