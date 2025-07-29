<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
        <div class="panel panel-bd ">
            <div class="panel-heading ui-sortable-handle">
                <div class="panel-title" style="max-width: calc(100% - 180px);">
                    <h4><?php echo display('transection');?></h4>
                </div>
            </div>
            <div class="panel-body">
                        
                    <div class="table-responsive">
                        <table class="table table-bordered">
                        <thead>
                            <tr>
                            <th><?php echo display('type');?></th>
                            <th><?php echo display('amount');?> (<?php echo $coin_setup->pair_with;?>)</th>
                            <th><?php echo display('fees');?> (<?php echo $coin_setup->pair_with;?>)</th>
                            <th><?php echo display('total');?></th>
                            </tr>
                        </thead>

                        <tbody>

                            <tr>
                                <th><?php echo $deposit_amount->transaction_type?$deposit_amount->transaction_type:'DEPOSIT' ?></th>
                                <td><?php echo $deposit_amount->transaction_amount ?></td>
                                <td><?php echo $deposit_amount_fees->transaction_fees ?></td>
                                <td><?php echo $deposit_amount->transaction_amount ?></td>
                            </tr>

                            <!-- <tr>
                                <th><?php echo $credited_amount->transaction_type?$credited_amount->transaction_type:'CREDITED' ?></th>
                                <td><?php echo $credited_amount->transaction_amount ?></td>
                                <td><?php echo $credited_amount_fees->transaction_fees ?></td>
                                <td><?php echo $credited_amount->transaction_amount+$credited_amount_fees->transaction_fees ?></td>
                            </tr> -->

                           <!--  <tr>
                                <th><?php echo $exchange_cancel_amount->transaction_type?$exchange_cancel_amount->transaction_type:'EXCHANGE_CANCEL' ?></th>
                                <td><?php echo $exchange_cancel_amount->transaction_amount ?></td>
                                <td><?php echo $exchange_cancel_amount_fees->transaction_fees ?></td>
                                <td><?php echo $exchange_cancel_amount->transaction_amount+$exchange_cancel_amount_fees->transaction_fees ?></td>
                            </tr> -->

                            <tr>
                                <th><?php echo $recevied_amount->transaction_type?$recevied_amount->transaction_type:'RECEIVED' ?></th>
                                <td><?php echo $recevied_amount->transaction_amount ?></td>
                                <td>0.00000000</td>
                                <td><?php echo $recevied_amount->transaction_amount; ?></td>
                            </tr>

                            <!-- <tr>
                                <th><?php echo $sell_amount->transaction_type?$sell_amount->transaction_type:'SELL' ?></th>
                                <td><?php echo $sell_amount->transaction_amount ?></td>
                                <td><?php echo $sell_amount_fees->transaction_fees ?></td>
                                <td><?php echo $sell_amount->transaction_amount-$sell_amount_fees->transaction_fees ?></td>
                            </tr> -->

                            <!-- <tr>
                                <th><?php echo @$return_amount->transaction_type?@$return_amount->transaction_type:'ADJUSTMENT' ?></th>
                                <td><?php echo @$return_amount->transaction_amount+$return_fees->transaction_fees; ?></td>
                                <td>0.00000000</td>
                                <td><?php echo @$return_amount->transaction_amount+$return_fees->transaction_fees; ?></td>
                            </tr> -->

                            <tr>
                                <th><?php echo display('roi')?></th>
                                <td><?php echo $roi_amount->amount ?></td>
                                <td>0.00000000</td>
                                <td><?php echo $roi_amount->amount ?></td>
                            </tr>

                            <!-- <tr>
                                <th><?php echo display('referral')?></th>
                                <td><?php echo $referral_amount->amount ?></td>
                                <td>0.00000000</td>
                                <td><?php echo $referral_amount->amount ?></td>
                            </tr> -->


                            <tr>
                                <td colspan="3" class="text-success text-center"><?php echo display('total');?> = </td>
                                <td><?php echo $coin_setup->pair_with;?> <?php echo ($deposit_amount->transaction_amount+$exchange_cancel_amount->transaction_amount+$exchange_cancel_amount_fees->transaction_fees+$recevied_amount->transaction_amount+$sell_amount->transaction_amount+$roi_amount->amount+$credited_amount->transaction_amount+$return_amount->transaction_amount+$return_fees->transaction_fees+$referral_amount->amount)-($sell_amount_fees->transaction_fees+$credited_amount_fees->transaction_fees); ?></td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><?php echo display('type');?></th>
                                <th><?php echo display('amount');?></th>
                                <th><?php echo display('fees');?></th>
                                <th><?php echo display('total');?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th><?php echo $transfer_amount->transaction_type?$transfer_amount->transaction_type:'TRANSFER' ?></th>
                                <td><?php echo $transfer_amount->transaction_amount ?></td>
                                <td><?php echo $transfer_amount_fees->transaction_fees ?></td>
                                <td><?php echo $transfer_amount->transaction_amount+$transfer_amount_fees->transaction_fees ?></td>
                            </tr>

                            <tr>
                                <th><?php echo $withdraw_amount->transaction_type?$withdraw_amount->transaction_type:'WITHDRAW' ?></th>
                                <td><?php echo $withdraw_amount->transaction_amount ?></td>
                                <td><?php echo $withdraw_amount_fees->transaction_fees ?></td>
                                <td><?php echo $withdraw_amount->transaction_amount+$withdraw_amount_fees->transaction_fees ?></td>
                            </tr>

                            <!-- <tr>
                                <th><?php echo $buy_amount->transaction_type?$buy_amount->transaction_type:'BUY' ?></th>
                                <td><?php echo $buy_amount->transaction_amount ?></td>
                                <td><?php echo $buy_amount_fees->transaction_fees; ?></td>
                                <td><?php echo $buy_amount->transaction_amount+$buy_amount_fees->transaction_fees ?></td>
                            </tr> -->
                            <tr>
                                <th><?php echo $invest_amount->transaction_type?$invest_amount->transaction_type:'INVESTMENT' ?></th>
                                <td><?php echo $invest_amount->transaction_amount ?></td>
                                <td>0.00000000</td>
                                <td><?php echo $invest_amount->transaction_amount ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-danger text-center"><?php echo display('total');?> = </td>
                                <td><?php echo $coin_setup->pair_with;?> <?php echo $transfer_amount->transaction_amount+$transfer_amount_fees->transaction_fees+$withdraw_amount->transaction_amount+$withdraw_amount_fees->transaction_fees+$buy_amount->transaction_amount+$buy_amount_fees->transaction_fees+$invest_amount->transaction_amount ?></td>
                            </tr>
                            <tr >
                                <th colspan="4" class="text-success text-center"><?php echo display('your_total_balance_is');?> = <?php echo $coin_setup->pair_with;?> <?php echo ($deposit_amount->transaction_amount+$exchange_cancel_amount->transaction_amount+$exchange_cancel_amount_fees->transaction_fees+$recevied_amount->transaction_amount+$sell_amount->transaction_amount+$roi_amount->amount+$credited_amount->transaction_amount+$return_amount->transaction_amount+$return_fees->transaction_fees+$referral_amount->amount)-($transfer_amount->transaction_amount+$transfer_amount_fees->transaction_fees+$withdraw_amount->transaction_amount+$withdraw_amount_fees->transaction_fees+$buy_amount->transaction_amount+$buy_amount_fees->transaction_fees+$invest_amount->transaction_amount+$sell_amount_fees->transaction_fees+$credited_amount_fees->transaction_fees) ?></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
        <div class="panel panel-bd ">
            <div class="panel-heading ui-sortable-handle">
                <div class="panel-title" style="max-width: calc(100% - 180px);">
                    <h4><?php echo display('transection');?></h4>
                </div>
            </div>
            <div class="panel-body">
                <div class="border_preview">

                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered datatable2">
                            <thead>
                                <tr>
                                    <th><?php echo display('date');?></th>
                                    <th><?php echo display('transection_category');?></th>
                                    <th><?php echo display('balance');?>(<?php echo $coin_setup->pair_with;?>)</th>
                                    <th><?php echo display('fees');?>(<?php echo $coin_setup->pair_with;?>)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($transection!=NULL){ 
                                    foreach ($transection as $key => $value) {  
                                ?>
                                <tr>
                                    <td><?php echo $value->date;?></td>
                                    <td><?php echo $value->transaction_type;?></td>
                                    <td><?php echo $value->transaction_amount;?></td>
                                    <td><?php echo $value->transaction_fees;?></td>
                                </tr>
                                <?php } } ?>

                            </tbody>
                        </table>
                        <?php echo $links; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>