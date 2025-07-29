<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h2><?php echo (!empty($title)?$title:null) ?> </h2>
                    <?php if($payment_gateway->identity=="bitcoin"){?>
                            <span class="text-right">*** Only <a href='https://gourl.io/view/registration' target='_blank'>GoUrl</a> Coin Allowed</span>
                    <?php }?>
                </div>
            </div>
            <div class="panel-body">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <?php if ($payment_gateway->identity=='bitcoin') { ?>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="col-form-label col-sm-4">Callback Url</label>
                            <div class="input-group col-sm-8">
                                <input type="text" class="form-control" id="copyed" value="<?php echo base_url('gourl/lib/cryptobox.callback.php'); ?>" readonly>
                                <span class="input-group-btn">
                                    <button class="btn btn-primary" type="button" onclick="myFunction()">Copy</button>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($payment_gateway->identity=='payeer') { ?>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="col-form-label col-sm-4">Success Url</label>
                            <div class="input-group col-sm-8">
                                <input type="text" class="form-control" id="copyed" value="<?php echo base_url('payment_callback/payeer_confirm'); ?>" readonly>
                                <span class="input-group-btn">
                                    <button class="btn btn-primary" type="button" onclick="myFunction()">Copy</button>
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="col-form-label col-sm-4">Cancel Url</label>
                            <div class="input-group col-sm-8">
                                <input type="text" class="form-control" id="copyed1" value="<?php echo base_url('payment_callback/payeer_cancel'); ?>" readonly>
                                <span class="input-group-btn">
                                    <button class="btn btn-primary" type="button" onclick="myFunction1()">Copy</button>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="border_preview">
                <?php echo form_open_multipart("backend/payment_gateway/payment_gateway/form/$payment_gateway->id") ?>
                <?php echo form_hidden('id', $payment_gateway->id) ?> 
                <?php echo form_hidden('identity', $payment_gateway->identity) ?> 
                    <div class="form-group row">
                        <label for="agent" class="col-sm-4 col-form-label"><?php echo display('gateway_name') ?></label>
                        <div class="col-sm-6">
                            <input name="agent" value="<?php echo $payment_gateway->agent ?>" class="form-control" type="text" id="agent">
                        </div>

                        <?php 
                        if ($payment_gateway->identity=='bitcoin') {
                            $level1 = display('public_key');
                            $level2 = display('private_key');

                            echo "<div class='col-sm-2'>
                               <a href='https://gourl.io/view/registration' target='_blank'>Create Account</a>
                            </div>";
                        }
                        else if ($payment_gateway->identity=='payeer') {
                            $level1 = display('shop_id');
                            $level2 = display('secret_key');

                            echo "<div class='col-sm-2'>
                               <a href='https://payeer.com/en/account/?register=yes' target='_blank'>Create Account</a>
                            </div>";
                        }
                        else if ($payment_gateway->identity=='phone') {
                            $level1 = display('phone');
                            $level2 = display('name');
                        }
                        else if ($payment_gateway->identity=='paypal') {
                            $level1 = display('client_id');
                            $level2 = display('client_secret');

                            echo "<div class='col-sm-2'>
                               <a href='https://www.paypal.com' target='_blank'>Create Account</a>
                            </div>";
                        }
                        else if ($payment_gateway->identity=='coinpayment') {

                            $level1 = display('public_key');
                            $level2 = display('private_key');

                            echo "<div class='col-sm-2'>
                               <a href='https://www.coinpayments.net/' target='_blank'>Create Account</a>
                            </div>";
                        }
                        else if ($payment_gateway->identity=='stripe') {
                            $level1 = display('public_key');
                            $level2 = display('private_key');

                            echo "<div class='col-sm-2'>
                               <a href='https://stripe.com/' target='_blank'>Create Account</a>
                            </div>";
                        }
                        else {
                            $level1 = display('public_key');
                            $level2 = display('private_key');

                        }
                    ?>

                    </div>


                    <?php if ($payment_gateway->identity=='bitcoin') {
                        $public_key = unserialize($payment_gateway->public_key);
                        $private_key = unserialize($payment_gateway->private_key);
                        $i=0;
                        foreach ($public_key as $key => $value) { 
                            $pb_key[$i] = $key;
                            $pb_val[$i] = $value;

                            $i++;

                        }
                        $j=0;
                        foreach ($private_key as $key => $value) { 
                            $piv_key[$j] = $key;
                            $piv_val[$j] = $value;

                            $j++;

                        }
                    ?>
                        <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Bitcoin</label>
                        <input name="key1" value="bitcoin" type="hidden"/>
                        <div class="col-sm-8">                            
                            <div class="col-sm-6" style="padding-left: 0;">
                                <input name="public_key" value="<?php echo @$pb_val[0] ?>" class="form-control col-sm-12" type="text" id="public_key" placeholder="<?php echo $level1; ?>">
                            </div>                            
                            <div class="col-sm-6" style="padding-left: 0;">
                                <input name="private_key" value="<?php echo @$piv_val[0] ?>" class="form-control col-sm-12" type="text" id="private_key" placeholder="<?php echo $level2; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Bitcoincash</label>
                        <input name="key2" value="bitcoincash" type="hidden"/>
                        <div class="col-sm-8">                            
                            <div class="col-sm-6" style="padding-left: 0;">
                                <input name="public_key2" value="<?php echo @$pb_val[1] ?>" class="form-control col-sm-12" type="text" id="public_key2" placeholder="<?php echo $level1; ?>">
                            </div>                            
                            <div class="col-sm-6" style="padding-left: 0;">
                                <input name="private_key2" value="<?php echo @$piv_val[1] ?>" class="form-control col-sm-12" type="text" id="private_key2" placeholder="<?php echo $level2; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Litecoin</label>
                        <input name="key3" value="litecoin" type="hidden"/>
                        <div class="col-sm-8">                            
                            <div class="col-sm-6" style="padding-left: 0;">
                                <input name="public_key3" value="<?php echo @$pb_val[2] ?>" class="form-control col-sm-12" type="text" id="public_key3" placeholder="<?php echo $level1; ?>">
                            </div>                            
                            <div class="col-sm-6" style="padding-left: 0;">
                                <input name="private_key3" value="<?php echo @$piv_val[2] ?>" class="form-control col-sm-12" type="text" id="private_key3" placeholder="<?php echo $level2; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Dash</label>
                        <input name="key4" value="dash" type="hidden"/>
                        <div class="col-sm-8">                            
                            <div class="col-sm-6" style="padding-left: 0;">
                                <input name="public_key4" value="<?php echo @$pb_val[3] ?>" class="form-control col-sm-12" type="text" id="public_key4" placeholder="<?php echo $level1; ?>">
                            </div>                            
                            <div class="col-sm-6" style="padding-left: 0;">
                                <input name="private_key4" value="<?php echo @$piv_val[3] ?>" class="form-control col-sm-12" type="text" id="private_key4" placeholder="<?php echo $level2; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Dogecoin</label>
                        <input name="key5" value="dogecoin" type="hidden"/>
                        <div class="col-sm-8">                            
                            <div class="col-sm-6" style="padding-left: 0;">
                                <input name="public_key5" value="<?php echo @$pb_val[4] ?>" class="form-control col-sm-12" type="text" id="public_key5" placeholder="<?php echo $level1; ?>">
                            </div>                            
                            <div class="col-sm-6" style="padding-left: 0;">
                                <input name="private_key5" value="<?php echo @$piv_val[4] ?>" class="form-control col-sm-12" type="text" id="private_key5" placeholder="<?php echo $level2; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Speedcoin</label>
                        <input name="key6" value="speedcoin" type="hidden"/>
                        <div class="col-sm-8">                            
                            <div class="col-sm-6" style="padding-left: 0;">
                                <input name="public_key6" value="<?php echo @$pb_val[5] ?>" class="form-control col-sm-12" type="text" id="public_key6" placeholder="<?php echo $level1; ?>">
                            </div>                            
                            <div class="col-sm-6" style="padding-left: 0;">
                                <input name="private_key6" value="<?php echo @$piv_val[5] ?>" class="form-control col-sm-12" type="text" id="private_key6" placeholder="<?php echo $level2; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Reddcoin</label>
                        <input name="key7" value="reddcoin" type="hidden"/>
                        <div class="col-sm-8">                            
                            <div class="col-sm-6" style="padding-left: 0;">
                                <input name="public_key7" value="<?php echo @$pb_val[6] ?>" class="form-control col-sm-12" type="text" id="public_key7" placeholder="<?php echo $level1; ?>">
                            </div>                            
                            <div class="col-sm-6" style="padding-left: 0;">
                                <input name="private_key7" value="<?php echo @$piv_val[6] ?>" class="form-control col-sm-12" type="text" id="private_key7" placeholder="<?php echo $level2; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Potcoin</label>
                        <input name="key8" value="potcoin" type="hidden"/>
                        <div class="col-sm-8">                            
                            <div class="col-sm-6" style="padding-left: 0;">
                                <input name="public_key8" value="<?php echo @$pb_val[7] ?>" class="form-control col-sm-12" type="text" id="public_key8" placeholder="<?php echo $level1; ?>">
                            </div>                            
                            <div class="col-sm-6" style="padding-left: 0;">
                                <input name="private_key8" value="<?php echo @$piv_val[7] ?>" class="form-control col-sm-12" type="text" id="private_key8" placeholder="<?php echo $level2; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Feathercoin</label>
                        <input name="key9" value="feathercoin" type="hidden"/>
                        <div class="col-sm-8">                            
                            <div class="col-sm-6" style="padding-left: 0;">
                                <input name="public_key9" value="<?php echo @$pb_val[8] ?>" class="form-control col-sm-12" type="text" id="public_key9" placeholder="<?php echo $level1; ?>">
                            </div>                            
                            <div class="col-sm-6" style="padding-left: 0;">
                                <input name="private_key9" value="<?php echo @$piv_val[8] ?>" class="form-control col-sm-12" type="text" id="private_key9" placeholder="<?php echo $level2; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Vertcoin</label>
                        <input name="key10" value="vertcoin" type="hidden"/>
                        <div class="col-sm-8">                            
                            <div class="col-sm-6" style="padding-left: 0;">
                                <input name="public_key10" value="<?php echo @$pb_val[9] ?>" class="form-control col-sm-12" type="text" id="public_key10" placeholder="<?php echo $level1; ?>">
                            </div>                            
                            <div class="col-sm-6" style="padding-left: 0;">
                                <input name="private_key10" value="<?php echo @$piv_val[9] ?>" class="form-control col-sm-12" type="text" id="private_key10" placeholder="<?php echo $level2; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Peercoin</label>
                        <input name="key11" value="peercoin" type="hidden"/>
                        <div class="col-sm-8">                            
                            <div class="col-sm-6" style="padding-left: 0;">
                                <input name="public_key11" value="<?php echo @$pb_val[10] ?>" class="form-control col-sm-12" type="text" id="public_key11" placeholder="<?php echo $level1; ?>">
                            </div>                            
                            <div class="col-sm-6" style="padding-left: 0;">
                                <input name="private_key11" value="<?php echo @$piv_val[10] ?>" class="form-control col-sm-12" type="text" id="private_key11" placeholder="<?php echo $level2; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Monetaryunit</label>
                        <input name="key12" value="monetaryunit" type="hidden"/>
                        <div class="col-sm-8">                            
                            <div class="col-sm-6" style="padding-left: 0;">
                                <input name="public_key12" value="<?php echo @$pb_val[11] ?>" class="form-control col-sm-12" type="text" id="public_key12" placeholder="<?php echo $level1; ?>">
                            </div>                            
                            <div class="col-sm-6" style="padding-left: 0;">
                                <input name="private_key12" value="<?php echo @$piv_val[11] ?>" class="form-control col-sm-12" type="text" id="private_key12" placeholder="<?php echo $level2; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Universalcurrency</label>
                        <input name="key13" value="universalcurrency" type="hidden"/>
                        <div class="col-sm-8">                            
                            <div class="col-sm-6" style="padding-left: 0;">
                                <input name="public_key13" value="<?php echo @$pb_val[12] ?>" class="form-control col-sm-12" type="text" id="public_key13" placeholder="<?php echo $level1; ?>">
                            </div>                            
                            <div class="col-sm-6" style="padding-left: 0;">
                                <input name="private_key13" value="<?php echo @$piv_val[12] ?>" class="form-control col-sm-12" type="text" id="private_key13" placeholder="<?php echo $level2; ?>">
                            </div>
                        </div>
                    </div>
                    <?php }elseif ($payment_gateway->identity=='coinpayment') {

                            if (is_string($payment_gateway->data) && is_array(json_decode($payment_gateway->data, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false) {

                                $coinpaymentdata    = json_decode($payment_gateway->data, true);

                                $marcent_id         = $coinpaymentdata['marcent_id'];
                                $ipn_secret         = $coinpaymentdata['ipn_secret'];
                                $debug_email        = $coinpaymentdata['debug_email'];
                                $debuging_active    = $coinpaymentdata['debuging_active'];
                                $withdraw           = $coinpaymentdata['withdraw'];
                                
                                if($debuging_active==1){
                                    $check = "checked='checked'";
                                }
                                else{
                                    $check = "";
                                }

                            } else {

                                $marcent_id         = "";
                                $ipn_secret         = "";
                                $debug_email        = "";
                                $check    = "";

                            }

                    ?>

                            <div class="form-group row">
                                <label for="public_key" class="col-sm-4 col-form-label"><?php echo $level1; ?> <i class="text-danger">*</i></label>
                                <div class="col-sm-6">
                                    <input name="public_key" value="<?php echo $payment_gateway->public_key ?>" class="form-control" type="text" id="public_key">
                                </div>
                            </div> 
                            <div class="form-group row">
                                <label for="private_key" class="col-sm-4 col-form-label"><?php echo $level2; ?> <i class="text-danger">*</i></label>
                                <div class="col-sm-6">
                                    <input name="private_key" value="<?php echo $payment_gateway->private_key ?>" class="form-control" type="text" id="private_key">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="mercent_id" class="col-sm-4 col-form-label">Your Merchant ID <i class="text-danger">*</i></label>
                                <div class="col-sm-6">
                                    <input name="mercent_id" value="<?=$marcent_id;?>" class="form-control" type="text" id="mercent_id"/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="ipn_secret" class="col-sm-4 col-form-label">IPN Secret <i class="text-danger">*</i></label>
                                <div class="col-sm-6">
                                    <input name="ipn_secret" value="<?=$ipn_secret;?>" class="form-control" type="text" id="ipn_secret">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="debug_email" class="col-sm-4 col-form-label">Debug Email <i class="text-danger">*</i></label>
                                <div class="col-sm-6">
                                    <input name="debug_email" value="<?=$debug_email;?>" class="form-control" type="text" id="debug_email">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="debuging_active" class="col-sm-4 col-form-label">Debuging Active </label>
                                <div class="col-sm-6">
                                    <input name="debuging_active" type="checkbox" id="debuging_active" <?=$check;?> > <?php echo display('active') ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="debuging_active" class="col-sm-4 col-form-label">Withdraw </label>
                                <div class="col-sm-6">
                                    <label class="radio-inline">
                                        <?php echo form_radio('coinpayment_wtdraw', '1', (($withdraw=='1' || $withdraw==null)?true:false)); ?>Automatic
                                     </label>
                                    <label class="radio-inline">
                                        <?php echo form_radio('coinpayment_wtdraw', '0', (($withdraw=="0")?true:false) ); ?>Manual
                                     </label>
                                </div>
                            </div>

                    <?php } else { ?>

                    <div class="form-group row">
                        <label for="public_key" class="col-sm-4 col-form-label"><?php echo $level1; ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <input name="public_key" value="<?php echo $payment_gateway->public_key ?>" class="form-control" type="text" id="public_key">
                        </div>
                    </div> 
                    <div class="form-group row">
                        <label for="private_key" class="col-sm-4 col-form-label"><?php echo $level2; ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <input name="private_key" value="<?php echo $payment_gateway->private_key ?>" class="form-control" type="text" id="private_key">
                        </div>
                    </div>
                    <?php } ?>

                    <?php if ($payment_gateway->identity=='paypal') { ?>
                    <div class="form-group row">
                        <label for="secret_key" class="col-sm-4 col-form-label">Mode</label>
                        <div class="col-sm-6">
                            <label class="radio-inline">
                                <?php echo form_radio('secret_key', 'sandbox', (($payment_gateway->secret_key=='sandbox' || $payment_gateway->secret_key==null)?true:false)); ?>SandBox
                             </label>
                            <label class="radio-inline">
                                <?php echo form_radio('secret_key', 'live', (($payment_gateway->secret_key=="live")?true:false) ); ?>Live
                             </label> 
                        </div>
                    </div>
                    <?php } ?>
                    <div class="form-group row">
                        <label for="status" class="col-sm-4 col-form-label"><?php echo display('status') ?></label>
                        <div class="col-sm-6">
                            <label class="radio-inline">
                                <?php echo form_radio('status', '1', (($payment_gateway->status==1 || $payment_gateway->status==null)?true:false)); ?><?php echo display('active') ?>
                             </label>
                            <label class="radio-inline">
                                <?php echo form_radio('status', '0', (($payment_gateway->status=="0")?true:false) ); ?><?php echo display('inactive') ?>
                             </label> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-9 col-sm-offset-3">
                            <a href="<?php echo base_url('admin'); ?>" class="btn btn-primary  w-md m-b-5"><?php echo display("cancel") ?></a>
                            <button type="submit" class="btn btn-success  w-md m-b-5"><?php echo $payment_gateway->id?display("update"):display("create") ?></button>
                        </div>
                    </div>
                <?php echo form_close() ?>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

 