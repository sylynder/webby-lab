<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>{{$site_name}} | {{ $page_title }} </title>
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
	<meta content="" name="description" />
	<meta content="" name="author" />
	
	<!-- ================== BEGIN core-css ================== -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
	<link href="{{ asset('bend/css/vendor.min.css') }}" rel="stylesheet" />
	<link href="<?=asset()?>bend/css/app.min.css" rel="stylesheet" />
	<!-- ================== END core-css ================== -->
</head>
<body class='pace-top'>
	<!-- BEGIN #loader -->
	<div id="loader" class="app-loader">
		<span class="spinner"></span>
	</div>
	<!-- END #loader -->

	<!-- BEGIN #app -->
	<div id="app" class="app">
		<!-- BEGIN login -->
		<div class="login login-with-news-feed">
			<!-- BEGIN news-feed -->
			<div class="news-feed">
				<div class="news-image" style="background-image: url(<?=asset()?>bend/img/login-bg.jpg)"></div>
				<div class="news-caption">
					<h4 class="caption-title"><b>Centroid</b> Admin</h4>
					<p>
						Manage all products from here ™.
					</p>
				</div>
			</div>
			<!-- END news-feed -->
			
			<!-- BEGIN login-container -->
			<div class="login-container">
				<!-- BEGIN login-header -->
				<div class="login-header mb-30px">
					<div class="brand">
						<div class="d-flex align-items-center">
							<a href="<?=url(current_route())?>">
								<img src="{{asset('img/logo.png')}}" alt="Centroid Logo" width="60%">
							</a>
						</div>
						<small>A Hire Purchase Platform</small>
					</div>
					<div class="icon">
						<i class="fa fa-sign-in-alt"></i>
					</div>
				</div>
				<!-- END login-header -->
				
				<!-- BEGIN login-content -->
				<div class="login-content">

					@section('Auth.components.alerts')

					<form class="fs-13px" action="<?=url('register')?>" method="post" autocomplete="off">
						
						<div class="form-floating mb-15px">
							<input type="text" class="form-control h-45px fs-13px" placeholder="Email Address" id="email"  name="email"  value="{{set_value('email')}}"/>
							<label for="user_id" class="d-flex align-items-center fs-13px text-gray-600">Email Address</label>
							<small class="text-danger"><strong>{{ form_error('email') }}</strong></small>
						</div>
						<div class="form-floating mb-15px">
							<input type="password" class="form-control h-45px fs-13px" placeholder="Password" id="password" name="password" />
							<label for="password" class="d-flex align-items-center fs-13px text-gray-600">Password</label>
							<small class="text-danger"><strong>{{ form_error('password') }}</strong></small>
						</div>
						<div class="form-floating mb-15px">
							<input type="password" class="form-control h-45px fs-13px" placeholder="Password" id="confirm_password" name="confirm_password" />
							<label for="password" class="d-flex align-items-center fs-13px text-gray-600">Confirm Password</label>
							<small class="text-danger mt-10px"><strong>{{ form_error('confirm_password') }}</strong></small>
						</div>
						<div class="mb-15px">
							<button type="submit" class="btn btn-success d-block h-45px w-100 btn-lg fs-14px">Sign me up</button>
						</div>
						<hr class="bg-gray-600 opacity-2" />
						<div class="text-gray-600 text-center text-gray-500-darker mb-0">
							&copy; Centroid All Rights Reserved <?=date('Y')?>
						</div>
					</form>
				</div>
				<!-- END login-content -->
			</div>
			<!-- END login-container -->
		</div>
		<!-- END login -->
		
		<!-- BEGIN scroll-top-btn -->
		<a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top" data-toggle="scroll-to-top"><i class="fa fa-angle-up"></i></a>
		<!-- END scroll-top-btn -->
	</div>
	<!-- END #app -->
	
	<!-- ================== BEGIN core-js ================== -->
	<script src="<?=asset()?>bend/js/vendor.min.js"></script>
	<script src="<?=asset()?>bend/js/app.min.js"></script>
	<script src="<?=asset()?>bend/js/default.min.js"></script>
	<!-- ================== END core-js ================== -->
</body>
</html>