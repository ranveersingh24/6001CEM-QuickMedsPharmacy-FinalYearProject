 <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="{{ route('dashboard.dashboards.index') }}" class="nav-link">Home</a>
      </li>
    </ul>



    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
     
      
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
    </ul>
    <div class="nav-item">
         <a class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"role="button" >
          <i class="fas fa-sign-out-alt"></i>
          <form id="logout-form" action="{{ route('admin_logout') }}" method="POST" style="display: none;">
            @csrf
          </form>
            </a>
          </div>  
  </nav>

  <!-- /.navbar -->
