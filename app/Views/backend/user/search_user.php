<div class="content">
	<div class="row">
		<div class="col-sm-12">

			 <?php echo form_open("backend/user/user/user_details") ?>
			<div class="form-group row">
                <label for="user_id" class="col-sm-2 col-form-label">User ID: </label>
                <div class="col-xs-2 col-sm-10 col-md-4 m-b-20">
                    <input name="user_id" class="form-control" type="search" id="user_id" value="<?php echo @$user->user_id ?>">
                </div>
                <div class="col-xs-2 col-sm-10 col-md-4 m-b-20">
                    <button type="submit" class="btn btn-success  w-md m-b-5">Search</button>
                </div>

            </div>
            <?php echo form_close() ?>

		</div>
	</div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 m-b-20">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab1" data-toggle="tab"><?php echo display('user_profile') ?></a></li>
                <li><a href="#tab2" data-toggle="tab">Balance</a></li>
                <li><a href="#tab3" data-toggle="tab">Transaction Log</a></li>
                <li><a href="#tab4" data-toggle="tab">Earning History</a></li>
            </ul>
            <!-- Tab panels -->
            <div class="tab-content">
                <div class="tab-pane fade in active" id="tab1">
                    <div class="panel-body">
		                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		                    <div class="form-group row">
		                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('user_id') ?></label>
		                        <div class="col-sm-8">
		                            <?php echo @$user->user_id ?></span>
		                        </div>
		                    </div>
		                    <div class="form-group row">
		                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('referral_id') ?></label>
		                        <div class="col-sm-8">
		                            <?php echo @$user->referral_id ?></span>
		                        </div>
		                    </div>
		                    <div class="form-group row">
		                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('language') ?></label>
		                        <div class="col-sm-8">
		                            <?php echo @$user->language ?></span>
		                        </div>
		                    </div>
		                    <div class="form-group row">
		                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('firstname') ?></label>
		                        <div class="col-sm-8">
		                            <?php echo @$user->first_name ?></span>
		                        </div>
		                    </div>
		                    <div class="form-group row">
		                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('lastname') ?></label>
		                        <div class="col-sm-8">
		                            <?php echo @$user->last_name ?></span>
		                        </div>
		                    </div>
		                    <div class="form-group row">
		                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('email') ?></label>
		                        <div class="col-sm-8">
		                            <?php echo @$user->email ?></span>
		                        </div>
		                    </div>
		                    <div class="form-group row">
		                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('mobile') ?></label>
		                        <div class="col-sm-8">
		                            <?php echo @$user->phone ?></span>
		                        </div>
		                    </div>
		                    <div class="form-group row">
		                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('registered_ip') ?></label>
		                        <div class="col-sm-8">
		                            <?php echo @$user->ip ?></span>
		                        </div>
		                    </div>
		                    <div class="form-group row">
		                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('status') ?></label>
		                        <div class="col-sm-8">
		                            <?php echo (@$user->status==1)?display('active'):display('inactive'); ?></span>
		                        </div>
		                    </div>
		                    <div class="form-group row">
		                        <label for="cid" class="col-sm-4 col-form-label">Registered Date</label>
		                        <div class="col-sm-8">
		                            <?php 
		                                $date=date_create(@$user->created);
		                                echo date_format($date,"jS F Y");  
		                            ?></span>
		                        </div>
		                    </div>
		                </div>
		            </div>
                </div>
                <div class="tab-pane fade" id="tab2">
                    <div class="panel-body">
                    	<table class="datatable1 table table-bordered table-hover table-striped">
		                    <thead>
		                        <tr> 
		                            <th><?php echo display('type')?></th>
		                            <th><?php echo display('amount')?></th>
		                        </tr>
		                    </thead>
		                    <tbody>
	                       			<tr>
		                                <td><?php echo $deposit_amount->transaction_type?$deposit_amount->transaction_type:'DEPOSIT' ?></td>
		                                <td><?php echo $deposit_amount->transaction_amount?number_format($deposit_amount->transaction_amount,2):0.00; ?></td>
		                            </tr>
		                            <tr>
		                                <td><?php echo $credited_amount->transaction_type?$credited_amount->transaction_type:'CREDITED' ?></td>
		                                <td><?php echo $credited_amount->transaction_amount?number_format($credited_amount->transaction_amount,2):0.00; ?></td>
		                            </tr>
		                            <tr>
		                                <td><?php echo $exchange_cancel_amount->transaction_type?$exchange_cancel_amount->transaction_type:'EXCHANGE_CANCEL' ?></td>
		                                <td><?php echo $exchange_cancel_amount->transaction_amount+$exchange_cancel_amount_fees->transaction_fees ?></td>
		                            </tr>
		                            <tr>
		                                <td><?php echo $recevied_amount->transaction_type?$recevied_amount->transaction_type:'RECEIVED' ?></td>
		                                <td><?php echo $recevied_amount->transaction_amount?number_format($recevied_amount->transaction_amount,2):0.00; ?></td>
		                            </tr>
		                            <tr>
		                                <td><?php echo $sell_amount->transaction_type?$sell_amount->transaction_type:'SELL' ?></td>
		                                <td><?php echo $sell_amount->transaction_amount-$sell_amount_fees->transaction_fees ?></td>
		                            </tr>
		                            <tr>
		                                <td><?php echo @$return_amount->transaction_type?@$return_amount->transaction_type:'ADJUSTMENT' ?></td>
		                                <td><?php echo @$return_amount->transaction_amount+$return_fees->transaction_fees; ?></td>
		                            </tr>
		                            <tr>
		                                <td><?php echo display('roi')?></td>
		                                <td><?php echo $roi_amount->amount?number_format($roi_amount->amount,2):0.00; ?></td>
		                            </tr>
		                            <tr>
		                                <td><?php echo display('referral')?></td>
		                                <td><?php echo $referral_amount->amount?number_format($referral_amount->amount):0.00; ?></td>
		                            </tr>
		                            <tr>
		                                <td class="text-success text-center"><?php echo display('total');?> = </td>
		                                <td><?php echo $coin_setup->pair_with;?> <?php echo ($deposit_amount->transaction_amount+$exchange_cancel_amount->transaction_amount+$exchange_cancel_amount_fees->transaction_fees+$recevied_amount->transaction_amount+$sell_amount->transaction_amount+$roi_amount->amount+$credited_amount->transaction_amount+$return_amount->transaction_amount+$return_fees->transaction_fees+$referral_amount->amount)-($sell_amount_fees->transaction_fees+$credited_amount_fees->transaction_fees); ?></td>
		                            </tr>

		                            <tr>
		                            	<th>Cost(-)</th>
		                            	<td></td>
		                            </tr>


		                            <tr>
		                                <td><?php echo $transfer_amount->transaction_type?$transfer_amount->transaction_type:'TRANSFER' ?></td>
		                                <td><?php echo $transfer_amount->transaction_amount+$transfer_amount_fees->transaction_fees; ?></td>
		                            </tr>
		                            <tr>
		                                <td><?php echo $withdraw_amount->transaction_type?$withdraw_amount->transaction_type:'WITHDRAW' ?></td>
		                                <td><?php echo $withdraw_amount->transaction_amount+$withdraw_amount_fees->transaction_fees ?></td>
		                            </tr>
		                            <tr>
		                                <td><?php echo $buy_amount->transaction_type?$buy_amount->transaction_type:'BUY' ?></td>
		                                <td><?php echo $buy_amount->transaction_amount+$buy_amount_fees->transaction_fees ?></td>
		                            </tr>
		                            <tr>
		                                <td><?php echo $invest_amount->transaction_type?$invest_amount->transaction_type:'INVESTMENT' ?></td>
		                                <td><?php echo $invest_amount->transaction_amount ?></td>
		                            </tr>
		                            <tr>
		                                <td class="text-danger text-center"><?php echo display('total');?> = </td>
		                                <td><?php echo $coin_setup->pair_with;?> <?php echo $transfer_amount->transaction_amount+$transfer_amount_fees->transaction_fees+$withdraw_amount->transaction_amount+$withdraw_amount_fees->transaction_fees+$buy_amount->transaction_amount+$buy_amount_fees->transaction_fees+$invest_amount->transaction_amount ?></td>
		                            </tr>
		                            <tr >
		                                <th colspan="2" class="text-success text-center"><?php echo display('your_total_balance_is');?> = <?php echo $coin_setup->pair_with;?> <?php echo ($deposit_amount->transaction_amount+$exchange_cancel_amount->transaction_amount+$exchange_cancel_amount_fees->transaction_fees+$recevied_amount->transaction_amount+$sell_amount->transaction_amount+$roi_amount->amount+$credited_amount->transaction_amount+$return_amount->transaction_amount+$return_fees->transaction_fees+$referral_amount->amount)-($transfer_amount->transaction_amount+$transfer_amount_fees->transaction_fees+$withdraw_amount->transaction_amount+$withdraw_amount_fees->transaction_fees+$buy_amount->transaction_amount+$buy_amount_fees->transaction_fees+$invest_amount->transaction_amount+$sell_amount_fees->transaction_fees+$credited_amount_fees->transaction_fees) ?></th>
		                            </tr>
	                        </tbody>
	                	</table>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab3">
                    <div class="panel-body">
                    	<table class="datatable1 table table-bordered table-hover table-striped">
		                    <thead>
                                <tr class="table-bg">
                                    <th>SL</th>
                                    <th>Transaction</th>
                                    <th>Amount(<?php echo $coin_setup->pair_with;?>)</th>
                                    <th>Fees</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
		                    <tbody>

                       <?php $i=1;  foreach ($transection as $key => $value) { ?>
                       		<tr>
                       			<td><?php echo $i ?></td>
                                <td><?php echo $value->transaction_type ?></td>
                                <td><?php echo number_format($value->transaction_amount,2); ?></td>
                                <td><?php echo number_format($value->transaction_fees,2); ?></td>
                                <td><?php echo $value->date; ?></td>
	                        </tr>

	                    <?php $i++; } ?>
	                    	</tbody>
	                	</table>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab4">
                    <div class="panel-body">
                    	<table class="datatable1 table table-bordered table-hover table-striped">
		                    <thead>
                                <tr class="table-bg">
                                    <th>SL</th>
                                    <th>Type</th>
                                    <th>Amount(<?php echo $coin_setup->pair_with;?>)</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
		                    <tbody>

                       <?php $i=1;  foreach ($earning as $key => $value) { ?>
                       		<tr>
                       			<td><?php echo $i;?></td>
                                <td><?php echo $value->earning_type; ?></td>
                                <td><?php echo number_format($value->amount,2); ?></td>
                                <td><?php echo $value->date; ?></td>
	                        </tr>

	                    <?php $i++; } ?>
	                    	</tbody>
	                	</table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// $(document).ready(function(){

//  function load_country_data(page, user)
//  {
// 	$.ajax({
// 		url:"<?php //echo base_url(); ?>backend/user/user/ajax_tradelist/"+user+"/"+page,
// 		method:"GET",
// 		dataType:"json",
// 		success:function(data)
// 		{
// 			console.log(data);
// 			$('#user_tradelist').html(data.country_table);
// 			$('#pagination_link').html(data.pagination_link);
// 		}
// 	});
//  }
//  var user = $('#user_id').val();
//  load_country_data(1, user);

// 	$(document).on("click", ".pagination li a", function(event){
// 		event.preventDefault();
// 		var page = $(this).data("ci-pagination-page");
// 		var user = $('#user_id').val();
// 		load_country_data(page, user);
// 	});
// });

</script>