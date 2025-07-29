<style type="text/css">
    
    .panel-navy-blue {
        background: #1b1464;
        color: #fff;
    }
    .panel-blue {
        background: #0071bd;
        color: #fff;
    }
    .panel-seven {
        background: #2a524f;
        color: #fff;
    }

</style>
                    <?php
                        $total_balance  = 0;
                        $tokenValue     = 0;
                    ?>
                    <?php if (!empty($transaction)) { ?>
                        <?php $data = json_decode($transaction->data); ?>

                        <?php foreach ($data as $key => $value) { ?>

                            <?php

                                $rate = 0;
                                
                                foreach ($value as $keys => $values) { 
                                    
                                    $total_balance =  @$values->crypto_balance;
                                    $rate          =  @$values->crypto_rate;
                                }
                        }
                            $tokenValue = $total_balance*$rate;
                    }

                    ?>

                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-2 col-form-label"><?php echo display('affiliate_url');?> </label>
                            <div class="col-sm-5">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="copyed" value="<?php echo base_url()?>register/?ref=<?php echo $this->session->userdata('user_id')?>">
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary" type="button" onclick="myFunction()"><?php echo display('copy');?></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br/>
                    <!-- /.Social share -->
                    <div class="row">

                        <div class="col-sm-6 col-md-4">
                            <div class="count_panel panel-navy-blue">
                                <div class="stats-title">
                                    <h4><?php echo display('balance');?></h4>
                                    <i class="fa fa-university"></i>
                                </div>
                                <h1 class="currency_text"><?php echo $coin_info->pair_with." ";?><?php echo @$totalBalance->balance>0 ?number_format(@$totalBalance->balance,8):0;?></h1>
                                <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Summation of all deposit, sell, received, roi and referral amount.  "></i>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="count_panel panel-blue">
                                <div class="stats-title ">
                                    <h4><?php echo "Funds Invested"; ?></h4>
                                    <i class="fa fa-universal-access"></i>
                                </div>
                                <h1 class="currency_text"><?php //echo $coin_info->symbol." ";?><?php echo @$total_balance>0 ?@$total_balance:0;?></h1>
                                <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Summation of all your invested money.  "></i>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="count_panel panel-seven">
                                <div class="stats-title ">
                                    <h4><?php echo "ROI";?></h4>
                                    <i class="fa fa-balance-scale"></i>
                                </div>
                                <h1 class="currency_text"><?php //echo $coin_info->symbol." ";?><?php echo @$tokenValue>0 ?number_format(@$tokenValue,8):0.00;?></h1>
                                <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Total of all Investment returns.  "></i>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="block_title"><?php echo display('package');?></h3>
                            <div class="owl-carousel owl-theme">

                                <?php if($package!=NULL){ 
                                    $i=1;
                                    foreach ($package as $key => $value) {  
                                ?>

                                <div class="item">
                                    <div class="pricing__item shadow navy__blue_<?php echo $i++;?>">
                                        <h3 class="pricing__title"><?php echo $value->package_name;?></h3>
                                        <div class="pricing__price"><span class="pricing__currency"><?php echo $coin_info->pair_with; ?></span><?php echo $value->package_amount;?></div>
                                        <!--<p class="pricing__sentence">Perfect for single freelancers who work by themselves</p>-->
                                        <ul class="pricing__feature-list">
                                            <li class="pricing__feature"><?php echo display('period');?> <span><?php echo $value->period;?> days</span></li>
                                            <li class="pricing__feature"><?php echo display('yearly_roi');?><span><?php echo $coin_info->pair_with; ?> <?php echo $value->yearly_roi;?></span></li>
                                            <li class="pricing__feature"><?php echo display('monthly_roi');?> <span><?php echo $coin_info->pair_with; ?> <?php echo $value->monthly_roi;?></span></li>
                                            <li class="pricing__feature"><?php echo display('weekly_roi');?> <span><?php echo $coin_info->pair_with; ?> <?php echo $value->weekly_roi;?></span></li>
                                        </ul>
                                        <a href="<?php echo base_url('customer/package/confirm_package/'.$value->package_id);?>" class="pricing__action center-block"><?php echo "Subscribe";?></a>
                                    </div>
                                    <!-- /.End of price item -->
                                </div>
                                <?php } }?>

                            </div>
                            <!-- /.Packages -->
                    </div>
                </div>