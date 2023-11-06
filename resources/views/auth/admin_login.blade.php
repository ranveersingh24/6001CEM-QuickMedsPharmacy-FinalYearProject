<!DOCTYPE html>
<html lang="en">
<style>
body {
 
  padding: 150px;

}
</style>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Quick Meds Pharmacy Admin Login</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="../../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
</head>

<body class="hold-transition login-page">
<div align="center">
  <div class="login-logo">
    <div class="center">
        <h1>
           
                <span class="red">Quick Meds Pharmacy Management System</span>
        </h1>
                <h4 class="blue" id="id-company-text">&copy; Quick Meds Pharmacy</h4>
    </div>
  </div>

  <!-- /.login-logo -->
  <div align="left"class="card">
    <div class="card-body login-card-body">
      <h4 align="center" class="header blue lighter bigger">
            <i class="ace-icon fa fa-coffee green"></i>
                    &nbsp;Please Enter Your Information
        </h4>
   <hr>

    <div class="form-group">
        @if($errors->any())
        <div class="alert alert-danger">{!! implode('<br/>', $errors->all(':message')) !!}</div>
            @endif
        </div>

    <form method="POST" action="{{ route('admin_login') }}">
        @csrf
    <fieldset>
        <div class="input-group mb-3">
          <input type="email" class="form-control" name="email" placeholder="Email">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        
        <div class="input-group mb-3">
          <input type="password" class="form-control"name="password" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember">
              <label for="remember">
                Remember Me
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-4">

           <button type="xbutton" class="btn-block btn btn-sm btn-primary">
                                                            <i classx="ace-icon fa fa-key"></i>
                                                            <span cxlass="bigger-110">Login</span>
         </button>
          </div>
          <!-- /.col -->
        </div>
        </fieldset>
        </form>

      
    
 
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->


         


       

        <!-- inline scripts related to this page -->
        <script type="text/javascript">
            jQuery(function($) {
             $(document).on('click', '.toolbar a[data-target]', function(e) {
                e.preventDefault();
                var target = $(this).data('target');
                $('.widget-box.visible').removeClass('visible');//hide others
                $(target).addClass('visible');//show target
             });
            });
            
            
            
            //you don't need this, just used for changing background
            jQuery(function($) {
             $('#btn-login-dark').on('click', function(e) {
                $('body').attr('class', 'login-layout');
                $('#id-text2').attr('class', 'white');
                $('#id-company-text').attr('class', 'blue');
                
                e.preventDefault();
             });
             $('#btn-login-light').on('click', function(e) {
                $('body').attr('class', 'login-layout light-login');
                $('#id-text2').attr('class', 'grey');
                $('#id-company-text').attr('class', 'blue');
                
                e.preventDefault();
             });
             $('#btn-login-blur').on('click', function(e) {
                $('body').attr('class', 'login-layout blur-login');
                $('#id-text2').attr('class', 'white');
                $('#id-company-text').attr('class', 'light-blue');
                
                e.preventDefault();
             });
             
             $('#btn-login-light').trigger('click');
            });
        </script>

<script src="assets/js/jquery-2.1.4.min.js"></script>
<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>
 <script type="text/javascript">
            if('ontouchstart' in document.documentElement) document.write("<script src='{{ asset('assets/js/jquery.mobile.custom.min.js') }}'>"+"<"+"/script>");
        </script>
    </body>
</html>
