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
                <?php echo form_open_multipart("backend/coin_release/form/$coin_release->id") ?>
                <?php echo form_hidden('id', $coin_release->id) ?>
                <?php $date = explode(" ", $coin_release->start_date) ?>

                    <div class="form-group row">
                        <label for="round_name" class="col-sm-4 col-form-label"><?php echo display('round_name')?><i class="text-danger">*</i></label>
                        <div class="col-sm-8">
                            <input name="round_name" value="<?php echo $coin_release->round_name ?>" class="form-control" placeholder="Round Name" type="text" id="round_name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="target" class="col-sm-4 col-form-label"><?php echo display('target')?><i class="text-danger">*</i></label>
                        <div class="col-sm-8">
                            <input name="target" value="<?php echo $coin_release->target ?>" class="form-control" placeholder="10000" type="text" id="target">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="start_date" class="col-sm-4 col-form-label"><?php echo display('release_date')?><i class="text-danger">*</i></label>
                        <div class="col-sm-8">
                            <input name="start_date" value="<?php echo $date[0] ?>" class="form-control"  type="date" id="start_date">
                        </div>
                    </div> 
                    <div class="form-group row">
                        <label for="start_time" class="col-sm-4 col-form-label"><?php echo display('release_time')?><i class="text-danger">*</i></label>
                        <div class="col-sm-8">
                            <input name="start_time" value="<?php echo $date[1] ?>" class="form-control" type="time" id="start_time">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="day" class="col-sm-4 col-form-label"><?php echo display('duration')?>(day)<i class="text-danger">*</i></label>
                        <div class="col-sm-8">
                            <input name="day" value="<?php echo $coin_release->day ?>" class="form-control" placeholder="7" type="number" id="day">
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="status" class="col-sm-4 col-form-label"><?php echo display('status') ?></label>
                        <div class="col-sm-8">
                            <label class="radio-inline">
                                <?php echo form_radio('status', '1', (($coin_release->status==1 || $coin_release->status==null)?true:false)); ?>Active
                             </label>
                            <label class="radio-inline">
                                <?php echo form_radio('status', '0', (($coin_release->status=="0")?true:false) ); ?>Inactive
                             </label> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-9 col-sm-offset-3">
                            <a href="<?php echo base_url('admin'); ?>" class="btn btn-primary  w-md m-b-5"><?php echo display("cancel") ?></a>
                            <button type="submit" class="btn btn-success  w-md m-b-5"><?php echo $coin_release->id?display("update"):display("create") ?></button>
                        </div>
                    </div>
                <?php echo form_close() ?>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
 