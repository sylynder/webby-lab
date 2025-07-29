<?php 
defined('BASEPATH') OR exit('No direct script access allowed'); 
$settings = $this->db->select("*")
	->get('setting')
	->row();
	
$user_id = $this->session->userdata('user_id');
$message = $this->db->select('*')->from('message')->where('receiver_id',$user_id)->where('receiver_status',0)->order_by('id','DESC')->get()->result();	
$unsen = $this->db->select('*')->from('notifications')->where('user_id',$user_id)->where('status',0)->order_by('notification_id','DESC')->get()->result();

$help_notify = $this->db->select('id')->from('dbt_messenger')->where('reciver_id',$user_id)->where('admin_status',1)->get()->num_rows();

?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title><?= $settings->title ?> - <?php echo (!empty($title)?$title:null) ?></title>

		<!-- Favicon and touch icons -->
		<link rel="shortcut icon" href="<?php echo base_url(!empty($settings->favicon)?$settings->favicon:"assets/images/icons/favicon.png"); ?>">

		<!-- jquery ui css -->
		<link href="<?php echo base_url('assets/css/jquery-ui.min.css') ?>" rel="stylesheet" type="text/css"/>

		<!-- Bootstrap --> 
		<link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
		<?php if (!empty($settings->site_align) && $settings->site_align == "RTL") {  ?>
			<!-- THEME RTL -->
			<link href="<?php echo base_url(); ?>assets/css/bootstrap-rtl.min.css" rel="stylesheet" type="text/css"/>
			<link href="<?php echo base_url('assets/css/custom-rtl.css') ?>" rel="stylesheet" type="text/css"/>
		<?php } ?>



		<!-- Font Awesome 4.7.0 -->
		<link href="<?php echo base_url('assets/css/font-awesome.min.css') ?>" rel="stylesheet" type="text/css"/>

		<!-- semantic css -->
		<link href="<?php echo base_url('assets/css/semantic.min.css'); ?>" rel="stylesheet" type="text/css"/> 
		<!-- sliderAccess css -->
		<link href="<?php echo base_url('assets/css/jquery-ui-timepicker-addon.min.css'); ?>" rel="stylesheet" type="text/css"/> 
		<!-- slider  -->
		<link href="<?php echo base_url('assets/css/select2.min.css'); ?>" rel="stylesheet" type="text/css"/> 
		<!-- DataTables CSS -->
		<link href="<?= base_url('assets/datatables/css/dataTables.min.css') ?>" rel="stylesheet" type="text/css"/> 
  

		<!-- pe-icon-7-stroke -->
		<link href="<?php echo base_url('assets/css/pe-icon-7-stroke.css') ?>" rel="stylesheet" type="text/css"/> 
		<!-- themify icon css -->
		<link href="<?php echo base_url('assets/css/themify-icons.css') ?>" rel="stylesheet" type="text/css"/> 
		<!-- Pace css -->
		<link href="<?php echo base_url('assets/css/flash.css') ?>" rel="stylesheet" type="text/css"/>
		<link href="<?php echo base_url('assets/js/sweetalert/sweetalert.css') ?>" rel="stylesheet" type="text/css"/>

		<!-- End Global Mandatory Style -->
        <!-- Start page Label Plugins  -->
        <link href="<?php echo base_url('assets/plugins/toastr/toastr.css') ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo base_url('assets/plugins/OwlCarousel2-2.2.1/owl.carousel.min.css') ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo base_url('assets/plugins/OwlCarousel2-2.2.1/owl.theme.default.min.css') ?>" rel="stylesheet" type="text/css"/>
        <!-- End page Label Plugins  -->
        

		<!-- Theme style -->
		<link href="<?php echo base_url('assets/css/custom.css') ?>" rel="stylesheet" type="text/css"/>
		<?php if (!empty($settings->site_align) && $settings->site_align == "RTL") {  ?>
		<!-- THEME RTL -->
		<link href="<?php echo base_url('assets/css/custom-rtl.css') ?>" rel="stylesheet" type="text/css"/>
		<?php } ?>

		<!-- jQuery  -->
		<script src="<?php echo base_url('assets/js/jquery.min.js') ?>" type="text/javascript"></script>

	</head>

	<body class="hold-transition sidebar-mini">
		<div class="se-pre-con"></div>

		<!-- Site wrapper -->
		<div class="wrapper">
			<header class="main-header">  

				<a href="<?php echo base_url('customer/home') ?>" class="logo"> <!-- Logo -->
					<span class="logo-mini">
						<img src="<?php echo base_url(!empty($settings->logo)?$settings->logo:"assets/images/icons/logo.png"); ?>" alt="Logo">
					</span>
					<span class="logo-lg">
						<img src="<?php echo base_url(!empty($settings->logo)?$settings->logo:"assets/images/icons/logo.png"); ?>" alt="Logo">
					</span>
				</a>

				<!-- Header Navbar -->
				<nav class="navbar navbar-static-top">
					<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button"> <!-- Sidebar toggle button-->
						<span class="sr-only">Toggle navigation</span>
						<span class="pe-7s-keypad"></span>
					</a>
					<div class="navbar-custom-menu">
						<ul class="nav navbar-nav">
							<li class="dropdown messages-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="pe-7s-mail"></i>
                                    <span class="label label-success"><?php echo count($message);?></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="header">You have <?php echo count($message);?> messages</li>
                                    <li>
                                        <ul class="menu">
                                            <li><!-- start message -->
                                            	<?php foreach($message as $val ){?>
                                                <a href="<?php echo base_url('customer/message/message_details/').$val->id ?>">
                                                    <div class="pull-left"><img src="<?php echo base_url(!empty($settings->logo)?$settings->logo:"assets/images/icons/logo.png"); ?>" class="img-circle" alt="User Image"></div>
                                                    <h4><?php echo $val->subject;?><small><i class="fa fa-clock-o"></i> <?php echo $val->datetime;?></small></h4>
                                                    <p><?php echo $val->message?></p>
                                                </a>
                                                <?php }?>
                                            </li>                                          
                                        </ul>
                                    </li>
                                    <li class="footer"><a href="<?php echo base_url('customer/message'); ?>">See All Messages</a></li>
                                </ul>
                            </li>
                            <!-- Notifications -->
                            <li class="dropdown notifications-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="pe-7s-speaker"></i>
                                    <span class="label label-warning"><?php echo count($unsen)?></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="header">You have <?php echo count($unsen)?> notifications</li>
                                    <li>
                                        <ul class="menu">
                                        	<?php foreach($unsen as $val ){?>
                                            <li><a href="<?php echo base_url('customer/notification/email_details/').$val->notification_id; ?>"><i class="ti-announcement color-green"></i><?php echo $val->details?></a></li>
                                            <?php }?>
                                        </ul>
                                    </li>
                                    <li class="footer"><a href="<?php echo base_url('customer/notification')?>">View all</a></li>
                                </ul>
                            </li>
							<!-- settings -->
							<li class="dropdown dropdown-user">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"> <i class="pe-7s-settings"></i></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo base_url('customer/profile'); ?>"><i class="pe-7s-users"></i> <?php echo display('profile') ?></a></li>
									<li><a href="<?php echo base_url('customer/profile/change_password'); ?>"><i class="pe-7s-settings"></i> <?php echo display('change_password') ?></a></li>
									<li><a href="<?php echo base_url('log_out') ?>"><i class="pe-7s-key"></i> <?php echo display('logout') ?></a></li>
								</ul>
							</li>
						</ul>
					</div>
				</nav>
			</header>

			<!-- =============================================== -->
			<!-- Left side column. contains the sidebar -->
			<aside class="main-sidebar">
				<!-- sidebar -->
				<div class="sidebar">
					<!-- Sidebar user panel -->
					<div class="user-panel text-center">
						<?php $image = $this->session->userdata('image'); ?>
						<div class="image">
							<img src="<?php echo base_url(!empty($image)?$image:"assets/images/icons/user.png") ?>" class="img-circle" alt="User Image">
						</div>
						<div class="info">
							<p><?php echo $this->session->userdata('fullname') ?></p>
							<a href="#"><i class="fa fa-circle text-success"></i>
							<?php echo display('user'); ?></a>
						</div>
					</div>
					<!-- sidebar menu -->
					<ul class="sidebar-menu"> 

						<li class="home">
							<a href="<?php echo base_url('customer/home') ?>"><i class="fa fa ti-home"></i> <span><?php echo display('dashboard') ?></span></a>
						</li>

						<li class="treeview account <?php echo (($this->uri->segment(2)=="verify_account" || $this->uri->segment(2)=="profile" || $this->uri->segment(3)=="change_password" || $this->uri->segment(3)=="language_setting")? "active":null)?>">
							<a href="#">
								<i class="ti-key"></i> <span><?php echo display('Account') ?></span>
								<span class="pull-right-container">
									<i class="fa fa-angle-left pull-right"></i>
								</span>
							</a> 
							<ul class="treeview-menu">
								<li class="transfer <?php echo (($this->uri->segment(2)=="verify_account" && $this->uri->segment(3)== null)? "active":null)?>"><a href="<?php echo base_url('customer/verify_account') ?>"><i class="fa fa-share-square-o"></i> KYC</a></li>
								<li class="<?php echo (($this->uri->segment(2)=="profile" && $this->uri->segment(3)== null)? "active":null)?>"><a href="<?php echo base_url('customer/profile'); ?>"><i class="pe-7s-users"></i> <?php echo display('profile') ?></a></li>
								<li class="<?php echo (($this->uri->segment(3)=="change_password")? "active":null)?>"><a href="<?php echo base_url('customer/profile/change_password'); ?>"><i class="pe-7s-settings"></i> <?php echo display('change_password') ?></a></li>
								<!-- <li class="<?php echo (($this->uri->segment(3)=="language_setting")? "active":null)?>"><a href="<?php echo base_url("customer/settings/language_setting") ?>"><i class="fa fa-language" aria-hidden="true"></i> <?php echo display('language_setting') ?> </a></li> -->
							</ul>
						</li>

						<!-- <li class="treeview account">
							<a href="#">
								<i class="fa fa-shield" aria-hidden="true"></i>Security</span>
								<span class="pull-right-container">
									<i class="fa fa-angle-left pull-right"></i>
								</span>
							</a> 
							<ul class="treeview-menu">
								<li class="transfer"><a href="<?php echo base_url('customer/verify_account') ?>"><i class="fa fa-share-square-o"></i> KYC</a></li>
								<li class="transfer"><a href="<?php echo base_url('customer/verify_account') ?>"><i class="fa fa-google" aria-hidden="true"></i> Google Authenticator</a></li>
								<li class="transfer"><a href="<?php echo base_url('customer/verify_account') ?>"><i class="fa fa-commenting" aria-hidden="true"></i> SMS Authentication</a></li>
							</ul>
						</li> -->

						<li class="treeview finance <?php echo (($this->uri->segment(2)== "transection" || $this->uri->segment(3)== "payment_method_setting")? "active":null)?>">
							<a href="#">
								<i class="fa fa-money"></i> <span><?php echo "Funding" ?></span>
								<span class="pull-right-container">
									<i class="fa fa-angle-left pull-right"></i>
								</span>
							</a> 
							<ul class="treeview-menu">
								<li class="deposit"><a href="<?php echo base_url("customer/deposit") ?>"><i class="fa fa-university"></i> <?php echo display('deposit') ?> </a></li>
								<li class="transfer"><a href="<?php echo base_url('customer/transfer') ?>"><i class="fa fa-share-square-o"></i> <?php echo display('transfer') ?></a></li>
								<li class="withdraw"><a href="<?php echo base_url('customer/withdraw') ?>"><i class="fa fa-reply-all"></i> <?php echo display('withdraw') ?></a></li>								
								<li class="transection"><a href="<?php echo base_url('customer/transection') ?>"><i class="fa fa-exchange"></i> <span><?php echo display('transection') ?></span></a></li>
								<li class="<?php echo (($this->uri->segment(3)== "payment_method_setting")? "active":null)?>"><a href="<?php echo base_url("customer/settings/payment_method_setting") ?>"><i class="fa fa-money" aria-hidden="true"></i> Payout Setting </a></li>
							</ul>
						</li>
						<li class="treeview wallet <?php echo (($this->uri->segment(2) == "package")? "active":null)?>">
                            <a href="#">
                                <i class="ti-package"></i> <span>Package</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li class="<?php echo (($this->uri->segment(2) == "package" && $this->uri->segment(3) == null)? "active":null)?>"><a href="<?php echo base_url("customer/package") ?>"><i class="fa fa-cubes" aria-hidden="true"></i> Packages</a></li>
                                <li class="<?php echo (($this->uri->segment(3) == "investment")? "active":null)?>"><a href="<?php echo base_url("customer/package/investment") ?>"><i class="fa fa-diamond" aria-hidden="true"></i> My Packages</a></li>
                                <li class="<?php echo (($this->uri->segment(3) == "package_payout")? "active":null)?>"><a href="<?php echo base_url("customer/package/package_payout") ?>"><i class="fa fa-cubes" aria-hidden="true"></i> Package Payout</a></li>
                            </ul>  
                        </li>						
						<!-- <li class="treeview wallet <?php echo (($this->uri->segment(2) == "token")? "active":null)?>">
                            <a href="#">
                                <i class="fa fa-address-card-o" aria-hidden="true"></i> <span>ICO Wallet</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li class="<?php echo (($this->uri->segment(3) == "token_buy")? "active":null)?>"><a href="<?php echo base_url("customer/token/token_buy") ?>"><i class="fa fa-shopping-cart" aria-hidden="true"></i> Buy Token</a></li>
                                <li class="<?php echo (($this->uri->segment(3) == "token_list")? "active":null)?>"><a href="<?php echo base_url("customer/token/token_list") ?>"><i class="fa fa-history" aria-hidden="true"></i> Wallet History</a></li>
                            </ul>  
                        </li>	 -->					
						<!-- <li class="treeview exchange">
                            <a href="#">
                                <i class="fa fa-exchange" aria-hidden="true"></i> <span><?php echo display('exchange')?></span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li class="<?php echo (($this->uri->segment(2) == "exchange" && $this->uri->segment(3) == null)? "active":null)?>"><a href="<?php echo base_url("customer/exchange") ?>"><i class="fa fa-exchange" aria-hidden="true"></i> <?php echo display('exchange')?></a></li>
                                <li class="<?php echo (($this->uri->segment(3) == "exchange_opened")? "active":null)?>"><a href="<?php echo base_url("customer/exchange/exchange_opened") ?>"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Open Exchange</a></li>
                                <li class="<?php echo (($this->uri->segment(3) == "exchange_canceled")? "active":null)?>"><a href="<?php echo base_url("customer/exchange/exchange_canceled") ?>"><i class="fa fa-ban" aria-hidden="true"></i> Cancled Exchange</a></li>
                                <li class="<?php echo (($this->uri->segment(3) == "exchange_history")? "active":null)?>"><a href="<?php echo base_url("customer/exchange/exchange_history") ?>"><i class="fa fa-history" aria-hidden="true"></i> History</a></li>
                            </ul>  
                        </li> -->
                        <li class="notification"><a href="<?php echo base_url('customer/notification') ?>"><i class="fa fa-comment-o"></i> <span><?php echo display('notification') ?></span></a></li>
						<li class="message"><a href="<?php echo base_url('customer/message') ?>"><i class="ti-email"></i> <span><?php echo display('sms') ?></span></a></li>
						<li class="<?php echo (($this->uri->segment(2) == "helpline") ? "active" : null) ?>">
                            <a href="<?php echo base_url("customer/helpline") ?>"><i class="fa fa-commenting"></i> <span>HelpLine</span><?php echo $help_notify>0?"<span class='helpline-notification'><i class='notify-text'>$help_notify</i></span>":""; ?></a>
                        </li>

					</ul>
				</div> <!-- /.sidebar -->
			</aside>


<script type="text/javascript">
	$(document).ready(function () {
		var segment_2 = '<?php echo $this->uri->segment(2); ?>';

		if (segment_2 === 'home') {
			$('.home').addClass('active');

		}
		else if (segment_2 === 'team' || segment_2==='commission') {
			$('.account').addClass('active');

		}
		else if (segment_2 === 'withdraw') {
			$('.finance').addClass('active');
			$('.withdraw').addClass('active');

		}
		else if (segment_2 === 'transfer') {
			$('.finance').addClass('active');
			$('.transfer').addClass('active');

		}
		else if (segment_2 === 'deposit') {
			$('.finance').addClass('active');
			$('.deposit').addClass('active');

		}
		else if (segment_2 === 'transection') {
			$('.transection').addClass('active');

		}
		else if (segment_2 === 'notification') {
			$('.notification').addClass('active');

		}
		else if (segment_2 === 'message') {
			$('.message').addClass('active');

		}
		else if (segment_2 === 'settings') {
			$('.settings').addClass('active');

		}
		else if (segment_2 === 'currency' || segment_2==='buy' || segment_2==='sell' || segment_2==='exchange') {
			$('.exchange').addClass('active');

		}

	});
</script>


			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<section class="content-header">

					<div class="p-l-30 p-r-30">
						<!-- <div class="header-icon"></div> -->
						<div class="header-title">
							<h1><?php echo str_replace('_', ' ', ucfirst($this->uri->segment(1))) ?></h1>
							<small><?php echo (!empty($title)?$title:null) ?></small> 
						</div>
					</div>
				</section>
				<!-- Main content -->
				<div class="content"> 

					<!-- alert message -->
					<?php if ($this->session->flashdata('message') != null) {  ?>
					<div class="alert alert-info alert-dismissable">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						<?php echo $this->session->flashdata('message'); ?>
					</div> 
					<?php } ?>
					
					<?php if ($this->session->flashdata('exception') != null) {  ?>
					<div class="alert alert-danger alert-dismissable">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						<?php echo $this->session->flashdata('exception'); ?>
					</div>
					<?php } ?>
					
					<?php if (validation_errors()) {  ?>
					<div class="alert alert-danger alert-dismissable">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						<?php echo validation_errors(); ?>
					</div>
					<?php } ?>					

					<!-- content -->
					<?php echo (!empty($content)?$content:null) ?>

				</div> <!-- /.content -->
			</div> <!-- /.content-wrapper -->

			<footer class="main-footer">
				<?= $settings->footer_text ?>
			</footer>
		</div> <!-- ./wrapper -->
 
		<!-- jquery-ui js -->
		<script src="<?php echo base_url('assets/js/jquery-ui.min.js') ?>" type="text/javascript"></script> 
		
		<!-- bootstrap js -->
		<script src="<?php echo base_url('assets/js/bootstrap.min.js') ?>" type="text/javascript"></script>  
		
		<!-- pace js -->
		<script src="<?php echo base_url('assets/js/pace.min.js') ?>" type="text/javascript"></script>  
		<!-- SlimScroll -->
		<script src="<?php echo base_url('assets/js/jquery.slimscroll.min.js') ?>" type="text/javascript"></script>  

		<!-- bootstrap timepicker -->
		<script src="<?php echo base_url() ?>assets/js/jquery-ui-sliderAccess.js" type="text/javascript"></script> 
		<script src="<?php echo base_url() ?>assets/js/jquery-ui-timepicker-addon.min.js" type="text/javascript"></script> 
		<!-- select2 js -->
		<script src="<?php echo base_url() ?>assets/js/select2.min.js" type="text/javascript"></script>

		<script src="<?php echo base_url('assets/js/sparkline.min.js') ?>" type="text/javascript"></script> 
		<!-- Counter js -->
		<script src="<?php echo base_url('assets/js/waypoints.js') ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('assets/js/jquery.counterup.min.js') ?>" type="text/javascript"></script>

		<!-- ChartJs JavaScript -->
		<script src="<?php echo base_url('assets/js/Chart.min.js') ?>" type="text/javascript"></script>
		<!-- sweetalert -->
		<script src="<?php echo base_url()?>assets/js/sweetalert/sweetalert.min.js" type="text/javascript"></script>
        
		<!-- semantic js -->
		<script src="<?php echo base_url() ?>assets/js/semantic.min.js" type="text/javascript"></script>
		<!-- DataTables JavaScript -->
		<script src="<?php echo base_url("assets/datatables/js/dataTables.min.js") ?>"></script>
		<!-- tinymce texteditor -->
		<script src="<?php echo base_url() ?>assets/tinymce/tinymce.min.js" type="text/javascript"></script> 
		<!-- Table Head Fixer -->
		<script src="<?php echo base_url() ?>assets/js/tableHeadFixer.js" type="text/javascript"></script> 

		<!-- Admin Script -->
		<script src="<?php echo base_url('assets/js/frame.js') ?>" type="text/javascript"></script> 


		<!-- Start Page Lavel Plugins
        =====================================================================-->
        <script src="<?php echo base_url() ?>assets/plugins/toastr/toastr.min.js" type="text/javascript"></script>
        <script src="<?php echo base_url() ?>assets/plugins/OwlCarousel2-2.2.1/owl.carousel.min.js" type="text/javascript"></script>
        <!-- End Page Lavel Plugins-->
       
       	<!-- Custom Theme JavaScript -->
		<script src="<?php echo base_url() ?>assets/js/custom.js" type="text/javascript"></script>
		

	</body>
</html>