<?php $this->load->view('website/partials/head');?>
 
<body class="nk-body body-wider bg-light-alt">
	<div class="nk-wrap">
		
    	<?php $this->load->view('website/components/auth_header');?>
        
        <main class="nk-pages">
            <div class="section section-l">

                <div class="nk-blocks d-flex justify-content-center">
                    
                    <!-- alert message -->
                    <?php if ($this->session->flashdata('message') != null) {  ?>
                    
                    <div class="alert alert-primary-alt alert-dismissible fade show">
                        <?php echo $this->session->flashdata('message'); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div> 
                    <?php } ?>
                        
                    <?php if ($this->session->flashdata('exception') != null) {  ?>
                    
                    <div class="alert alert-danger-alt alert-dismissible fade show">
                        <?php echo $this->session->flashdata('exception'); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div> 

                    <?php } ?>
                        
                    <?php if (validation_errors()) {  ?>
                    
                    <div class="alert alert-danger-alt alert-dismissible fade show">
                        <?php echo validation_errors(); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div> 
                    <?php } ?> 
                    <br>
                </div>

                <div class="container">

                    <div class="nk-blocks d-flex justify-content-center">

                        <div class="ath-container m-0">
                            <?php
                               dump(url('pod'))
                            ?>
                            <div class="ath-body">
                                <h5 class="ath-heading title">Sign In  <a href="<?=url('pod')?> ">Check</a><small class="tc-default">with your Net Trading Account</small></h5>
                                <!-- <form action="<?=site_url('login')?>" method="post" autocomplete="off"> -->
                                <?php echo form_open('home/login','id="loginForm" '); ?>
                                    <div class="field-item">
                                        <div class="field-wrap">
                                            <input type="text" class="input-bordered" name="luseremail" id="useremail" placeholder="Email" autocomplete="off" required>
                                        </div>
                                    </div>
                                    <div class="field-item">
                                        <div class="field-wrap">
                                            <input type="password" class="input-bordered" name="lpassword" id="password" placeholder="Password" required>
                                        </div>
                                    </div>
                                    <div class="field-item d-flex justify-content-between align-items-center">
                                        <div class="field-item pb-0">
                                            <!-- <input class="input-checkbox" id="remember-me-100" type="checkbox"> -->
                                            <!-- <label for="remember-me-100">Remember Me</label> -->
                                        </div>
                                        <div class="forget-link fz-6">
                                            <a href="<?=site_url('reset')?>">Forgot password?</a>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btn-block btn-md">Sign In</button>
                                </form>

                                
                                <div class="ath-note text-center">
                                    Don’t have an account? <a href="<?=site_url('register')?>"> <strong>Sign up here</strong></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    
		<?php $this->load->view('website/components/footer')?>

	<div class="preloader"><span class="spinner spinner-round"></span></div>
	
	<!-- JavaScript -->
	<?php $this->load->view('website/partials/scripts');?>
</body>
</html>
