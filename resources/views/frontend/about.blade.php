@extends('layouts.app')
@section('content')
<style>
  /* :: 11.0 About CSS */
.about-us-content h3 {
  font-size: 36px;
  display: block;
  margin-bottom: 5px; }
  @media only screen and (max-width: 767px) {
    .about-us-content h3 {
      font-size: 30px; } }
.about-us-content .line {
  height: 3px;
  width: 70px;
  background-color: red;
  margin-bottom: 50px; }

.about-video-area {
  position: relative;
  z-index: 1;
  border-radius: 10px; }
  .about-video-area img {
    -webkit-box-shadow: 3px 0 24px rgba(0, 0, 0, 0.15);
    box-shadow: 3px 0 24px rgba(0, 0, 0, 0.15);
    width: 100%;
    border-radius: 10px; }
  .about-video-area .video-icon a {
    position: absolute;
    top: 50%;
    left: 50%;
    z-index: 99;
    -webkit-transform: translate(-50%, -50%);
    -ms-transform: translate(-50%, -50%);
    transform: translate(-50%, -50%);
    color: #ffffff;
    height: 50px;
    width: 50px;
    display: inline-block;
    background-color: #fc6060;
    text-align: center;
    line-height: 56px;
    font-size: 38px;
    border-radius: 50%; }
    .about-video-area .video-icon a::after {
      content: '';
      position: absolute;
      width: 0%;
      height: 0%;
      border-radius: 50%;
      background-color: transparent;
      top: 50%;
      left: 50%;
      -webkit-transform: translate(-50%, -50%);
      -ms-transform: translate(-50%, -50%);
      transform: translate(-50%, -50%);
      border: 2px solid #fc6060;
      -webkit-animation: video-icon 1200ms linear infinite;
      animation: video-icon 1200ms linear infinite;
      z-index: -10; }
    .about-video-area .video-icon a:hover, .about-video-area .video-icon a:focus {
      background-color: #000000; }
      .about-video-area .video-icon a:hover::after, .about-video-area .video-icon a:focus::after {
        border: 2px solid #000000; }

@-webkit-keyframes video-icon {
  0% {
    width: 0;
    height: 0;
    opacity: 1; }
  50% {
    width: 100%;
    height: 100%;
    opacity: 1; }
  100% {
    width: 200%;
    height: 200%;
    opacity: 0; } }
@keyframes video-icon {
  0% {
    width: 0;
    height: 0;
    opacity: 1; }
  50% {
    width: 100%;
    height: 100%;
    opacity: 1; }
  100% {
    width: 200%;
    height: 200%;
    opacity: 0; } }
.why-choose-us-content {
  -webkit-transition-duration: 800ms;
  -o-transition-duration: 800ms;
  transition-duration: 800ms;
  background-color: #ffffff;
  padding: 80px 30px 50px;
  border-radius: 10px;
  box-shadow: 3px 0 15px -1px rgba(0, 0, 0, 0.15);
  position: relative;
  z-index: 1;
  margin-top: 50px; }
  .why-choose-us-content .chosse-us-icon {
    -webkit-transition-duration: 800ms;
    -o-transition-duration: 800ms;
    transition-duration: 800ms;
    height: 76px;
    width: 76px;
    background-color: darkred;
    display: inline-block;
    text-align: center;
    line-height: 76px;
    border-radius: 50%;
    color: #ffffff;
    font-size: 50px;
    position: absolute;
    top: -38px;
    left: 50%;
    margin-left: -38px; }
  .why-choose-us-content h4 {
    font-size: 20px;
    font-weight: 500;
    margin-bottom: 15px;
    -webkit-transition-duration: 800ms;
    -o-transition-duration: 800ms;
    transition-duration: 800ms; }
  .why-choose-us-content p {
    -webkit-transition-duration: 800ms;
    -o-transition-duration: 800ms;
    transition-duration: 800ms;
    font-size: 14px;
    margin-bottom: 0; }
  .why-choose-us-content:hover, .why-choose-us-content:focus {
    background-color: #f79caf;}
    .why-choose-us-content:hover .chosse-us-icon, .why-choose-us-content:focus .chosse-us-icon {
      background-color: #000000;
      box-shadow: 0 5px 15px 2px rgba(255, 255, 255, 0.15); }
    .why-choose-us-content:hover h4,
    .why-choose-us-content:hover p, .why-choose-us-content:focus h4,
    .why-choose-us-content:focus p {
      color: #ffffff; }
      /* :: 14.0 Team CSS */
.team-content-area {
  position: relative;
  z-index: 1;
  padding: 40px;
  -webkit-box-shadow: 0 4px 20px 3px rgba(0, 0, 0, 0.1);
  box-shadow: 0 4px 20px 3px rgba(0, 0, 0, 0.1);
  border-radius: 10px;
  text-align: center;
  -webkit-transition-duration: 500ms;
  -o-transition-duration: 500ms;
  transition-duration: 500ms;
  background-color: #fabbc8; }
  .team-content-area .member-thumb {
    margin: 0 auto 40px;
    width: 165px; }
    .team-content-area .member-thumb img {
      
      width: 100%;
      border-radius: 50%; }
  .team-content-area h5 {
    
    font-size: 20px;
    font-weight: 500;
    margin-bottom: 0; }
  .team-content-area span {
    
    color: #fc6060;
    font-size: 12px;
    text-transform: uppercase; }
  .team-content-area .member-social-info {
    position: relative;
    z-index: 1;
    margin-top: 30px; }
    .team-content-area .member-social-info a {
      color: #636363;
      font-size: 16px;
      margin: 0 10px;
       }
      .team-content-area .member-social-info a:hover, .team-content-area .member-social-info a:focus {
        color: #fabbc8; }
  .team-content-area:hover, .team-content-area:focus {
    background-color: #f79caf; }
    .team-content-area:hover .member-thumb img, .team-content-area:focus .member-thumb img {
      }
    .team-content-area:hover h5,
    .team-content-area:hover span, .team-content-area:focus h5,
    .team-content-area:focus span {
      color: #ffffff; }
    .team-content-area:hover .member-social-info a, .team-content-area:focus .member-social-info a {
      color: #ffffff; }
      .team-content-area:hover .member-social-info a:hover, .team-content-area:hover .member-social-info a:focus, .team-content-area:focus .member-social-info a:hover, .team-content-area:focus .member-social-info a:focus {
        color: #000000; }
        /* :: 3.0 Preloader CSS */
#preloader {
  position: fixed;
  width: 100%;
  height: 100%;
  z-index: 9999999;
  top: 0;
  left: 0;
  background-color: #ffffff;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  -webkit-box-pack: center;
  -ms-flex-pack: center;
  justify-content: center;
  overflow: hidden; }

.loader,
.loader:before,
.loader:after {
  border-radius: 50%;
  width: 2.5em;
  height: 2.5em;
  -webkit-animation-fill-mode: both;
  animation-fill-mode: both;
  -webkit-animation: load7 1.8s infinite ease-in-out;
  animation: load7 1.8s infinite ease-in-out; }

.loader {
  margin-top: -20px;
  color: #fc6060;
  font-size: 6px;
  position: relative;
  -webkit-transform: translateZ(0);
  -ms-transform: translateZ(0);
  transform: translateZ(0);
  -webkit-animation-delay: -0.16s;
  animation-delay: -0.16s; }

.loader:before,
.loader:after {
  content: '';
  position: absolute;
  top: 0; }

.loader:before {
  left: -3.5em;
  -webkit-animation-delay: -0.32s;
  animation-delay: -0.32s; }

.loader:after {
  left: 3.5em; }

@-webkit-keyframes load7 {
  0%,
    80%,
    100% {
    box-shadow: 0 2.5em 0 -1.3em; }
  40% {
    box-shadow: 0 2.5em 0 0; } }
@keyframes load7 {
  0%,
    80%,
    100% {
    box-shadow: 0 2.5em 0 -1.3em; }
  40% {
    box-shadow: 0 2.5em 0 0; } }
</style>

    <!-- Breadcrumb Area End -->
 <!-- About Us  Start -->
 <section id="about" class="about">
      <div class="container">

        <div class="row">
          <div class="col-lg-6">
            <img src="images/about.png" class="img-fluid" alt="">
          </div>
    <div class="about-us-area section-padding-80-0 clearfix">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 col-lg-6">
                    <div class="about-us-content mb-80">
                        <h3 class="wow fadeInUp" data-wow-delay="100ms">Quick Meds Pharmacy</h3>
                        <div class="line wow fadeInUp" data-wow-delay="200ms"></div>
                        <p class="wow fadeInUp" data-wow-delay="300ms">Why Use Quick Meds Pharmacy? In addition to providing improved access, cheaper transaction and product costs, convenience, and greater client privacy, Quick Meds pharmacies provide better pricing than physical storefronts, and also accessibility for those who prefer to not have physical contact with anyone.</p>
                        <p class="wow fadeInUp" data-wow-delay="400ms">The pharmacy distributes variety of different types of non-prescription medicines as well as vitamins and supplements to consumers after selling them online. Through the internet, users may place their desired orders and have their medications delivered at their front door. If you have any questions, don't hesitate to contact us.</p>
                    </div>
                </div>
                
</iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About Us Area End -->

    <!-- Why Choose Ace Abstract Start -->
    <section class="why-choose-us-area bg-gray section-padding-80-0 clearfix">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-heading text-center wow fadeInUp" data-wow-delay="100ms">
                        <h2>Why Choose Quick Meds Pharmacy</h2>
                    </div>
                </div>
            </div>

            <div class="row">
                <!--  Why Choose Ace Abstract (High Quality Artwork) -->
                <div class="col-md-6 col-lg-4">
                    <div class="why-choose-us-content text-center mb-80 wow fadeInUp" data-wow-delay="100ms">
                        <div class="chosse-us-icon">
                        <i class="fa fa-shield" aria-hidden="true"></i>

                        </div>
                        <h4>Trusted Security</h4>
                        
                    </div>
                </div>

                <!--  Why Choose Ace Abstract (Chat With Us) -->
                <div class="col-md-6 col-lg-4">
                    <div class="why-choose-us-content text-center mb-80 wow fadeInUp" data-wow-delay="300ms">
                        <div class="chosse-us-icon">
                        <i class="fa fa-user-md" aria-hidden="true"></i>

                        </div>
                        <h4>Chat With Consultant</h4>
                        
                    </div>
                </div>

                <!--  Why Choose Ace Abstract (Photo Editor)-->
                <div class="col-md-6 col-lg-4">
                    <div class="why-choose-us-content text-center mb-80 wow fadeInUp" data-wow-delay="500ms">
                        <div class="chosse-us-icon">
                        <i class="fa fa-truck" aria-hidden="true"></i>

                        </div>
                        <h4>Delivers To Your Doorstep</h4>
                        
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <!-- Why Choose  us Area End -->

    <!-- Our Team Area Start -->
    <section class="our-team-area section-padding-80-50">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-heading text-center wow fadeInUp" data-wow-delay="100ms">
                        <h2>Testimonials</h2>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-xl-3">
                    <div class="team-content-area text-center mb-30 wow fadeInUp" data-wow-delay="100ms">
                        <div class="member-thumb">
                            <img src="images/testi1.jpg" alt="">
                        </div>
                        <h5>Darwin Nunez</h5>
                        <p>"Quick Meds has greatly benefited my family... I'll keep using it because it's convenient and simple to use.</p>
                    </div>
                </div>


                <div class="col-md-6 col-xl-3">
                    <div class="team-content-area text-center mb-30 wow fadeInUp" data-wow-delay="300ms">
                        <div class="member-thumb">
                            <img src="images/testi2.jpg" alt="">
                        </div>
                        <h5>Alisson Becker</h5>
                        <p>"The prices are unbelievable! identical medications costing 50% less... I am and will be a very delighted customer."</p>
                    </div>
                </div>


                <div class="col-md-6 col-xl-3">
                    <div class="team-content-area text-center mb-30 wow fadeInUp" data-wow-delay="500ms">
                        <div class="member-thumb">
                            <img src="images/testi3.jpg" alt="">
                        </div>
                        <h5>Alexander Arnold</h5>
                        <p>"Quick Meds is always a solid choice. For me, it's the greatest thing since the invention of bread. All I have to say about it is that."</p>
                    </div>
                </div>

                 
                <div class="col-md-6 col-xl-3">
                    <div class="team-content-area text-center mb-30 wow fadeInUp" data-wow-delay="500ms">
                        <div class="member-thumb">
                            <img src="images/testi4.jpg" alt="">
                        </div>
                        <h5>Mohammed Salah</h5>
                        <p>"Lower pricing, the ability to compare prices between different providers, and convenience are all characteristics of Quick Meds."</p>
                    </div>
                </div>


              
    

@endsection