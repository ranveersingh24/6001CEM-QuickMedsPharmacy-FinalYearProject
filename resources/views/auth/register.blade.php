@extends('layouts.app')
@section('css')
<style type="text/css">
    .cat_menu_container ul {
        display: block;
        position: absolute;
        top: 100%;
        left: 0;
        visibility: hidden;
        opacity: 0;
        min-width: 100%;
        background: #FFFFFF;
        box-shadow: 0px 10px 25px rgba(0,0,0,0.1);
        -webkit-transition: opacity 0.3s ease;
        -moz-transition: opacity 0.3s ease;
        -ms-transition: opacity 0.3s ease;
        -o-transition: opacity 0.3s ease;
        transition: all 0.3s ease;
    }
    .cat_menu_container:hover .cat_menu {
        visibility: visible;
        opacity: 1;
    }

    .select2-container{
        width: 100% !important;
    }

    .select2-container--default .select2-selection--single{
        padding: 5px;
        height: 39px;
    }

    .nice-select{
        display: none;
    }
</style>
@endsection
@section('content')
<!------ Include the above in your HEAD tag ---------->

<div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100">
              

              <form class="login100-form validate-form" method="POST" action="{{ route('register') }}" id="register-form">
                @csrf
                    <span class="login100-form-title">
                        <b style="color:white;">Member Registration</b>
                    </span>
                    <div class="form-group">
                        @if($errors->any())
                          <div class="alert alert-danger">{!! implode('<br/>', $errors->all(':message')) !!}</div>
                        @endif
                        <input type="hidden" name="role" value="1">
                    </div>

                    <div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
                    <input type="text" class="input100" placeholder="First Name" name="f_name" value="{{ old('f_name') }}">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-user-circle" aria-hidden="true"></i>
                        </span>
                    </div>


                    <div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
                        <input type="text" class="input100" placeholder="Last Name" name="l_name" value="{{ old('l_name') }}">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-user-circle" aria-hidden="true"></i>
                        </span>
                    </div>

                    <div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
                        <input type="text" class="input100" placeholder="Email" name="email" value="{{ old('email') }}">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-envelope" aria-hidden="true"></i>
                        </span>
                    </div>
                    
                    <div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
                        <select class="input100" name="country_code" id="country_code" data-live-search="true">
                                
                                    <option value="60">(+60) Malaysia</option>
                                    <option value="65">(+65) Singapore</option>
                        </select>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-phone" aria-hidden="true"></i>
                        </span>
                    </div>

                    <div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
                        <input type="text" class="input100" placeholder="Example: 0171234567" name="phone" value="{{ old('phone') }}"  onkeypress="return isNumberKey(event)">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-phone" aria-hidden="true"></i>
                        </span>
                    </div>


                    <div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
                       <input type="password" class="input100"  placeholder="Password" name="password">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-key" aria-hidden="true"></i>
                        </span>
                    </div>

                    <div class="wrap-input100 validate-input" data-validate = "Password is required">
                       <input type="password" class="input100"  placeholder="Confirm Password" name="password_confirmation">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-key" aria-hidden="true"></i>
                        </span>
                    </div>

                    <div class="container-login100-form-btn">
                        <button class="login100-form-btn login-submit-button register-btn">
                             SIGN UP
                        </button>
                    </div>

                    <div class="text-center p-t-136">
                        <a class="txt2" href="{{ route('login') }}">
                     <b style="color:white;">Already have an account? </b>
                            <b style="color:white;"><i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i></b>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('js')
<script type="text/javascript">
   

    $('#register-form .required-feild').change( function(){
        if($(this).val()){
            $(this).removeClass('required-feild-error');
        }
    });

    $('.register-btn').click( function(e){
       e.preventDefault();

       // $('input[name="password"]').val(phone);
       
          $('#register-form').submit();        
       
   
    });
</script>
@endsection