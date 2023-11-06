<!--
Author: W3layouts
Author URL: http://w3layouts.com
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
-->
<!DOCTYPE html>
<html>
<head>
<title>Site Under Maintenance </title>
<!-- custom-theme -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Site Under Maintenance Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template, Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false);
		function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- //custom-theme -->
<link href="{{ asset('maintainence/css/font-awesome.css') }}" rel="stylesheet">
<link href="{{ asset('maintainence/css/style.css') }}" rel="stylesheet" type="text/css" media="all" />
<link href="//fonts.googleapis.com/css?family=Hind+Siliguri:300,400,500,600,700" rel="stylesheet">
</head>

@if($data['maintaince']->maintaince != 1 || 
	Auth::guard($data['userGuardRole'])->check() && Auth::guard($data['userGuardRole'])->user()->maintain_admin == 1 ||
	(!empty($data['maintaince']->maintaince_until) && 
	 date('Y-m-d H:i:s') >= $data['maintaince']->maintaince_until) ||
	date('Y-m-d H:i:s') <= $data['maintaince']->maintaince_start)
		<script type="text/javascript">
	        window.location.href="{{ route('home') }}";
	    </script>
@endif
<body class="bg-agileinfo">
   <h1 class="agile-head text-center">网站正在维护当中</h1>
	<div class="container-w3">
		<div class="content1-w3layouts"> 
			<img src="{{ asset('maintainence/images/2.png') }}" alt="under-construction">
			<p class="text-center">
				很抱歉给您带来不便。为了改善我们的服务，我们暂时关闭了我们的网站。
			</p>
		</div>
		<div class="demo2"></div>
		<!-- <div class="content2-w3-agileits">
		   <form action="#" method="post" class="agile-info-form">
				<input type="email" class="email" placeholder="Enter your email address" required="">
				<input type="submit" value="get notified!">
				<div class="clear"> </div> 
			</form>	
		</div> -->
	</div>	
	<script type="text/javascript" src="{{ asset('maintainence/js/jquery-1.11.1.min.js') }}"></script>
	<link rel="stylesheet" href='{{ asset("maintainence/css/dscountdown.css") }}' type='text/css' media='all' />
	<!-- Counter required files -->
		<script type="text/javascript" src="{{ asset('maintainence/js/dscountdown.min.js') }}"></script>
		<script>
			jQuery(document).ready(function($){						
				$('.demo2').dsCountDown({
					endDate: new Date("{{ date('F d, Y h:i:s', strtotime($data['maintaince']->maintaince_until)) }}"),
					theme: 'black'
				});								
			});
		</script>
	<!-- //Counter required files -->
</body>
</html>