<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h2><?php echo (!empty($title)?$title:null) ?></h2>
                </div>
            </div>
            <div class="panel-body">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="border_preview">
                <?php echo form_open_multipart("backend/coin_manager/index/".@$coin_manager->id) ?>
                <?php echo form_hidden('id', @$coin_manager->id) ?> 

                    <div class="form-group row">
                        <label for="total_coin" class="col-sm-4 col-form-label"><?php echo display('total_coin')?></label>
                        <div class="col-sm-8">
                            <input name="total_coin" value="0" class="form-control" type="text" id="total_coin" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="hold_coin" class="col-sm-4 col-form-label"><?php echo display('hold_coin')?><i class="text-danger">*</i></label>
                        <div class="col-sm-8">
                            <input name="hold_coin" value="<?php echo @$coin_manager->hold_coin ?>" class="form-control coin" placeholder="15000" type="text" id="hold_coin">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="pre_sell" class="col-sm-4 col-form-label"><?php echo display('pre_sell')?><i class="text-danger">*</i></label>
                        <div class="col-sm-8">
                            <input name="pre_sell" value="<?php echo @$coin_manager->pre_sell ?>" class="form-control coin" placeholder="15000" type="text" id="pre_sell">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="sell_available" class="col-sm-4 col-form-label"><?php echo display('sell_available')?><i class="text-danger">*</i></label>
                        <div class="col-sm-8">
                            <input name="sell_available" value="<?php echo @$coin_manager->sell_available ?>" class="form-control coin" placeholder="15000" type="text" id="sell_available">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-9 col-sm-offset-3">
                            <a href="<?php echo base_url('admin'); ?>" class="btn btn-primary  w-md m-b-5"><?php echo display("cancel") ?></a>
                            <button type="submit" class="btn btn-success  w-md m-b-5"><?php echo @$coin_manager->id?display("update"):display("create") ?></button>
                        </div>
                    </div>
                <?php echo form_close() ?>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

    var sum = 0;
    $('.coin').each(function(){
        sum += Number($(this).val());
    });
    $('#total_coin').val(sum);
    
    $(document).on('change','.coin',function(){
        var sum = 0;
        $('.coin').each(function(){
            sum += Number($(this).val());
        });
        $('#total_coin').val(sum);
    });

</script>