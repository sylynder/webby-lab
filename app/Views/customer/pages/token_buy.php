<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h3>Token Buy</h3>
                    <div class="col-sm-3 col-md-3 pull-right">
                        <a class="btn btn-success w-md m-b-5 pull-right" href="<?php echo base_url("customer/token/token_list") ?>"><i class="fa fa-list" aria-hidden="true"></i> token list</a>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">

        
                        <div class="border_preview">
                            <?php echo form_open('customer/token/token_buy', array('name'=>'token_buy'));?>
                            <div class="form-group row">
                                <label for="coin_qty" class="col-sm-4 col-form-label">Buy Qty<i class="text-danger">*</i></label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="coin_qty" type="text" id="coin_qty" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="rate" class="col-sm-4 col-form-label">Rate</label>
                                <div class="col-sm-8">
                                    <span id="rate"><?php echo @$coin_price->rate==''?(@$coininfo->pair_with.' 0.00'):@$coininfo->pair_with.'&nbsp;'.@$coin_price->rate ?></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="total" class="col-sm-4 col-form-label">Total</label>
                                <div class="col-sm-8" id="total">
                                    <span id="total"><?php echo @$coininfo->pair_with;?> 0.00</span>
                                </div>
                            </div>

                            <div class="row m-b-15">
                                <div class="col-sm-8 col-sm-offset-4">
                                    <button type="submit" class="btn btn-success w-md m-b-5">Buy Token</button>
                                    <a href="<?php echo base_url('customer/home');?>" class="btn btn-danger w-md m-b-5"><?php echo display('cancel')?></a>
                                </div>
                            </div>

                            <?php echo form_close();?>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- /.row -->
<!-- Buy -->
<script type="text/javascript">
    var price = <?php echo @$coin_price->rate==''?'0.00':$coin_price->rate; ?>;
    var symbol = '<?php echo @$coininfo->pair_with; ?>';

    $("#coin_qty").on("keyup", function(event) {
        event.preventDefault();

        var coin_qty = parseFloat($('#coin_qty').val());
        var total = coin_qty*price;

        if(total>0){
            $("#total").html("<span>" + symbol+" "+ total + "</span>");
        }
        else{
            $("#total").html("<span>" + symbol+" 0.00"+ "</span>");
        }

    });
</script>


<script type="text/javascript">
    // $("#coin_qty").on("keyup", function(event) {
    //     event.preventDefault();

    //     var price = parseFloat($('#currency').find(':selected').data('rate'))||0;
    //     var symbol = $('#currency').find(':selected').data('icon')||'';
    //     var coin_qty = parseFloat($('#coin_qty').val())||0;

    //     // console.log(price);
    //     // console.log(symbol);
    //     // console.log(coin_qty);


    //     var total = coin_qty*price;
    //     $("#total").html("<span>" + symbol + total + "</span>");

    // });
    // $("#currency").on("change", function(event) {
    //     event.preventDefault();

    //     var price = parseFloat($('#currency').find(':selected').data('rate'))||0;
    //     var symbol = $('#currency').find(':selected').data('icon')||'';

    //     var coin_qty = parseFloat($('#coin_qty').val())||0;

    //     // console.log(price);
    //     // console.log(symbol);
    //     // console.log(coin_qty);
        
    //     var total = coin_qty*price;
    //     $("#total").html("<span>" + symbol + total + "</span>");

    // });
    var menucontrol = <?php echo $menucontrol->ico_wallet; ?>;
    if(menucontrol==0){
        $('#coin_qty,.m-b-5').prop('disabled',true);
    }


</script>

<!-- <select class="form-control basic-single" name="currency" id="currency">
    <option><?php //echo display('select_option') ?></option>
    <?php //foreach ($currency as $key => $value) { ?>
    <option value="<?php //echo $value->symbol; ?>" data-rate="<?php //echo $value->rate; ?>" data-icon="<?php //echo $value->icon; ?>"><?php //echo $value->name; ?></option>
    <?php //} ?>
</select> -->