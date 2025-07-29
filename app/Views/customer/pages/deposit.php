<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h3><?php echo display('deposit');?></h3>
                    <div class="col-sm-3 col-md-3 pull-right">
                        <a class="btn btn-success w-md m-b-5 pull-right" href="<?php echo base_url("customer/deposit/show") ?>"><i class="fa fa-list" aria-hidden="true"></i> <?php echo display('deposit_list') ?></a>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">

        
                        <div class="border_preview">
                            <?php echo form_open('customer/deposit', array('name'=>'deposit_form', 'id'=>'deposit_form'));?>

                                <div class="form-group row">
                                    <label for="p_name" class="col-sm-5 col-form-label"><?php echo display('deposit_amount');?>(<?php echo $coin_setup->pair_with; ?>)<i class="text-danger">*</i></label>
                                    <div class="col-sm-7">
                                        <input class="form-control" name="deposit_amount" type="text" id="deposit_amount" onkeyup="Fee()" autocomplete="off" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="method" class="col-sm-5 col-form-label"><?php echo display('deposit_method');?><i class="text-danger">*</i></label>
                                    <div class="col-sm-7">
                                        <select class="form-control basic-single" name="method" onchange="Fee()" id="method" required disabled>
                                            <option><?php echo display('deposit_method');?></option>
                                            <?php foreach ($payment_gateway as $key => $value) { ?>
                                            <option value="<?php echo $value->identity; ?>"><?php echo $value->agent; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="changed" class="col-sm-5 col-form-label"></label>
                                    <div class="col-sm-7">
                                        <span id="fee" class="text-success"></span>
                                    </div>
                                </div>

                                <span class="payment_info">
                                <div class="form-group row">
                                    <label for="comment" class="col-sm-5 col-form-label"><?php echo display('comments');?></label>
                                    <div class="col-sm-7">
                                        <textarea class="form-control" name="comment" id="comment" rows="3"></textarea>
                                    </div>
                                </div>
                                </span>

                                <div class="row m-b-15">
                                    <div class="col-sm-7 col-sm-offset-5">
                                        <button type="submit" class="btn btn-success w-md m-b-5"><?php echo display('deposit');?></button>
                                        <a href="<?php echo base_url('customer/home');?>" class="btn btn-danger w-md m-b-5"><?php echo display('cancel')?></a>
                                    </div>
                                </div>

                                <input type="hidden" name="level" value="deposit">
                                <input type="hidden" name="fees" value="">

                                <?php echo form_close();?>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- /.row -->


<script type="text/javascript">
    function Fee(method){
        
        var amount = document.forms['deposit_form'].elements['deposit_amount'].value;
        var method = document.forms['deposit_form'].elements['method'].value;
        var level = document.forms['deposit_form'].elements['level'].value;
        var csrf_test_name = document.forms['deposit_form'].elements['csrf_test_name'].value;

        if (amount!="" || amount==0) {
            $("#method" ).prop("disabled", false);
        }
        if (amount=="" || amount==0) {
            $('#fee').text("Fees is "+0);
        }
        if (amount!="" && method!=""){
            $.ajax({
                'url': '<?php echo base_url("customer/ajaxload/fees_load");?>',
                'type': 'POST', //the way you want to send data to your URL
                'data': {'method': method,'level':level,'amount':amount,'csrf_test_name':csrf_test_name },
                'dataType': "JSON",
                'success': function(data) { 
                    if(data){
                        $('[name="amount"]').val(data.amount);
                        $('[name="fees"]').val(data.fees);
                        $('#fee').text("Fees is "+data.fees);                    
                    } else {
                        alert('Error!');
                    }  
                }
            });
        } 
    }
</script>
<?php  $gateway = $this->db->select('*')->from('payment_gateway')->where('identity', 'phone')->where('status',1)->get()->row(); ?>
<!-- Ajax Payable -->
<script type="text/javascript">
    $(function(){
        $("#method").on("change", function(event) {
            event.preventDefault();
            var method = $("#method").val()|| 0;

            if (method=='phone') {
                $( ".payment_info").html("<div class='form-group row'><label for='send_money' class='col-sm-5 col-form-label'>Send Money</label><div class='col-sm-7'><h2><a href='tel:<?=@$gateway->public_key?>'><?=@$gateway->public_key?></a></h2></div></div><div class='form-group row'><label for='om_name' class='col-sm-5 col-form-label'><?php echo "Mobile Money Name" ?></label><div class='col-sm-7'><input name='om_name' class='form-control om_name' type='text' id='om_name' autocomplete='off'></div></div><div class='form-group row'><label for='om_mobile' class='col-sm-5 col-form-label'><?php echo "Mobile Money Number" ?></label><div class='col-sm-7'><input name='om_mobile' class='form-control om_mobile' type='text' id='om_mobile' autocomplete='off'></div></div><div class='form-group row'><label for='transaction_no' class='col-sm-5 col-form-label'><?php echo "Transaction ID" ?></label><div class='col-sm-7'><input name='transaction_no' class='form-control transaction_no' type='text' id='transaction_no' autocomplete='off'></div></div><div class='form-group row'><label for='idcard_no' class='col-sm-5 col-form-label'><?php echo display("idcard_no") ?></label><div class='col-sm-7'><input name='idcard_no' class='form-control idcard_no' type='text' id='idcard_no' autocomplete='off'></div></div>");
            }
            else{
                $( ".payment_info").html("<div class='form-group row'><label for='comments' class='col-sm-5 col-form-label'><?php echo display("comments") ?></label><div class='col-sm-7'><textarea name='comments' class='form-control editor' placeholder='' type='text' id='comments'></textarea></div></div>");
            }

        });

    }); 
</script>