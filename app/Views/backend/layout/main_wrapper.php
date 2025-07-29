<?php 
defined('BASEPATH') OR exit('No direct script access allowed'); 
//get site_align setting
$settings = $this->db->select("*")
    ->get('setting')
    ->row();
$help_notify = $this->db->select('id')->from('dbt_messenger')->where('reciver_id','admin')->where('status',1)->get()->num_rows();
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
        <link href="<?php echo base_url(); ?>assets/css/semantic.min.css" rel="stylesheet" type="text/css"/> 
        <!-- sliderAccess css -->
        <link href="<?php echo base_url(); ?>assets/css/jquery-ui-timepicker-addon.min.css" rel="stylesheet" type="text/css"/> 
        <!-- slider  -->
        <link href="<?php echo base_url(); ?>assets/css/select2.min.css" rel="stylesheet" type="text/css"/> 
        <!-- DataTables CSS -->
        <link href="<?= base_url('assets/datatables/css/dataTables.min.css') ?>" rel="stylesheet" type="text/css"/> 
  

        <!-- pe-icon-7-stroke -->
        <link href="<?php echo base_url('assets/css/pe-icon-7-stroke.css') ?>" rel="stylesheet" type="text/css"/> 
        <!-- themify icon css -->
        <link href="<?php echo base_url('assets/css/themify-icons.css') ?>" rel="stylesheet" type="text/css"/> 
        <!-- Pace css -->
        <link href="<?php echo base_url('assets/css/flash.css') ?>" rel="stylesheet" type="text/css"/>

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

                <a href="<?php echo base_url('backend/dashboard') ?>" class="logo"> <!-- Logo -->
                    <span class="logo-mini">
                        <img src="<?php echo base_url(!empty($settings->logo)?$settings->logo:"assets/images/icons/logo.png"); ?>" alt="">
                    </span>
                    <span class="logo-lg">
                        <img src="<?php echo base_url(!empty($settings->logo)?$settings->logo:"assets/images/icons/logo.png"); ?>" alt="">
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
                            <!-- settings -->
                            <li class="dropdown dropdown-user">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <i class="pe-7s-settings"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo base_url('backend/dashboard/home/profile'); ?>"><i class="pe-7s-users"></i> <?php echo display('profile') ?></a></li>
                                    <li><a href="<?php echo base_url('backend/dashboard/home/edit_profile'); ?>"><i class="pe-7s-settings"></i> <?php echo display('setting') ?></a></li>
                                    <li><a href="<?php echo base_url('logout') ?>"><i class="pe-7s-key"></i> <?php echo display('logout') ?></a></li>
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
                            <?php echo display('admin'); ?></a>
                        </div>
                    </div> 

                    <!-- sidebar menu -->
                    <ul class="sidebar-menu"> 

                        <li class="<?php echo (($this->uri->segment(3) == '' || $this->uri->segment(3) == 'home' ) ? "active" : null) ?>">
                            <a href="<?php echo base_url('backend/dashboard') ?>"><i class="fa fa ti-home"></i> <span><?php echo display('dashboard') ?></span></a>
                        </li> 

                        <li class="treeview <?php echo (($this->uri->segment(3) == "withdraw") || ($this->uri->segment(3) == "deposit") || ($this->uri->segment(3) == "credit") ? "active" : null) ?>">
                            <a href="#">
                                <i class="fa fa-money"></i> <span><?php echo "Manage Funding"; ?></span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a> 
                            <ul class="treeview-menu">
                                <li class="<?php echo (($this->uri->segment(4) == "withdraw_list") ? "active" : null) ?>"><a href="<?php echo base_url("backend/withdraw/withdraw/withdraw_list") ?>"> <i class="fa fa-reply-all"></i><?php echo display('withdraw') ?> </a></li>
                                <li class="<?php echo (($this->uri->segment(4) == "pending_withdraw") ? "active" : null) ?>"><a href="<?php echo base_url("backend/withdraw/withdraw/pending_withdraw") ?>"><i class="fa fa-clock-o"></i> <?php echo display('pending_withdraw') ?> </a></li>
                                <li class="<?php echo (($this->uri->segment(4) == "deposit_list") ? "active" : null) ?>"><a href="<?php echo base_url("backend/deposit/deposit/deposit_list") ?>"> <i class="fa fa-university"></i> <?php echo display('deposit') ?> </a></li>
                                <li class="<?php echo (($this->uri->segment(4) == "pending_deposit") ? "active" : null) ?>"><a href="<?php echo base_url("backend/deposit/deposit/pending_deposit") ?>"><i class="fa fa-money"></i> <?php echo display('pending_deposit') ?> </a></li>
                                <!-- <li class="<?php echo (($this->uri->segment(4) == "add_credit") ? "active" : null) ?>"><a href="<?php echo base_url("backend/dashboard/credit/add_credit") ?>"><i class="fa fa-credit-card"></i> <?php echo display('add_credit') ?> </a></li> -->
                                <!-- <li class="<?php echo (($this->uri->segment(4) == "credit_list") ? "active" : null) ?>"><a href="<?php echo base_url("backend/dashboard/credit/credit_list") ?>"><i class="fa fa-list"></i> <?php echo display('credit_list') ?> </a></li> -->
                            </ul>
                        </li>

                        <li class="treeview <?php echo (($this->uri->segment(3) == "admin") ? "active" : null) ?>">
                            <a href="#">
                                <i class="fa fa-user-secret"></i> <span><?php echo display('admin') ?></span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a> 

                            <ul class="treeview-menu">
                                <li class="<?php echo (($this->uri->segment(3) == "admin" && $this->uri->segment(4) == "form") ? "active" : null)?>"><a href="<?php echo base_url("backend/dashboard/admin/form") ?>"><i class="fa fa-user-plus"></i> <?php echo display('add_admin') ?> </a></li>
                                <li class="<?php echo (($this->uri->segment(3) == "admin" && $this->uri->segment(4) == null) ? "active" : null)?>"><a href="<?php echo base_url("backend/dashboard/admin/") ?>"><i class="fa fa-address-card"></i> <?php echo display('admin_list') ?> </a></li>
                            </ul>
                            
                        </li>
  

                        <li class="treeview <?php echo (($this->uri->segment(3) == "user") ? "active" : null) ?>">
                            <a href="#">
                                <i class="fa fa-users"></i> <span><?php echo display('users') ?></span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a> 
                            <ul class="treeview-menu">
                                <li class="<?php echo (($this->uri->segment(3) == "user") && ($this->uri->segment(4) == "form") ? "active" : null) ?>"><a href="<?php echo base_url("backend/user/user/form") ?>"><i class="fa fa-user-plus"></i> <?php echo display('add_user') ?></a></li>
                                <li class="<?php echo (($this->uri->segment(3) == "user") && ($this->uri->segment(4) == null) ? "active" : null) ?>"><a href="<?php echo base_url("backend/user/user/") ?>"><i class="fa fa-address-card"></i> <?php echo display('user_list') ?> </a></li>
                                <li class="<?php echo (($this->uri->segment(3) == "user") && ($this->uri->segment(4) == "pending_user_verification_list") ? "active" : null) ?>"><a href="<?php echo base_url("backend/user/user/pending_user_verification_list") ?>"><i class="fa fa-check-square"></i> Verify User </a></li>
                                <li class="<?php echo (($this->uri->segment(3) == "user") && ($this->uri->segment(4) == "subscriber_list") ? "active" : null) ?>"><a href="<?php echo base_url("backend/user/user/subscriber_list") ?>"><i class="fa fa-share-square" aria-hidden="true"></i> Subscriber </a></li>
                            </ul>
                        </li>

                        <li class="treeview <?php echo (($this->uri->segment(2) == "cms") ? "active" : null) ?>">
                            <a href="#">
                                <i class="fa fa-comments-o"></i> <span>CMS</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a> 
                            <ul class="treeview-menu">
                                <li class="<?php echo (($this->uri->segment(3) == "content") ? "active" : null) ?>"><a href="<?php echo base_url("backend/cms/content") ?>"><?php echo display('content') ?></a></li>
                                <li class="<?php echo (($this->uri->segment(3) == "faq") ? "active" : null) ?>"><a href="<?php echo base_url("backend/cms/faq") ?>"><?php echo display('faq') ?></a></li>
                                <li class="<?php echo (($this->uri->segment(3) == "team") ? "active" : null) ?>"><a href="<?php echo base_url("backend/cms/team") ?>"><?php echo display('team') ?></a></li>
                                <!-- <li class="<?php echo (($this->uri->segment(3) == "advisors") ? "active" : null) ?>"><a href="<?php echo base_url("backend/cms/advisors") ?>"><?php echo display('advisors') ?></a></li> -->
                                <!-- <li class="<?php echo (($this->uri->segment(3) == "testimonial") ? "active" : null) ?>"><a href="<?php echo base_url("backend/cms/testimonial") ?>"><?php echo display('testimonial') ?></a></li> -->
                                <!-- <li class="<?php echo (($this->uri->segment(3) == "chart") ? "active" : null) ?>"><a href="<?php echo base_url("backend/cms/chart") ?>">Chart</a></li> -->
                                <!-- <li class="<?php echo (($this->uri->segment(3) == "roadmap") ? "active" : null) ?>"><a href="<?php echo base_url("backend/cms/roadmap") ?>">Roadmap</a></li> -->
                                <li class="<?php echo (($this->uri->segment(3) == "client") ? "active" : null) ?>"><a href="<?php echo base_url("backend/cms/client") ?>"><?php echo display('client') ?></a></li>
                                <!-- <li class="<?php echo (($this->uri->segment(3) == "aboutcoin") ? "active" : null) ?>"><a href="<?php echo base_url("backend/cms/aboutcoin") ?>">About Coin</a></li> -->
                                <li class="<?php echo (($this->uri->segment(3) == "contact") ? "active" : null) ?>"><a href="<?php echo base_url("backend/cms/contact") ?>"><?php echo display('contact') ?></a></li>
                                <li class="<?php echo (($this->uri->segment(3) == "news") ? "active" : null) ?>"><a href="<?php echo base_url("backend/cms/news") ?>"><?php echo display('blog') ?></a></li>
                                <!-- <li class="<?php echo (($this->uri->segment(3) == "category") ? "active" : null) ?>"><a href="<?php echo base_url("backend/cms/category") ?>"><?php echo display('category') ?></a></li> -->
                                <li class="<?php echo (($this->uri->segment(3) == "social_link") ? "active" : null) ?>"><a href="<?php echo base_url("backend/cms/social_link") ?>"><?php echo display('social_link') ?></a></li>
                                <!-- <li class="<?php echo (($this->uri->segment(3) == "advertisement") ? "active" : null) ?>"><a href="<?php echo base_url("backend/cms/advertisement") ?>"><?php echo display('advertisement') ?></a></li> -->
                                <!-- <li class="<?php echo (($this->uri->segment(3) == "web_language") ? "active" : null) ?>"><a href="<?php echo base_url("backend/cms/web_language") ?>"><?php echo display('language_setting') ?></a></li> -->
                            </ul> 
                        </li>

                        <!-- <li class="treeview <?php echo (($this->uri->segment(2) == "currency") || ($this->uri->segment(2) == "coin_release") || ($this->uri->segment(2) == "coin_manager"|| ($this->uri->segment(2) == "coin_setup") || ($this->uri->segment(2) == "white_paper") || ($this->uri->segment(2) == "menu_control")) ? "active" : null) ?>">
                            <a href="#">
                                <i class="fa fa-sun-o"></i> <span>ICO settings</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a> 
                            <ul class="treeview-menu">                                
                                <li class="<?php echo (($this->uri->segment(2) == "currency") ? "active" : null) ?>"><a href="<?php echo base_url("backend/currency") ?>"><i class="fa fa-gg-circle"></i> <?php echo display('currency') ?> </a></li>
                                <li class="<?php echo (($this->uri->segment(2) == "coin_setup") ? "active" : null) ?>"><a href="<?php echo base_url("backend/coin_setup") ?>"><i class="fa fa-life-ring"></i> Coin Setup</a>
                                </li>
                                <li class="<?php echo (($this->uri->segment(2) == "coin_manager") ? "active" : null) ?>"><a href="<?php echo base_url("backend/coin_manager") ?>"><i class="fa fa-tasks"></i> Coin Manager</a></li>
                                <li class="<?php echo (($this->uri->segment(2) == "coin_release") ? "active" : null) ?>"><a href="<?php echo base_url("backend/coin_release") ?>"><i class="fa fa-database"></i> Coin Releasing</a></li>
                                <li class="<?php echo (($this->uri->segment(2) == "menu_control") ? "active" : null) ?>"><a href="<?php echo base_url("backend/menu_control") ?>"><i class="fa fa-venus-mars"></i> Menu Control </a></li>
                                <li class="<?php echo (($this->uri->segment(2) == "white_paper") ? "active" : null) ?>"><a href="<?php echo base_url("backend/white_paper") ?>"><i class="fa fa-picture-o" aria-hidden="true"></i> White Paper</a></li>
                            </ul>
                            
                        </li> -->

                        <li class="treeview <?php echo (($this->uri->segment(3) == "setting" || $this->uri->segment(3) == "language" || $this->uri->segment(2) == "payment_gateway" || $this->uri->segment(2) == "affiliation" || $this->uri->segment(2) == "cron_jobs") ? "active" : null) ?>">
                            <a href="#">
                                <i class="fa fa ti-settings"></i> <span><?php echo display('setting') ?></span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li class="<?php echo (($this->uri->segment(3) == "setting" && $this->uri->segment(4) == null ) ? "active" : null) ?>"><a href="<?php echo base_url("backend/dashboard/setting") ?>"> <?php echo display('app_setting') ?> </a></li> 
                                <li class="<?php echo (($this->uri->segment(4) == "fees_setting") ? "active" : null) ?>"><a href="<?php echo base_url("backend/dashboard/setting/fees_setting") ?>"> <?php echo display('fees_setting') ?> </a></li>
                                <li class="<?php echo (($this->uri->segment(4) == "email_sms_setting") ? "active" : null) ?>"><a href="<?php echo base_url("backend/dashboard/setting/email_sms_setting") ?>"> <?php echo display('email_and_sms_setting') ?> </a></li> 
                                <li class="<?php echo (($this->uri->segment(4) == "email_sms_gateway") ? "active" : null) ?>"><a href="<?php echo base_url("backend/dashboard/setting/email_sms_gateway") ?>"> <?php echo display('email_and_sms_gateway') ?> </a></li> 
                                <!-- <li class="<?php echo (($this->uri->segment(3) == "language") ? "active" : null) ?>"><a href="<?php echo base_url("backend/dashboard/language") ?>"> <?php echo display('language_setting') ?> </a></li>  -->
                                <li class="<?php echo (($this->uri->segment(3) == "payment_gateway") ? "active" : null) ?>"><a href="<?php echo base_url("backend/payment_gateway/payment_gateway") ?>"><?php echo display('payment_gateway') ?></a></li>
                                <!-- <li class="<?php echo (($this->uri->segment(2) == "affiliation") ? "active" : null) ?>"><a href="<?php echo base_url("backend/affiliation") ?>">Affiliation Setup</a></li> -->
                                
                                <!-- <li class="<?php //echo (($this->uri->segment(2) == "cron_jobs") ? "active" : null) ?>"><a href="<?php //echo base_url("backend/cron_jobs") ?>">Cron Jobs</a></li> -->
                            </ul>
                        </li>

                        <li class="treeview <?php echo (($this->uri->segment(3) == "package") ? "active" : null) ?>">
                            <a href="#">
                                <i class="ti-gift"></i> <span><?php echo display('package') ?></span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a> 
                            <ul class="treeview-menu">
                                <li class="<?php echo ($this->uri->segment(4)=="form"?'active':null)?>"><a href="<?php echo base_url("backend/package/package/form") ?>"><i class="fa fa-cube" aria-hidden="true"></i> <?php echo display('add_package') ?> </a></li>
                                <li class="<?php echo ($this->uri->segment(3)=="package" && $this->uri->segment(4)==null?'active':null)?>"><a href="<?php echo base_url("backend/package/package/") ?>"><i class="fa fa-cubes"></i> <?php echo display('package_list') ?> </a></li>
                            </ul>
                        </li>

                        <!-- <li class="treeview <?php echo (($this->uri->segment(3) == "exchange") ? "active" : null) ?>">
                            <a href="#">
                                <i class="fa fa-exchange"></i> <span>Exchange</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a> 
                            <ul class="treeview-menu">                                
                                <li class="<?php echo (($this->uri->segment(4) == "history") ? "active" : null) ?>"><a href="<?php echo base_url("backend/exchange/exchange/history") ?>"><i class="fa fa-history"></i> History</a></li>
                                <li class="<?php echo (($this->uri->segment(4) == "running") ? "active" : null) ?>"><a href="<?php echo base_url("backend/exchange/exchange/running") ?>"><i class="fa fa-filter" aria-hidden="true"></i> Running</a></li>
                                <li class="<?php echo (($this->uri->segment(4) == "canceled") ? "active" : null) ?>"><a href="<?php echo base_url("backend/exchange/exchange/canceled") ?>"><i class="fa fa-ban"></i> Canceled</a></li>
                            </ul>
                            
                        </li> -->
                        <li class="<?php echo (($this->uri->segment(2) == "helpline") ? "active" : null) ?>">
                            <a href="<?php echo base_url("backend/helpline") ?>"><i class="fa fa-commenting"></i> <span>HelpLine</span><?php echo $help_notify>0?"<span class='helpline-notification'><i class='notify-text'>$help_notify</i></span>":""; ?></a>
                        </li>                        
                        <!-- <li>
                            <a href="<?php echo base_url('backend/autoupdate') ?>"><i class="fa fa-magic"></i>Update</a>
                        </li> -->
                        <!-- <li>
                            <a target="_blank" href="https://forum.bdtask.com/"><i class="fa fa-question-circle"></i><span>Support</span></a>
                        </li> -->
     
                    </ul>
                </div> <!-- /.sidebar -->
            </aside>

            <!-- =============================================== -->
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">

                    <div class="p-l-30 p-r-30">
                        <div class="header-icon"><i class="pe-7s-world"></i></div>
                        <div class="header-title">
                            <h1><?php echo (($this->uri->segment(2)=="dashboard" || $this->uri->segment(3)=='home'|| $this->uri->segment(3)==null)?"Dashboard":str_replace('_', ' ', ucfirst($this->uri->segment(1)))) ?></h1>
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
        
        <!-- DataTables JavaScript -->
        <script src="<?php echo base_url("assets/datatables/js/dataTables.min.js") ?>"></script>

        <!-- Table Head Fixer -->
        <script src="<?php echo base_url() ?>assets/js/tableHeadFixer.js" type="text/javascript"></script> 

        <!-- Admin Script -->
        <script src="<?php echo base_url('assets/js/frame.js') ?>" type="text/javascript"></script> 

        <!-- Custom Theme JavaScript -->
        <script src="<?php echo base_url() ?>assets/js/custom.js" type="text/javascript"></script>

<?php

if (@$monthlyInvestment) {

    $monthsi = array();
    //$monthsw = array();
    //$monthst = array();

    $investmonth = '';
    $investamount = '';
    foreach ($monthlyInvestment as $key => $value) {
        $investmonth    .= '"'.$value->month.'", ';
        $investamount   .= $value->invest.', ';

        array_push($monthsi,$value->month);
    }
    $investmonth     = rtrim($investmonth, ", "); 
    $investamount    = rtrim($investamount, ", ");



    // $withmonth = '';
    // $withamount = '';
    // foreach ($monthly_withdraw as $key => $value) {
    //     $withmonth .= '"'.$value->month.'", ';
    //     $withamount .= $value->withdraw.', ';

    //     array_push($monthsw,$value->month);
    // }
    // $withmonth     = rtrim($withmonth, ", "); 
    // $withamount   = rtrim($withamount, ", ");



    // $trnsmonth = '';
    // $trnsamount = '';
    // foreach ($monthly_transfer as $key => $value) {
    //     $trnsmonth .= '"'.$value->month.'", ';
    //     $trnsamount .= $value->transfer.', ';

    //     array_push($monthst,$value->month);
    // }    
    // $trnsmonth     = rtrim($trnsmonth, ", "); 
    // $trnsamount   = rtrim($trnsamount, ", ");

    //$month = array_merge($monthsi, $monthsw, $monthst);

    $months = '';
    foreach ($monthsi as $key => $value) {
        $months .= '"'.$value.'", ';
    }
    $months = rtrim($months, ", ");

?>
        <script id="chartscript">
            $(document).ready(function () {
            "use strict"; // Start of use strict
                // line chart
                var ctx = document.getElementById("lineChart");
                window.myChart1 = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [<?php echo $months; ?>],
                        datasets: [

                            {
                                label: "Investment",
                                borderColor: "rgba(0,0,0,.09)",
                                borderWidth: "1",
                                backgroundColor: "rgba(55, 160, 0, 0.5)",
                                pointHighlightStroke: "rgba(26,179,148,1)",
                                data: [<?php echo $investamount;  ?>]
                            }
 
                        ]
                    },
                    options: {
                        responsive: true,
                        tooltips: {
                            mode: 'index',
                            intersect: false
                        },
                        hover: {
                            mode: 'nearest',
                            intersect: true
                        }

                    }
                });
            });
        </script>
<?php } ?>

<?php

if (@$monthly_fees) {

    $feesmonth = '';
    $fessamount = '';
    foreach ($monthly_fees as $key => $value) {
        $feesmonth .= '"'.$value->month.'", ';
        $fessamount .= $value->fees.', ';
    }

    $feesmonth     = rtrim($feesmonth, ", "); 
    $fessamount   = rtrim($fessamount, ", ");

?>
        <script id="linetscript">
            $(document).ready(function () {
                "use strict"; // Start of use strict

                // single bar chart
                var ctx = document.getElementById("singelBarChart");
                window.myChart2 = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: [<?php echo $feesmonth; ?>],
                        datasets: [
                            {
                                label: "Fees Collected",
                                data: [<?php echo $fessamount;  ?>],
                                borderColor: "rgba(55, 160, 0, 0.9)",
                                borderWidth: "0",
                                backgroundColor: "rgba(55, 160, 0, 0.5)"
                            }
                        ]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }]
                        }
                    }
                });
            });
        </script>
<?php } ?>


    </body>
</html>