

<style type="text/css">
    .panel-navy-blue{
        background: #1b1464;
        color: #fff;
    }
    .panel-orenge{
        background: #f8931f;
        color: #fff;
    }

    .panel-blue{
        background: #0071bd;
        color: #fff;
    }
    .panel-sky{
        background: #00a99e;
        color: #fff;
    }
    .panel-four{
        background: #C50B3E;
        color: #fff;
    }
    .panel-five {
        background: #86C20C;
        color: #fff;
    }
    .panel-six {
        background: #033641;
        color: #fff;
    }
    .panel-seven{
        background: #2a524f;
        color: #fff;
    }
</style>
    <!-- /.Social share -->
    <div class="row">

        <div class="col-sm-6 col-md-3">
            <div class="count_panel panel-navy-blue">
                <div class="stats-title">
                    <h4><?php echo "Total User(s)"?></h4>
                    <i class="fa fa-users"></i>
                </div>
                <p class="currency_text "><?php echo (@$total_user?$total_user:'0'); ?></p>
                <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Your Total User"></i>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="count_panel panel-four">
                <div class="stats-title">
                    <h4><?php echo "Deposits"?></h4>
                    <i class="fa fa-university"></i>
                </div>
                <p class="currency_text "><?php echo @$coin_info->pair_with;?> <?php echo (@$total_deposit->deposit?number_format($total_deposit->deposit,2):'0.0'); ?></p>
                <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Total deposit amount"></i>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="count_panel panel-five">
                <div class="stats-title">
                    <h4><?php echo "Withdrawals"?></h4>
                    <i class="fa fa-reply-all"></i>
                </div>
                <p class="currency_text "><?php echo @$coin_info->pair_with;?> <?php echo (@$total_withdraw->withdraw?number_format($total_withdraw->withdraw,2):'0.0'); ?></p>
                <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Total withdraw amount"></i>
            </div>
        </div>

        <!-- <div class="col-sm-6 col-md-3">
            <div class="count_panel panel-six">
                <div class="stats-title">
                    <h4><?php echo display('token_sold')?></h4>
                    <i class="fa fa-database"></i>
                </div>
                <p class="currency_text "><?php echo @$coin_info->symbol;?> <?php echo (@$sold_token->soldtoken?(int)$sold_token->soldtoken:'0'); ?></p>
                <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Total release sold token."></i>
            </div>
        </div> -->

        <div class="col-sm-6 col-md-3">
            <div class="count_panel panel-blue">
                <div class="stats-title ">
                    <h4><?php echo "Fundings"?></h4>
                    <i class="fa fa-universal-access"></i>
                </div>
                <p class="currency_text"><?php //echo @$coin_info->symbol;?> <?php echo (@$token?$token:'0');?></p>
                <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Total Fundings"></i>
            </div>
        </div>

        <!-- <div class="col-sm-6 col-md-3">
            <div class="count_panel panel-seven">
                <div class="stats-title ">
                    <h4>Total Fees</h4>
                    <i class="fa fa-balance-scale"></i>
                </div>
                <p class="currency_text"><?php echo @$coin_info->pair_with;?> <?php echo (@$total_earning_fees?number_format($total_earning_fees, 2):'0.00');?></p>
                <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Total Earning Fees."></i>
            </div>
        </div> -->

        <div class="col-sm-6 col-md-3">
            <div class="count_panel panel-sky">
                <div class="stats-title ">
                    <h4><?php echo display('total_investment');?></h4>
                    <i class="fa fa-briefcase"></i>
                </div>
                <p class="currency_text"><?php echo @$coin_info->pair_with;?> <?php echo (@$total_investment->amount?number_format($total_investment->amount, 2):'0.00');?></p>
                <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Summation all user invest."></i>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="count_panel panel-orenge">
                <div class="stats-title ">
                    <h4><?php echo display('total_roi')?></h4>
                    <i class="fa fa-pie-chart"></i>
                </div>
                <p class="currency_text"><?php echo @$coin_info->pair_with;?> <?php echo (@$total_roi->amount?number_format($total_roi->pair_with, 2):'0.00');?></p>
                <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Total Return on investment (ROI)"></i>
            </div>
        </div>

        <!-- Flot Filled Area Chart -->
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-bd lobidrag">
                <div class="panel-heading">
                    <div class="panel-title">
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <h4>Investments Charts</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <canvas id="lineChart" height="140"></canvas>   
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-md-6">
            <div class="panel panel-bd lobidrag">
                <div class="panel-heading">
                    <div class="panel-title">
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <h4><?php echo display('withdraw');?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo display('user_id') ?></th>
                                    <th><?php echo display('payment_method') ?></th>
                                    <th><?php echo display('amount') ?></th>
                                    <th><?php echo display('status') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty(@$withdraw)) ?>
                                <?php $sl = 1; ?>
                                <?php foreach ($withdraw as $value) { ?>
                                <tr>
                                    <td><?php echo $value->user_id; ?></td>
                                    <td><?php echo $value->method; ?></td>
                                    <td><?php echo $value->amount+$value->fees_amount; ?></td>
                                    <td>
                                        <?php if($value->status==2){?>
                                         <a class="btn btn-warning btn-sm"><?php echo display('pending_withdraw')?></a>
                                         <?php } else if($value->status==1){?>
                                         <a class="btn btn-success btn-sm"><?php echo display('success')?></a>
                                         <?php } else if($value->status==0){ ?>
                                         <a class="btn btn-danger btn-sm"><?php echo display('cancel')?></a>
                                         <?php } ?>
                                     </td>
                                    
                                </tr>
                                <?php } ?> 
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-6">
            <div class="panel panel-bd lobidrag">
                <div class="panel-heading">
                    <div class="panel-title">
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <h4><?php echo display('deposit');?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo display('user_id') ?></th>
                                    <th><?php echo display('payment_method') ?></th>
                                    <th><?php echo display('amount') ?></th>
                                    <th><?php echo display('status') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty(@$deposit)) ?>
                                <?php $sl = 1; ?>
                                <?php foreach ($deposit as $value) { ?>
                                <tr>
                                    <td><?php echo $value->user_id; ?></td>
                                    <td><?php echo $value->method; ?></td>
                                    <td><?php echo $value->amount+$value->fees_amount; ?></td>
                                    <td>
                                        <?php if($value->status==2){?>
                                         <a class="btn btn-warning btn-sm"><?php echo display('pending_withdraw')?></a>
                                         <?php } else if($value->status==1){?>
                                         <a class="btn btn-success btn-sm"><?php echo display('success')?></a>
                                         <?php } else if($value->status==0){ ?>
                                         <a class="btn btn-danger btn-sm"><?php echo display('cancel')?></a>
                                         <?php } ?>
                                     </td>
                                    
                                </tr>
                                <?php } ?> 
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div class="col-sm-12 col-md-12">
            <div class="panel panel-bd lobidrag">
                <div class="panel-heading">
                    <div class="panel-title">
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <h4><?php echo display('exchange');?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Exchange Type</th>
                                    <th>Crypto Qty</th>
                                    <th>Crypto Rate</th>
                                    <th>Complete Qty</th>
                                    <th>Available Qty</th>
                                    <th>Date</th>
                                    <th>status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty(@$exchange)) ?>
                                <?php $sl = 1; ?>
                                <?php foreach ($exchange as $value) { ?>
                                <tr>
                                    <td><?php echo $value->exchange_type; ?></td>
                                    <td><?php echo $value->crypto_qty; ?></td>
                                    <td><?php echo $value->crypto_rate; ?></td>
                                    <td><?php echo $value->complete_qty; ?></td>
                                    <td><?php echo $value->available_qty; ?></td>
                                    <td><?php echo $value->datetime; ?></td>
                                    <td>
                                        <?php if($value->status==2){?>
                                         <a class="btn btn-warning btn-sm"><?php echo display('running')?></a>
                                         <?php } else if($value->status==1){?>
                                         <a class="btn btn-success btn-sm"><?php echo display('complete')?></a>
                                         <?php } else if($value->status==0){ ?>
                                         <a class="btn btn-danger btn-sm"><?php echo display('cancel')?></a>
                                         <?php } ?>
                                     </td>
                                    
                                </tr>
                                <?php } ?> 
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> -->

</div>




<!-- Modal body load from ajax start-->
<div class="modal fade modal-success" id="newModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
   <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h1 class="modal-title"><?php echo display('profile');?></h1>
        </div>
        <div class="modal-body">
            <table>
                <tr><td><strong><?php echo display('name');?> : </strong></td> <td id="name"></td></tr>
                <tr><td><strong><?php echo display('email');?> : </strong></td> <td id="email"></td></tr>
                <tr><td><strong><?php echo display('mobile');?> : </strong></td> <td id="phone"></td></tr>
                <tr><td><strong><?php echo display('user_id');?> : </strong></td> <td id="user_id"></td></tr>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
    </div><!-- /.modal-content -->
  </div>
</div>
<!-- Modal body load from ajax end-->

<!-- Modal ajax call start -->
<script type="text/javascript">

    $(".AjaxModal").click(function(){
      var url = $(this).attr("href");
      var href = url.split("#");  
      
      jquery_ajax(href[1]);
    });

    function jquery_ajax(id) {
       $.ajax({
            url : "<?php echo site_url('backend/Ajax_load/user_info_load/')?>" + id,
            type: "GET",
            data: {'id':id},
            dataType: "JSON",
            success: function(data)
            {

                $('#name').text(data.f_name+' '+data.l_name);
                $('#email').text(data.email);
                $('#phone').text(data.phone);
                $('#user_id').text(data.user_id);
              
               
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });

    }
</script>