<?=partial('Auth.partials.login_head')?>
	<body class="hold-transition login-page">
		<div class="login-box">
			<div class="login-logo">
				<a href="<?=url()?>"><b>Wigal Blog</b></a>
			</div>
            <?=section('Admin.components.alerts')?>
			<!-- /.login-logo -->
			<div class="card">
				<div class="card-body login-card-body">
					<p class="login-box-msg">Sign in</p>
					<form action="<?=url('admin.login')?>" method="post">
						<div class="input-group mb-3">
							<input type="user_id" class="form-control" name="email" placeholder="Email" value="{{old('email')}}">
							<div class="input-group-append">
								<div class="input-group-text">
									<span class="fas fa-envelope"></span>
								</div>
							</div>
						</div>
                        <small class="text-danger"><?=form_error('email')?></small>
						<div class="input-group mb-3">
							<input type="password" class="form-control" name="password" placeholder="Password">
							<div class="input-group-append">
								<div class="input-group-text">
									<span class="fas fa-lock"></span>
								</div>
							</div>
						</div>
                        <small class="text-danger"><?=form_error('password')?></small>
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
								<button type="submit" name="login" class="btn btn-primary btn-block">Sign In</button>
							</div>
							<!-- /.col -->
						</div>
					</form>
				</div>
				<!-- /.login-card-body -->
			</div>
		</div>
		<!-- /.login-box -->
		<?=partial('Auth.partials.login_scripts')?>
	</body>
</html>
