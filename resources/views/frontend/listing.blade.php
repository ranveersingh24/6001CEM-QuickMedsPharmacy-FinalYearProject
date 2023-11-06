@extends('layouts.app')

@section('content')
<div class="ps-products-wrap pt-80 pb-80">
        <div class="ps-products" data-mh="product-listing">
          <div class="ps-product-action">
            
            <div class="ps-pagination">
              <ul class="pagination">
                
                {{ $products->links() }}
              </ul>
            </div>
          </div>

          <div class="ps-product__columns">
            
            @if(!$products->isEmpty())
                @foreach($products as $product)
                @php
                    $discount_percentage = 0;
                    if(Auth::guard('merchant')->check() || Auth::guard('admin')->check()){
                        if(!empty($product->agent_special_price)){
                            
                            $discount_percentage =  (($product->agent_price - $product->agent_special_price)*100) / $product->agent_price;
                            
                        }
                    }else{
                        if(!empty($product->special_price)){
                            $discount_percentage = (($product->price - $product->special_price)*100) / $product->price;
                        }
                    }
                @endphp
                <div class="ps-product__column">
                  <div class="ps-shoe mb-30">
                    <div class="ps-shoe__thumbnail">
                        @if($product->packages == '1')
                        <div class="ps-badge ">
                            <span>Packages</span>
                        </div>
                        @endif
                        @if(!empty($discount_percentage))
                        <div class="ps-badge ">
                            <span>-{{ number_format($discount_percentage) }}%</span>
                        </div>
                        @endif
                        <!-- <div class="ps-badge ps-badge--sale ps-badge--2nd"><span>-35%</span></div> -->
                        <!-- <a class="ps-shoe__favorite" href="#"><i class="ps-icon-heart"></i></a> -->
                        <img src="{{ (!empty($product->image)) ? url($product->image)  : 'images/no-image-available-icon-61.jpg' }}" alt=""width="400" height="300">
                        <a class="ps-shoe__overlay" href="{{ route('details', [str_replace('/', '-', $product->product_name), md5($product->id)]) }}"></a>
                    </div>
                    <div class="ps-shoe__content">
                      <div class="ps-shoe__variants">
                        <div class="ps-shoe__variant normal">
                            @if(!$productImages[$product->id]->isEmpty())
                            @foreach($productImages[$product->id] as $small_image)
                                <img src="{{ url($small_image->image) }}" alt="">
                            @endforeach
                            @endif
                        </div>
                        
                      </div>
                      <div class="ps-shoe__detail">
                            <a class="ps-shoe__name" href="#">
                                {{ $product->product_name }}
                            </a>
                          <p class="ps-shoe__categories">
                            <!-- <a href="#">Men shoes</a>,<a href="#"> Nike</a>,<a href="#"> Jordan</a> -->
                          </p>
                            <span class="ps-shoe__price">
                                @if($product->variation_enable == '1')
                                    @if(Auth::guard('merchant')->check() || Auth::guard('admin')->check())
                                        @if($priceV[$product->id][3] == $priceV[$product->id][2])
                                            RM {{ number_format($priceV[$product->id][3], 2) }}    
                                        @else
                                            RM {{ number_format($priceV[$product->id][3], 2) }} - {{ number_format($priceV[$product->id][2], 2) }}
                                        @endif
                                    @else
                                        @if($priceV[$product->id][1] == $priceV[$product->id][0])
                                            RM {{ number_format($priceV[$product->id][1], 2) }}
                                        @else
                                            RM {{ number_format($priceV[$product->id][1], 2) }} - {{ number_format($priceV[$product->id][0], 2) }}
                                        @endif
                                    @endif
                                @else
                                    @if(Auth::guard('merchant')->check() || Auth::guard('admin')->check())
                                        @if(!empty($product->agent_special_price))
                                            <del>RM {{ number_format($product->agent_price, 2) }}</del> 
                                            RM {{ number_format($product->agent_special_price, 2) }}
                                        @else
                                            RM {{ number_format($product->agent_price, 2) }}
                                        @endif
                                    @else

                                        @if(!empty($product->special_price))
                                            <del>RM {{ number_format($product->price, 2) }}</del> 
                                            RM {{ number_format($product->special_price, 2) }}
                                        @else
                                            RM {{ number_format($product->price, 2) }}
                                        @endif
                                    @endif
                                @endif
                            </span>
                        </div>
                    </div>
                  </div>
                </div>
                @endforeach

                
            @else
                <div class="form-group" align="center">
                    <p>No Result Found!</p>
                    <p>We’re sorry. We couldn’t find anything that matched your search term</p>
                    <i class="fa fa-search fa-5x"></i> 
                </div>
            @endif
          </div>
          <div class="ps-product-action" align="right">
            
            <div class="ps-pagination">
              <ul class="pagination">
                
                {{ $products->links() }}
              </ul>
            </div>
          </div>
        </div>
        <div class="ps-sidebar" data-mh="product-listing">
          <aside class="ps-widget--sidebar ps-widget--category">
            <div class="ps-widget__header">
              <h3>CONDITION</h3>
            </div>
            <div class="ps-widget__content">
              <ul class="ps-list--checked">
                @foreach($categories as $category)
                <li class="">
                    @if(!empty(request('category')))
                      @if(count($top_subCategory[$category->id]) > 0)
                        <div class="has-sub-category" data-filter="{{ $category->category_name }}">
                            <span style="font-size: 15px; font-weight: bold;">
                              {{ $category->category_name }} <i class="fa fa-angle-right arrow-right" aria-hidden="true"></i>
                            </span>
                        </div>
                        @foreach($top_subCategory[$category->id] as $tsc)
                        <div class="form-group sub-category-child">
                            <a href="{{ route('listing', ['sub_category='.urlencode($tsc->sub_category_name),
                                                          'category='.urlencode($category->category_name),
                                                          'brand='.request('brand'),
                                                          'result='.request('result')]) }}" class="{{ (request('sub_category') == $tsc->sub_category_name) ? 'current' : '' }}">
                                {{ $tsc->sub_category_name }}
                            </a>
                        </div>
                        @endforeach
                      @else
                        <a href="{{ route('listing', ['category=',
                                                    'brand='.request('brand'),
                                                    'result='.request('result')]) }}" class="{{ (request('category') == $category->category_name) ? 'current' : '' }}">
                          {{ $category->category_name }}
                        </a>
                      @endif
                    @else
                      @if(count($top_subCategory[$category->id]) > 0)
                        <div class="has-sub-category">
                            <span style="font-size: 15px; font-weight: bold;">
                              {{ $category->category_name }} <i class="fa fa-angle-right arrow-right" aria-hidden="true"></i>
                            </span>
                        </div>
                        @foreach($top_subCategory[$category->id] as $tsc)
                        <div class="form-group sub-category-child">
                            <a href="{{ route('listing', ['sub_category='.urlencode($tsc->sub_category_name),
                                                          'category='.urlencode($category->category_name),
                                                          'brand='.request('brand'),
                                                          'result='.request('result')]) }}">
                                {{ $tsc->sub_category_name }}
                            </a>
                        </div>
                        @endforeach
                      @else
                        <a href="{{ route('listing', ['category='.urlencode($category->category_name),
                                                      'brand='.request('brand'),
                                                      'result='.request('result')]) }}">
                            {{ $category->category_name }}
                        </a>
                      @endif
                    @endif
                </li>
                @endforeach
              </ul>
            </div>
          </aside>
          @if(!$brands->isEmpty())
          <aside class="ps-widget--sidebar ps-widget--category">
            <div class="ps-widget__header">
              <h3>VITAMINS & SUPPLEMENTS</h3>
            </div>
            <div class="ps-widget__content">
              <ul class="ps-list--checked">
                @foreach($brands as $brand)
                <li class="{{ (request('brand') == $brand->brand_name) ? 'current' : '' }}">
                    @if(!empty(request('brand')))
                      <a href="{{ route('listing', ['brand=',
                                                    'category='.request('category'),
                                                    'result='.request('result')]) }}">
                          {{ $brand->brand_name }}
                      </a>
                    @else
                      <a href="{{ route('listing', ['brand='.urlencode($brand->brand_name),
                                                    'category='.request('category'),
                                                    'result='.request('result')]) }}">
                          {{ $brand->brand_name }}
                      </a>                    
                    @endif
                </li>
                @endforeach
              </ul>
            </div>
          </aside>
          @endif
        </div>
      </div>
@endsection

@section('js')
<script type="text/javascript">
  $('.has-sub-category').click( function(e){
      e.preventDefault();
      var ele = $(this);

      ele.parent().find('.arrow-right').toggleClass('fa-angle-down');
      ele.parent().find('.sub-category-child').slideToggle('fast', function(){});
  });
</script>

@if(!empty(request('category')))
<script type="text/javascript">
    var categoryS = "{{ urldecode(request('category')) }}";
    categoryS = categoryS.replace('&amp;', '&');
    $(document).ready(function() {
        $(window).on('load', function() {
            $('.has-sub-category').filter(function(){return $(this).data('filter')==categoryS}).click();
        });
    });
</script>
@endif
@endsection