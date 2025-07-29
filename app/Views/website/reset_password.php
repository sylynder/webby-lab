<?php $this->load->view('website/partials/head');?>
 
<body class="nk-body body-wider bg-light-alt">
	<div class="nk-wrap">
		
    	<?php $this->load->view('website/components/auth_header');?>

        <main class="nk-pages">
            <div class="section section-l">
                <div class="container">
                    <div class="nk-blocks d-flex justify-content-center">
                        <div class="ath-container m-0">
                            <div class="ath-body">
                                <h5 class="ath-heading title">Reset<small class="tc-default">with your Email</small></h5>
                                <form action="./">
                                    <div class="field-item">
                                        <div class="field-wrap">
                                            <input type="text" class="input-bordered" placeholder="Your Email">
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btn-block btn-md">Reset Password</button>
                                    <div class="ath-note text-center">
                                        Remembered? <a href="<?=site_url('login')?>"> <strong>Sign in here</strong></a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    
		<?php $this->load->view('website/components/footer')?>
	</div>
	<div class="preloader"><span class="spinner spinner-round"></span></div>
	
	<!-- JavaScript -->
	<?php $this->load->view('website/partials/scripts');?>
</body>
</html>
