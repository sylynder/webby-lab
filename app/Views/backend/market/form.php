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
                <?php echo form_open_multipart("backend/market/form/$market->id") ?>
                <?php echo form_hidden('id', $market->id) ?> 

                    <div class="form-group row">
                        <label for="name" class="col-sm-4 col-form-label"><?php echo display('name') ?><i class="text-danger">*</i></label>
                        <div class="col-sm-8">
                            <input name="name" value="<?php echo $market->name ?>" class="form-control" placeholder="<?php echo display('name') ?>" type="text" id="name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="full_name" class="col-sm-4 col-form-label">Full Name</label>
                        <div class="col-sm-8">
                            <input name="full_name" value="<?php echo $market->full_name ?>" class="form-control" placeholder="<?php echo display('full_name') ?>" type="text" id="full_name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="symbol" class="col-sm-4 col-form-label"><?php echo display('symbol') ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-8">
                            <select class="form-control basic-single" name="symbol">
                                <option><?php echo display('select_option') ?></option>
                                <?php foreach ($coins as $key => $value) { ?>
                                    <option value="<?php echo $value->symbol; ?>" <?php echo ($market->symbol==$value->symbol)?'Selected':'' ?>><?php echo $value->symbol; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="status" class="col-sm-4 col-form-label"><?php echo display('status') ?></label>
                        <div class="col-sm-8">
                            <label class="radio-inline">
                                <?php echo form_radio('status', '1', (($market->status==1 || $market->status==null)?true:false)); ?>Active
                             </label>
                            <label class="radio-inline">
                                <?php echo form_radio('status', '0', (($market->status=="0")?true:false) ); ?>Inactive
                             </label> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-9 col-sm-offset-3">
                            <a href="<?php echo base_url('admin'); ?>" class="btn btn-primary  w-md m-b-5"><?php echo display("cancel") ?></a>
                            <button type="submit" class="btn btn-success  w-md m-b-5"><?php echo $market->id?display("update"):display("create") ?></button>
                        </div>
                    </div>
                <?php echo form_close() ?>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

 