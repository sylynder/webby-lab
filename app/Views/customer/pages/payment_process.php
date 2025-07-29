<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h2><?php echo display("deposit"); ?></h2>
                </div>
            </div>
            <div class="panel-body">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="border_preview">

                        <?php if ($deposit->method=='bitcoin') { ?>      



    <!-- Bootstrap4 CSS - -->
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" crossorigin="anonymous">    -->
      
    <!-- Note - If your website not use Bootstrap4 CSS as main style, please use custom css style below and delete css line above. 
    It isolate Bootstrap CSS to a particular class 'bootstrapiso' to avoid css conflicts with your site main css style -->
    <!-- <link rel="stylesheet" href="css/bootstrapcustom.min.css" crossorigin="anonymous"> -->

                           
    <!-- JS -->
   <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js" crossorigin="anonymous"></script>-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" crossorigin="anonymous"></script>
    <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" crossorigin="anonymous"></script>-->
    <script defer src="https://use.fontawesome.com/releases/v5.0.9/js/all.js" crossorigin="anonymous"></script>
    <script src="<?php //echo CRYPTOBOX_JS_FILES_PATH; ?><?php echo base_url("gourl/js/support.min.js"); ?>" crossorigin="anonymous"></script> 

    <!-- CSS for Payment Box -->
    <style>
            html { font-size: 14px; }
            @media (min-width: 768px) { html { font-size: 16px; } .tooltip-inner { max-width: 350px; } }
            .mncrpt .container { max-width: 980px; }
            .mncrpt .box-shadow { box-shadow: 0 .25rem .75rem rgba(0, 0, 0, .05); }
            img.radioimage-select { padding: 7px; border: solid 2px #ffffff; margin: 7px 1px; cursor: pointer; box-shadow: none; }
            img.radioimage-select:hover { border: solid 2px #a5c1e5; }
            img.radioimage-select.radioimage-checked { border: solid 2px #7db8d9; background-color: #f4f8fb; }
    </style>
<?php
     
    // Display payment box  
    echo $deposit_data['box']->display_cryptobox_bootstrap($deposit_data['coins'], $deposit_data['def_coin'], $deposit_data['def_language'], $deposit_data['custom_text'], $deposit_data['coinImageSize'], $deposit_data['qrcodeSize'], $deposit_data['show_languages'], $deposit_data['logoimg_path'], $deposit_data['resultimg_path'], $deposit_data['resultimgSize'], $deposit_data['redirect'], $deposit_data['method'], $deposit_data['debug']);
    

    // You can setup method='curl' in function above and use code below on this webpage -
    // if successful bitcoin payment received .... allow user to access your premium data/files/products, etc.
    // if ($box->is_paid()) { 



    // }


?>




                        <?php } elseif ($deposit->method=='payeer') { ?>
                            <table class="table table-bordered">
                                <tr>
                                    <th><?php echo display("user_id") ?></th>
                                    <td class="text-right"><?php echo $deposit->user_id ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo display("payment_gateway") ?></th>
                                    <td class="text-right"><?php echo $deposit->method ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo display("amount") ?></th>
                                    <td class="text-right"><?php echo $coininfo->pair_with." "; echo $deposit->amount ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo display("fees") ?></th>
                                    <td class="text-right"><?php echo $coininfo->pair_with." "; echo (float)@$deposit->fees_amount ?></td>
                                </tr>
                                <tr>
                                    <th>Total</th>
                                    <td class="text-right"><?php echo $coininfo->pair_with." "; echo (float)@$deposit->amount+(float)@$deposit->fees_amount ?></td>
                                </tr>
                            </table>
                            <form method="post" action="https://payeer.com/merchant/">
                            <input type="hidden" name="m_shop" value="<?php echo $deposit_data['m_shop'] ?>">
                            <input type="hidden" name="m_orderid" value="<?php echo $deposit_data['m_orderid'] ?>">
                            <input type="hidden" name="m_amount" value="<?php echo $deposit_data['m_amount'] ?>">
                            <input type="hidden" name="m_curr" value="<?php echo $deposit_data['m_curr'] ?>">
                            <input type="hidden" name="m_desc" value="<?php echo $deposit_data['m_desc'] ?>">
                            <input type="hidden" name="m_sign" value="<?php echo $deposit_data['sign'] ?>">
                           
                            <input type="submit" name="m_process" value="Payment Process" class="btn btn-success w-md m-b-5" />

                            <a href="<?php echo base_url('customer/deposit'); ?>" class="btn btn-primary  w-md m-b-5"><?php echo display("cancel") ?></a>
                            
                            <br>
                            <br>
                            <br>
                            </form>

                        <?php } elseif ($deposit->method=='paypal')  { ?>

                            <table class="table table-bordered">
                                <tr>
                                    <th><?php echo display("user_id") ?></th>
                                    <td class="text-right"><?php echo $deposit->user_id ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo display("payment_gateway") ?></th>
                                    <td class="text-right"><?php echo $deposit->method ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo display("amount") ?></th>
                                    <td class="text-right"><?php echo $coininfo->pair_with." "; echo $deposit->amount ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo display("fees") ?></th>
                                    <td class="text-right"><?php echo $coininfo->pair_with." "; echo @$deposit->fees_amount ?></td>
                                </tr>
                                <tr>
                                    <th>Total</th>
                                    <td class="text-right"><?php echo $coininfo->pair_with." "; echo (float)$deposit->amount+(float)@$deposit->fees_amount ?></td>
                                </tr>
                            </table>

                            <a class="btn btn-success w-md m-b-5 text-right" href="<?php echo $deposit_data['approval_url'] ?>">Payment Process</a>
                        <?php } elseif ($deposit->method=='coinpayment')  { ?>
                            
                                    <strong>Important</strong></br>
                                    <ul>
                                        <li>
                                        Send Only <strong><?=$deposit->currency_symbol;?></strong>
                                        deposit address. Sending any other coin or token to this address may result in the loss of your deposit.</li>
                                    </ul>
                                    <br>
                                    <center>
                                    <div class="diposit-address" style="margin-top: 25px">
                                        <div class="label">
                                            <?php echo $deposit->currency_symbol;?> Diposit Address.
                                        </div>
                                        <div class="dip_address">
                                            <strong><input type="text" id="copyed" value="<?=$deposit_data['result']['address']?>" readonly="readonly"/></strong>
                                        </div>
                                        <div class="copy_address" style="margin-top: 10px">
                                            <button  class="btn btn-primary" onclick="copyFunction()">Copy Address</button>
                                        </div>
                                        <div class="diposit-qrcode" style="margin-top: 25px">
                                            <div class="qrcode">
                                                <img src="<?=$deposit_data['result']['qrcode_url']?>" />
                                            </div>
                                        </div>
                                        <div class="deposit-balance" style="margin-top: 5px">
                                            <h2 style="font-family: inherit;"><?php echo number_format($deposit->amount+(float)@$deposit->fees_amount,8)." <span style='font-weight:normal'>".$deposit->currency_symbol; ?></span></h2>
                                        </div>
                                    </div>
                                    </center>

                                    <div class="please-note" style="margin-top: 10px">
                                        <div class="label_note">
                                            Please Note
                                        </div>
                                        <div class="textnote">
                                            <ul>
                                                <li>Coins will be deposited immediately after <font color="#03a9f4"><?=$deposit_data['result']['confirms_needed'];?></font> network confirmations</li>
                                                <li>You can track its progress on the <a target="_blank" href="<?=$deposit_data['result']['status_url'];?>">history</a>  page</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="please-note" style="margin-top: 10px">
                                        <div class="label_note">
                                            <a href="<?=base_url()?>"><button type="button" class="btn btn-success">Back</button></a>
                                        </div>
                                    </div>
                                    <style type="text/css">
                                        .dip_address{
                                            border: 1px solid #ddd;
                                            background: #ddd;
                                            width: 300px;
                                            padding: 5px;
                                        }
                                        .dip_address input{
                                            width: 100%;
                                            border: transparent;
                                            background: transparent;
                                            font-weight: bold;
                                            font-size: 14px;
                                            text-align: center;
                                        }
                                        #copydaddress{
                                            width: 100%;
                                            background: no-repeat;
                                            border: 0;
                                            text-align: center;
                                            font-weight: bold;
                                        }
                                    </style>

                        <?php } elseif ($deposit->method=='stripe')  { ?>
                            <table class="table table-bordered">
                                <tr>
                                    <th><?php echo display("user_id") ?></th>
                                    <td class="text-right"><?php echo $deposit->user_id ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo display("payment_gateway") ?></th>
                                    <td class="text-right"><?php echo $deposit->method ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo display("amount") ?></th>
                                    <td class="text-right"><?php echo $coininfo->pair_with." "; echo $deposit->amount ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo display("fees") ?></th>
                                    <td class="text-right"><?php echo $coininfo->pair_with." "; echo (float)@$deposit->fees_amount ?></td>
                                </tr>
                                <tr>
                                    <th>Total</th>
                                    <td class="text-right"><?php echo $coininfo->pair_with." "; echo (float)@$deposit->amount+(float)@$deposit->fees_amount ?></td>
                                </tr>
                            </table>

                            <?php echo form_open('payment_callback/stripe_confirm', 'method="post" '); ?>
                            <script src="https://checkout.stripe.com/checkout.js" class="stripe-button" data-key="<?php echo $deposit_data['stripe']['publishable_key']; ?>" data-description="<?php echo $deposit_data['description'] ?>" data-amount="<?php $total = $deposit->amount+$deposit->fees_amount; echo round($total*100) ?>" data-locale="auto">
                            </script>
                            <?php echo form_close();?>


                        <?php } elseif ($deposit->method=='phone')  { ?>
                            <table class="table table-bordered">
                                <tr>
                                    <th><?php echo display("user_id") ?></th>
                                    <td class="text-right"><?php echo @$deposit->user_id ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo display("payment_gateway") ?></th>
                                    <td class="text-right"><?php echo @$deposit->method ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo display("amount") ?></th>
                                    <td class="text-right"><?php echo $coininfo->pair_with." "; echo @$deposit->amount ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo display("fees") ?></th>
                                    <td class="text-right"><?php echo $coininfo->pair_with." "; echo @$deposit->fees_amount ?></td>
                                </tr>
                                <tr>
                                    <th>Total</th>
                                    <td class="text-right"><?php echo $coininfo->pair_with." "; echo (float)@$deposit->amount+(float)@$deposit->fees_amount ?></td>
                                </tr>
                            </table>
                            
                            <a class="btn btn-success w-md m-b-5 text-right" href="<?php echo $deposit_data['approval_url'] ?>">Payment Process</a>

                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 