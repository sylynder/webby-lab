<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h2><?php echo (!empty($title)?$title:null) ?></h2>
                </div>
            </div>
            <div class="panel-body">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-8">
                <div class="border_preview">
                <?php echo form_open_multipart("backend/affiliation") ?>
                <?php echo form_hidden('id', @$affiliation->id) ?> 

                    <div class="form-group row">
                        <label for="commission" class="col-sm-4 col-form-label">Commission<i class="text-danger">*</i></label>
                        <div class="col-sm-8">
                            <input name="commission" value="<?php echo @$affiliation->commission ?>" class="form-control" placeholder="<?php echo display('commission') ?>" type="text" id="commission">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="type" class="col-sm-4 col-form-label">Type<i class="text-danger">*</i></label>
                        <div class="col-sm-8">
                            <label class="radio-inline">
                                <?php echo form_radio('type', 'PERCENT', ((@$affiliation->type=='PERCENT' || @$affiliation->type==null)?true:false)); ?>Percent
                             </label>
                            <label class="radio-inline">
                                <?php echo form_radio('type', 'FIXED', ((@$affiliation->type=='FIXED')?true:false) ); ?>Fixed
                             </label> 
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="status" class="col-sm-4 col-form-label"><?php echo display('status') ?><i class="text-danger">*</i></label>
                        <div class="col-sm-8">
                            <label class="radio-inline">
                                <?php echo form_radio('status', '1', ((@$affiliation->status==1 || @$affiliation->status==null)?true:false)); ?>Active
                             </label>
                            <label class="radio-inline">
                                <?php echo form_radio('status', '0', ((@$affiliation->status=="0")?true:false) ); ?>Inactive
                             </label> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-9 col-sm-offset-3">
                            <a href="<?php echo base_url('admin'); ?>" class="btn btn-primary  w-md m-b-5"><?php echo display("cancel") ?></a>
                            <button type="submit" class="btn btn-success  w-md m-b-5"><?php echo @$affiliation->id?display("update"):display("create") ?></button>
                        </div>
                    </div>
                <?php echo form_close() ?>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

 