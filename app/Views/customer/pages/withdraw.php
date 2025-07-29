<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
        <div class="panel panel-bd">
            <div class="panel-heading ui-sortable-handle">
                <div class="panel-title">
                    <h4 style="font-size: 19px"><?php echo display('withdraw');?></h4>
                    <div class="col-sm-3 col-md-3 pull-right">
                        <a class="btn btn-success w-md m-b-5 pull-right" href="<?php echo base_url("customer/withdraw/withdraw_list") ?>"><i class="fa fa-list" aria-hidden="true"></i> <?php echo display('withdraw_list') ?></a>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
                        <?php echo form_open('customer/withdraw',array('name'=>'withdraw','id'=>'withdraw'));?>

                            <div class="form-group row">
                                <label for="amount" class="col-sm-4 col-form-label"><?php echo display('amount');?>(<?php echo $coininfo->pair_with; ?>)<i class="text-danger">*</i></label>
                                <div class="col-sm-8">
                                    <input class="form-control" onkeyup="Fee()" name="amount" type="text" id="amount" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="changed" class="col-sm-4 col-form-label"></label>
                                <div class="col-sm-8">
                                    <span id="fee" class="text-success"></span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="p_name" class="col-sm-4 col-form-label"><?php echo display('payment_method');?><i class="text-danger">*</i></label>
                                <div class="col-sm-8">
                                    <select class="form-control basic-single" name="method" id="payment_method" onchange="WalletId(this.value)">
                                        <option><?php echo display('payment_method')?></option>
                                        <?php foreach ($payment_gateway as $key => $value) {  ?>
                                        <option value="<?php echo $value->identity; ?>"><?php echo $value->agent; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div id="coinwallet" class="form-group row"></div>

                            <div class="form-group row">
                                <label for="changed" class="col-sm-4 col-form-label"></label>
                                <div class="col-sm-8">
                                    <span id="walletidis" class="text-success"></span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label"><?php echo display('otp_send_to')?></label>
                                <div class="col-sm-8">
                                    <div class="radio radio-info radio-inline">
                                        <input type="radio" id="inlineRadio1" value="1" name="varify_media" checked="">
                                        <label for="inlineRadio1"> <?php echo display('sms')?> </label>
                                    </div>
                                    <div class="radio radio-inline">
                                        <input type="radio" id="inlineRadio2" value="2" name="varify_media">
                                        <label for="inlineRadio2"> <?php echo display('email')?> </label>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="walletid" value="">

                            <div class="row m-b-15">
                                <div class="col-sm-8 col-sm-offset-4">
                                    <button type="submit" disabled class="btn btn-success w-md m-b-5"><?php echo display('withdraw');?></button>
                                    <a href="<?php echo base_url('customer/home');?>" class="btn btn-danger w-md m-b-5"><?php echo display('cancel')?></a>
                                </div>
                            </div>

                            <input type="hidden" name="level" value="withdraw">

                        <?php echo form_close();?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    function Fee(){
        
        var amount = document.forms['withdraw'].elements['amount'].value;
        var level = document.forms['withdraw'].elements['level'].value;
        var csrf_test_name = document.forms['withdraw'].elements['csrf_test_name'].value;

        if (amount=="" || amount==0) {
            $('#fee').text("Fees is "+0);
        }
        if (amount!=""){
            $.ajax({
                'url': '<?php echo base_url("customer/ajaxload/fees_load");?>',
                'type': 'POST', //the way you want to send data to your URL
                'data': {'level':level,'amount':amount,'csrf_test_name':csrf_test_name },
                'dataType': "JSON",
                'success': function(data) { 
                    if(data){
                        $('#fee').text("Fees is "+data.fees);                    
                    } else {
                        alert('Error!');
                    }  
                }
            });
        } 
    }

</script>

<script type="text/javascript">
    function WalletId(method){
        
        var csrf_test_name = document.forms['withdraw'].elements['csrf_test_name'].value;

        if (method=='phone') { method = 'phone'; }

        $.ajax({
            url: '<?php echo base_url("customer/ajaxload/walletid"); ?>',
            type: 'POST', //the way you want to send data to your URL
            data: {'method': method,'csrf_test_name':csrf_test_name },
            dataType:'JSON',
            success: function(data) { 
               
                if(data){

                    $('[name="walletid"]').val(data.wallet_id);
                    $('button[type=submit]').prop('disabled', false);
                    $('#walletidis').text('Your Wallet Id Is '+data.wallet_id);
                    $('#coinwallet').html("");
                
                } else {

                    if(method=='coinpayment'){
                        $('button[type=submit]').prop('disabled', false);
                        $('#coinwallet').html("<label class='col-sm-4 col-form-label' for='amount'>Your Address<i class='text-danger'>*</i></label><div class='col-sm-8'><input class='form-control' name='walet_address' type='text' id='walet_address' required></div>");
                        $('#walletidis').text('');

                    }else{
                        $('#coinwallet').html("");
                        $('button[type=submit]').prop('disabled', true);
                        $('#walletidis').text('Your have no withdrawal account');
                    }
                }  
            }
        });
    }
</script>