<!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4 ">
    <!-- Brand Logo -->
    <a href="{{ route('admin.admins.index') }}" class="brand-link">
      @if(!empty($data['website_logo']))
          <img class="img-circle elevation-2"src="{{ url($data['website_logo']) }}" style="width: 50px;">
          @else
          <img class="img-circle elevation-2"src="{{ url('images/logo/Vesson_Enterprise_Trans_Gold.png') }}" style="width: 30px;">
          @endif
         &nbsp;
      <span class="brand-text font-weight-light">
       @if(!empty($data['website_name']))
          {{ $data['website_name'] }} Backend
          @else
          Quick Meds Pharmacy Backend
          @endif
    </span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
           @if(!empty(Auth::user()->profile_logo))
            <img class="img-circle elevation-2"  src="{{ url(Auth::user()->profile_logo) }}" />
            @else
            <img class="img-circle elevation-2" src="{{ url('images/images.png') }}" />
            @endif
        </div>
        <div class="info">
          <a href="#" class="d-block">{{ Auth::user()->f_name }} {{ Auth::user()->l_name }}</a>
        </div>
      </div>



      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-3 ">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">


      @foreach($data['permission'] as $key => $value)
        @if(Auth::guard('admin')->check())
           <li class="{{ (Request::segment(1) == 'dashboards') ? 'menu-open' : '' }} nav-item">
            <a href="{{ route('dashboard.dashboards.index') }}" class="nav-link {{ (Request::segment(1) == 'dashboards') ? 'active' : '' }} ">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
      </li>
         @endif

         @if(isset($value[$permission_level]['profile']) && $value[$permission_level]['profile'] == 1)
        
          <li class="{{ (Request::segment(1) == 'admins')  ? 'menu-open' : '' }} nav-item">
            <a href="{{ route('admin.admins.index') }}" class="nav-link {{ (Request::segment(1) == 'admins')  ? 'active' : '' }}">
              <i class="nav-icon fa fa-user"></i>
           @if(Auth::guard('admin')->check())
            <p>Company Profile</p>
            @else
            <p>Profil</p>
            @endif
            </a>
      </li>
        @endif

       

       

    <li class="{{ (Request::segment(1) == 'members' || Request::segment(1) == 'tree' || Request::segment(1) == 'tree_details') ? 'menu-open' : '' }} nav-item">
            <a href="#" class="nav-link {{ (Request::segment(1) == 'members' || Request::segment(1) == 'tree' || Request::segment(1) == 'tree_details') ? 'active' : '' }}">
              <i class="nav-icon fa fa-users"></i>
              <p>
                Member Management 
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>

            <ul class="nav nav-treeview">

                <li class="nav-item">
              <a href="{{ route('member.members.index') }}" class="nav-link {{ (Request::segment(1) == 'members' && (Request::segment(2) == '' || Request::segment(3) == 'edit' || Request::segment(1) == 'tree' || Request::segment(1) == 'tree_details')) ? 'active' : '' }}">
             <i class="nav-icon far fa-circle text-info"></i>
                  <p>Member List</p>
                </a>
              </li>

              
            </ul>
         
         @if((isset($value[$permission_level]['product-list']) && $value[$permission_level]['product-list'] == 1) || 
                (isset($value[$permission_level]['product-add']) && $value[$permission_level]['product-add'] == 1) ||
                (isset($value[$permission_level]['point-product-list']) && $value[$permission_level]['point-product-list'] == 1) || 
                (isset($value[$permission_level]['point-product-add']) && $value[$permission_level]['point-product-add'] == 1))
        <li class="{{ (Request::segment(1) == 'products' || Request::segment(1) == 'point_malls') ? 'menu-open' : '' }} nav-item">
            <a href="#" class="nav-link {{ (Request::segment(1) == 'products' || Request::segment(1) == 'point_malls') ? 'active' : '' }}">
              <i class="nav-icon fa fa-cubes"></i>
              <p>
                Medicine Management
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>

            @if(isset($value[$permission_level]['product-list']) && $value[$permission_level]['product-list'] == 1)
            <ul class="nav nav-treeview">

           
                <li class="nav-item">
            <a href="{{ route('product.products.index') }}" class="nav-link {{ (Request::segment(1) == 'products' && (Request::segment(2) == '' || Request::segment(3) == 'edit' || Request::segment(3) == 'stock')) ? 'active' : '' }}">
             <i class="nav-icon far fa-circle text-info"></i>
                  <p>Medicine List</p>
                </a>
              </li>

              @endif

              @if(isset($value[$permission_level]['product-add']) && $value[$permission_level]['product-add'] == 1)
              <li class="nav-item">
            <a href="{{ route('product.products.create') }}" class="nav-link {{ (Request::segment(1) == 'products' && Request::segment(2) == 'create') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle text-info"></i>
                  <p> Add New Medicine</p>
                </a>
              </li>
              @endif     
            </ul>

      </li>
         @endif

         @if((isset($value[$permission_level]['category-list']) && $value[$permission_level]['category-list'] == 1) || 
                (isset($value[$permission_level]['category-list']) && $value[$permission_level]['category-add'] == 1))
          <li class="{{ (Request::segment(1) == 'categories') ? 'menu-open' : '' }} nav-item">
            <a href="#" class="nav-link {{ (Request::segment(1) == 'categories') ? 'active' : '' }}">
              <i class="nav-icon fa fa-tag"></i>
              <p>
                Condition Management
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>

            <ul class="nav nav-treeview">
            @if(isset($value[$permission_level]['category-list']) && $value[$permission_level]['category-list'] == 1)

                <li class="nav-item">
            <a href="{{ route('category.categories.index') }}"class="nav-link {{ (Request::segment(1) == 'categories' && (Request::segment(2) == '' || Request::segment(3) == 'edit' || Request::segment(3) == 'stock')) ? 'active' : '' }}">
                <i class="nav-icon far fa-circle text-info"></i>
                  <p>Condition List</p>
                </a>
              </li>
              @endif

              @if(isset($value[$permission_level]['category-add']) && $value[$permission_level]['category-add'] == 1)
              <li class="nav-item">
            <a href="{{ route('category.categories.create') }}" class="nav-link {{ (Request::segment(1) == 'categories' && Request::segment(2) == 'create') ? 'active' : '' }}">
               <i class="nav-icon far fa-circle text-info"></i>
                  <p> Add New Condition</p>
                </a>
              </li>
              @endif
            </ul>
      </li>
         @endif

          

         @if((isset($value[$permission_level]['brand-list']) && $value[$permission_level]['brand-list'] == 1) || 
                (isset($value[$permission_level]['brand-add']) && $value[$permission_level]['brand-add'] == 1))
         <li class="{{ (Request::segment(1) == 'brands') ? 'menu-open' : '' }} nav-item">
            <a href="#" class="nav-link {{ (Request::segment(1) == 'brands') ? 'active' : '' }}">
              <i class="nav-icon fa fa-cube"></i>
              <p>
               Varieties Management
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>

            <ul class="nav nav-treeview">
            @if(isset($value[$permission_level]['brand-list']) && $value[$permission_level]['brand-list'] == 1)

                <li class="nav-item">
            <a href="{{ route('brand.brands.index') }}"class="nav-link {{ (Request::segment(1) == 'brands' && (Request::segment(2) == '' || Request::segment(3) == 'edit')) ? 'active' : '' }}">
                <i class="nav-icon far fa-circle text-info"></i>
                  <p> Varieties List</p>
                </a>
              </li>

              @endif

            @if(isset($value[$permission_level]['brand-add']) && $value[$permission_level]['brand-add'] == 1)
              <li class="nav-item">
            <a href="{{ route('brand.brands.create') }}"class="nav-link {{ (Request::segment(1) == 'brands' && Request::segment(2) == 'create') ? 'active' : '' }}">

                <i class="nav-icon far fa-circle text-info"></i>
                  <p> Add New Varieties</p>
                </a>
              </li>
              @endif
            </ul>
      </li>
         @endif

       


        
        

        @if(isset($value[$permission_level]['shipping-fee']) && $value[$permission_level]['shipping-fee'] == 1)
          <li class="{{ (Request::segment(1) == 'setting_shipping_fee'||Request::segment(1) == 'setting_customer_feedback'||Request::segment(1) == 'setting_banner') ? 'menu-open ' : '' }} nav-item">
            <a href="#" class="nav-link {{ (Request::segment(1) == 'setting_shipping_fee' ||Request::segment(1) == 'setting_customer_feedback'||
             Request::segment(1) == 'setting_banner') ? 'active ' : '' }}">
              <i class="nav-icon fa fa-cogs"></i>
              <p>
            Settings
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>

            <ul class="nav nav-treeview">
               
                  <li class="nav-item">
                <a href="{{ route('setting_banner') }}"class="nav-link {{ (Request::segment(1) == 'setting_banner') ? 'active' : '' }}">
                    <i class="nav-icon far fa-circle text-info"></i>
                      <p>Setting Banner</p>
                    </a>
                  </li>


              @if(isset($value[$permission_level]['shipping-fee']) && $value[$permission_level]['shipping-fee'] == 1)
             
                    <li class="nav-item">
                        <a href="{{ route('setting_shipping_fee') }}"class="nav-link {{ (Request::segment(1) == 'setting_shipping_fee') ? 'active' : '' }}">
                            <i class="nav-icon far fa-circle text-info"></i>
                            <p>Shipping Fees</p>
                        </a>
                    </li>

            @endif 


              @if(isset($value[$permission_level]['shipping-fee']) && $value[$permission_level]['shipping-fee'] == 1)
             
                    <li class="nav-item">
                        <a href="{{ route('setting_customer_feedback') }}"class="nav-link {{ (Request::segment(1) == 'setting_customer_feedback') ? 'active' : '' }}">
                            <i class="nav-icon far fa-circle text-info"></i>
                            <p>Customers Feedbacks</p>
                        </a>
                    </li>

            @endif 

            </ul>

          </li>
      @endif
        @endforeach
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->