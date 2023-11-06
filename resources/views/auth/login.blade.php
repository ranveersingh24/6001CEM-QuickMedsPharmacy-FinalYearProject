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
</style>
@endsection
@section('content')
<!------ Include the above in your HEAD tag ---------->

<div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100">
               

               <form class="login100-form validate-form"method="POST" action="{{ route('login') }}" id="login-form">
                @csrf
                    <span class="login100-form-title">
                        <b style="color:white;">Member Login</b>
                    </span>

                    <div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
                        <input class="input100" type="email" name="email" placeholder="Email">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-envelope" aria-hidden="true"></i>
                        </span>
                    </div>

                    <div class="wrap-input100 validate-input" data-validate = "Password is required">
                        <input class="input100" type="password" name="password" placeholder="Password">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-lock" aria-hidden="true"></i>
                        </span>
                    </div>

                    <div class="container-login100-form-btn">
                        <button class="login100-form-btn login-submit-button login-btn">
                            Login
                        </button>
                    </div>

                    <div class="text-center p-t-136">
                        <a class="txt2" href="{{ route('register') }}">
                           <b style="color:white;">Create your Account</b>
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
    $('.button-inside').on('click', '.get-verify-code-btn', function(e){
        e.preventDefault();
        $('.loading-gif').show();
        var ele = $(this);
        var phone = $('input[name="phone"]').val();
        if(phone.length < 10){
            alert("Please enter a valid mobile phone number");
            $('.loading-gif').hide();
            return false;
        }

        var fd = new FormData();
        fd.append('phone', phone);

        $.ajax({
            url: '{{ route("getVerifyCode") }}',
            type: 'post',
            data: fd,
            contentType: false,
            processData: false,
            success: function(response){
             alert(Welcome Back);
                // return false;
                
            },
        });
    });


    $('.login-btn').click( function(e){
       e.preventDefault();
       $('#login-form').submit();
       // $('.loading-gif').show();
       // // $('input[name="password"]').val(phone);
       // // $('#login-form').submit();
       // var phone = $('input[name="phone"]').val();
       // var code = $('input[name="code"]').val();
       // var country_code = $('.country_code').val();

       // if(!phone){
       //      $('#action-return-message').addClass('important-text');
       //      $('#action-return-message').html("Please enter phone number");
       //      $('.loading-gif').hide();
       //      return false;
       // }else{
       //    if(phone.length < 10){
       //          $('#action-return-message').addClass('important-text');
       //          $('#action-return-message').html("Please enter a valid mobile phone number");
       //          $('.loading-gif').hide();
       //          return false;
       //    }
       // }

       // if(!code){
       //      $('#action-return-message').addClass('important-text');
       //      $('#action-return-message').html("Please enter a valid verification code");
       //      $('.loading-gif').hide();
       //      return false;
       // }



       // var fd = new FormData();
       // fd.append('phone', phone);
       // fd.append('code', code);
       // fd.append('country_code', country_code);

       // $.ajax({
       //      url: '{{ route("CheckLogin") }}',
       //      type: 'post',
       //      data: fd,
       //      contentType: false,
       //      processData: false,
       //      success: function(response){
       //          // alert(response);
       //          if(response == 1){
       //              $('#action-return-message').html("Verification code error");
       //              $('#action-return-message').addClass('important-text');
       //              $('.loading-gif').hide();
       //              return false;
       //          }else if(response == 2){
       //              $('#action-return-message').html("Phone number does not exist");
       //              $('#action-return-message').addClass('important-text');
       //              $('.loading-gif').hide();
       //          }else{
       //              // $('input[name="password"]').val(phone);
       //              $('#login-form').submit();
       //          }
       //      },
       //  }); 
    });
</script>
@endsection