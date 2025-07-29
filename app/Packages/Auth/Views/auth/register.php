<?=partial('Auth.partials.login_head')?>
	<body class="hold-transition login-page">
		<div class="login-box">
			<div class="login-logo">
				<a href="<?=url()?>"><b>Ditch The Wait</b></a>
			</div>
            <?=section('Admin.components.alerts')?>
			<!-- /.login-logo -->
			<div class="card">
				<div class="card-body login-card-body">
					<p class="login-box-msg">Sign Up</p>
					<?php //validation_errors()?>
					<form action="<?=url('signup')?>" method="post" autocomplete="off">
                    
                        <div class="input-group mb-3">
							<input type="text" class="form-control" name="company_name" value="<?=set_value('company_name')?>" placeholder="Company Name">
							<div class="input-group-append">
								<div class="input-group-text">
									<!-- <span class="fas fa-envelope"></span> -->
								</div>
							</div>
						</div>
                        <small class="text-danger"><?=form_error('company_name')?></small>
                        
                        <div class="input-group mb-3">
							<input type="text" class="form-control" name="phone" value="<?=set_value('phone')?>" placeholder="Phone">
							<div class="input-group-append">
								<div class="input-group-text">
									<!-- <span class="fas fa-envelope"></span> -->
								</div>
							</div>
						</div>
                        <small class="text-danger"><?=form_error('phone')?></small>


                        <div class="input-group mb-3">
							<input type="text" class="form-control" name="address1" value="<?=set_value('address1')?>" placeholder="Address 1">
							<div class="input-group-append">
								<div class="input-group-text">
									<!-- <span class="fas fa-envelope"></span> -->
								</div>
							</div>
						</div>
                        <small class="text-danger"><?=form_error('address1')?></small>

                        <div class="input-group mb-3">
							<input type="text" class="form-control" name="address2" value="<?=set_value('address2')?>" placeholder="Address 2">
							<div class="input-group-append">
								<div class="input-group-text">
									<!-- <span class="fas fa-envelope"></span> -->
								</div>
							</div>
						</div>
                        <small class="text-danger"><?=form_error('address2')?></small>

                        <div class="input-group mb-3">
							<input type="text" class="form-control" name="state" value="<?=set_value('state')?>" placeholder="State">
							<div class="input-group-append">
								<div class="input-group-text">
									<!-- <span class="fas fa-envelope"></span> -->
								</div>
							</div>
						</div>
                        <small class="text-danger"><?=form_error('state')?></small>
                        
						<div class="input-group mb-3">
							<input type="text" class="form-control" name="city" value="<?=set_value('city')?>" placeholder="City">
							<div class="input-group-append">
								<div class="input-group-text">
									<!-- <span class="fas fa-envelope"></span> -->
								</div>
							</div>
						</div>
                        <small class="text-danger"><?=form_error('city')?></small>

                        <div class="input-group mb-3">
							<input type="text" class="form-control" name="zip" value="<?=set_value('zip')?>" placeholder="Zip">
							<div class="input-group-append">
								<div class="input-group-text">
									<!-- <span class="fas fa-envelope"></span> -->
								</div>
							</div>
						</div>
                        <small class="text-danger"><?=form_error('zip')?></small>

						<div class="input-group mb-3">
							<input type="text" class="form-control" name="email" value="<?=set_value('email')?>" placeholder="Admin Email">
							<div class="input-group-append">
								<div class="input-group-text">
									<!-- <span class="fas fa-envelope"></span> -->
								</div>
							</div>
						</div>
                        <small class="text-danger"><?=form_error('email')?></small>

						<div class="input-group mb-3">
							<input type="text" class="form-control" name="firstname" value="<?=set_value('firstname')?>" placeholder="Admin Name">
							<div class="input-group-append">
								<div class="input-group-text">
									<!-- <span class="fas fa-envelope"></span> -->
								</div>
							</div>
						</div>
                        <small class="text-danger"><?=form_error('firstname')?></small>
						
						<div class="input-group mb-3">
							<input type="password" class="form-control" name="password" placeholder="Password">
							<div class="input-group-append">
								<div class="input-group-text">
									<!-- <span class="fas fa-lock"></span> -->
								</div>
							</div>
						</div>
                        <small class="text-danger"><?=form_error('password')?></small>
						
						<div class="row">
							<div class="col-12">
								<button type="submit" name="login" class="btn btn-primary btn-block">Sign Up</button>
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
