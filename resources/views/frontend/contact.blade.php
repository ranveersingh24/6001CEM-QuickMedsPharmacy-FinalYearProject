@extends('layouts.app')
<style type="text/css">
    @import url(https://fonts.googleapis.com/css?family=Raleway:300);
@import url(https://fonts.googleapis.com/css?family=Lusitana:400,700);
.align-center {
  text-align: center;
}

html {
  height: 100%;
}

body {
  height: 100%;
  position: relative;
}

.row {
  margin: -20px 0;
}
.row:after {
  content: "";
  display: table;
  clear: both;
}
.row .col {
  padding: 0 20px;
  float: left;
  box-sizing: border-box;
}
.row .col.x-50 {
  width: 50%;
}
.row .col.x-100 {
  width: 100%;
}

.content-wrapper {
  min-height: 100%;
  position: relative;
}

.get-in-touch {
  max-width: 650px;
  margin: 0 auto;
  position: relative;
  top: 50%;
  
}
.get-in-touch .title {
  text-align: center;
  font-family: Raleway, sans-serif;
  text-transform: uppercase;
  letter-spacing: 3px;
  font-size: 36px;
  font-weight: bold;
  line-height: 48px;
  padding-bottom: 80px;
}

.contact-form .form-field {
  position: relative;
  margin: 32px 0;
}
.contact-form .input-text {
  display: block;
  width: 100%;
  height: 40px;
  border-width: 0 0 8px 0;
  border-color: blue;
  font-family: Lusitana, serif;
  font-size: 20px;
  line-height: 26px;
  font-weight: 400;
  border: 3px solid #555;
}
.contact-form .input-text:focus {
  background-color: darkgreen;
  padding-left: 40px;
  color: white;
  outline: blue;
}
.contact-form .input-text:focus + .label, .contact-form .input-text.not-empty + .label {
  -webkit-transform: translateY(-24px);
          transform: translateY(-24px);
          color: limegreen;
          font-weight: bold;
}
.contact-form .label {
  position: absolute;
  left: 20px;
  bottom: 30px;
  font-family: Lusitana, serif;
  font-size: 24px;
  line-height: 26px;
  font-weight: 400;
  color: #000000;
  cursor: text;
  -webkit-transition: -webkit-transform .2s ease-in-out;
  transition: -webkit-transform .2s ease-in-out;
  transition: transform .2s ease-in-out;
  transition: transform .2s ease-in-out, -webkit-transform .2s ease-in-out;
}
.contact-form .submit-btn {
  display: inline-block;
  font-weight: bold;
  background-color: forestgreen;
  color: #fff;
  font-family: Raleway, sans-serif;
  text-transform: uppercase;
  letter-spacing: 2px;
  font-size: 16px;
  line-height: 24px;
  padding: 15px 25px;
  border: none;
  cursor: pointer;
  margin: 28px 2px;
}

.note {
  position: absolute;
  left: 0;
  bottom: 10px;
  width: 100%;
  text-align: center;
  font-family: Lusitana, serif;
  font-size: 16px;
  line-height: 21px;
}
.note .link {
  color: #888;
  text-decoration: none;
}
.note .link:hover {
  text-decoration: underline;
}

</style>
@section('content')
<div class="row">
<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3972.4711039665854!2d100.29977712534757!3d5.344824484419841!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x304ac0ff6590ebc9%3A0x39c97ee6394e43e9!2sDesa%20Bistari%20Apartments%20(A%2FB%20block)!5e0!3m2!1sen!2smy!4v1632367400141!5m2!1sen!2smy" width="600" height="350" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
 </div>

 
 <br>
  <br>
  <section class="get-in-touch">
   <h1 class="title">Get in touch</h1>
   <form method="POST" action="{{ route('Contact') }}"class="contact-form row">
    @csrf
      <div class="form-field col x-50">
         <input id="name" name="user_name"  class="input-text js-input" type="text" required>
         <label class="label" for="name">Name</label>
      </div>
      <div class="form-field col x-50">
         <input id="email" name="user_mail" class="input-text js-input" type="email" required>
         <label class="label" for="email">E-mail</label>
      </div>
      <div class="form-field col x-100">
         <input id="message" name="user_feedback" class="input-text js-input" type="text" required>
         <label class="label" for="message">Message</label>
      </div>
      <div class="form-field col x-100 align-center">
         <input class="submit-btn" type="submit" value="Submit">
      </div>
   </form>
</section>
  </div>

@endsection

@section('js')

<script type="text/javascript">
    $( '.js-input' ).keyup(function() {
  if( $(this).val() ) {
     $(this).addClass('not-empty');
  } else {
     $(this).removeClass('not-empty');
  }
});
function initMap() {
    var uluru = {
        lat: 1.4949852,
        lng: 103.7662106
    };

    var map = new google.maps.Map(document.getElementById('map'), {
        center: {
            lat: 1.4949852,
            lng: 103.7662106
        },
        zoom: 13,
        scrollwheel: false
    });

    var myLatlng = new google.maps.LatLng('1.4944424', '103.7670022');

    var marker = new google.maps.Marker({
        position: myLatlng,
        map: map,
        title: 'Topten Plus'
    });
}
</script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDOOHXs-s61j0lEX-hXu2-1MrJTb7hV8zU&amp;callback=initMap"></script>
@endsection