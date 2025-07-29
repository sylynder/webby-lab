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
                <?php echo form_open_multipart("backend/currency/form/$currency->id") ?>
                <?php echo form_hidden('id', $currency->id) ?>
                
                    <div class="form-group row">
                        <label for="name" class="col-sm-4 col-form-label">Name<i class="text-danger">*</i></label>
                        <div class="col-sm-8">
                            <input name="name" value="<?php echo $currency->name ?>" class="form-control" placeholder="Coin Name" type="text" id="name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="icon" class="col-sm-4 col-form-label">Symbol<i class="text-danger">*</i></label>
                        <div class="col-sm-8">
                            <input name="icon" value="<?php echo $currency->icon ?>" class="form-control"  type="text" id="icon" value="<?php echo $currency->icon ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="symbol" class="col-sm-4 col-form-label">ISO Code<i class="text-danger">*</i></label>
                        <div class="col-sm-8">
                            <input name="symbol" value="<?php echo $currency->symbol ?>" class="form-control" placeholder="symbol" type="text" id="symbol">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="rate" class="col-sm-4 col-form-label">Rate<i class="text-danger">*</i></label>
                        <div class="col-sm-8">
                            <input name="rate" value="<?php echo $currency->rate ?>" class="form-control" placeholder="rate" type="text" id="rate">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="status" class="col-sm-4 col-form-label"><?php echo display('status') ?></label>
                        <div class="col-sm-8">
                            <label class="radio-inline">
                                <?php echo form_radio('status', '1', (($currency->status==1 || $currency->status==null)?true:false)); ?>Active
                             </label>
                            <label class="radio-inline">
                                <?php echo form_radio('status', '0', (($currency->status=="0")?true:false) ); ?>Inactive
                             </label> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-9 col-sm-offset-3">
                            <a href="<?php echo base_url('admin'); ?>" class="btn btn-primary  w-md m-b-5"><?php echo display("cancel") ?></a>
                            <button type="submit" class="btn btn-success  w-md m-b-5"><?php echo $currency->id?display("update"):display("create") ?></button>
                        </div>
                    </div>
                <?php echo form_close() ?>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

 